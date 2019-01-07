<?php

namespace zikwall\encore\modules\core\components\managers;

use Yii;
use yii\base\Exception;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use zikwall\encore\modules\core\components\bootstrap\ModuleAutoLoader;
use zikwall\encore\modules\core\components\console\Application;
use zikwall\encore\modules\core\components\Module;
use zikwall\encore\modules\core\models\ModuleEnabled;

class ModuleManager extends \yii\base\Component
{

    /**
     * Создание резервной копии при удалении папки модуля
     *
     * @var boolean
     */
    public $createBackup = true;

    /**
     * Список всех модулей, также содержит установленные, но не включенные модули.
     *
     * @var array
     */
    protected $modules;

    /**
     * Список всех активированных модулей
     *
     * @var array module id's
     */
    public $enabledModules = [];

    /**
     * Список основных классов модулей.
     *
     * @var array the core module class names
     */
    public $coreModules = [];

    /**
     * Инициализация
     *
     * Загружает все включенные модули из базы данных
     */
    public function init()
    {
        parent::init();

        // Любая база данных установлена и не установлена в установленном состоянии
        if (!Yii::$app->params['databaseInstalled'] && !Yii::$app->params['installed']) {
            return;
        }

        if (Yii::$app instanceof Application && !Yii::$app->isDatabaseInstalled()) {
            $this->enabledModules = [];
        } else {
            $this->enabledModules = \zikwall\encore\modules\core\models\ModuleEnabled::getEnabledIds();
        }
    }

    /**
     * Регистрирует модуль для менеджера
     *
     * @throws Exception
     */
    public function registerBulk(array $configs)
    {
        foreach ($configs as $basePath => $config) {
            $this->register($basePath, $config);
        }
    }

    /**
     * Метод регистрирует модуль
     *
     * @param string $basePath the modules base path
     * @param array $config the module configuration (config.php)
     * @throws InvalidConfigException
     */
    public function register(string $basePath, array $config = null)
    {
        if ($config === null && is_file($basePath . '/config.php')) {
            $config = require($basePath . '/config.php');
        }

        // Проверка обязательных параметров конфигурации
        if (!isset($config['class']) || !isset($config['id'])) {
            throw new InvalidConfigException("Module configuration requires an id and class attribute!");
        }

        $isCoreModule = (isset($config['isCoreModule']) && $config['isCoreModule']);
        $isInstallerModule = (isset($config['isInstallerModule']) && $config['isInstallerModule']);

        $this->modules[$config['id']] = $config['class'];

        if (isset($config['namespace'])) {
            Yii::setAlias('@' . str_replace('\\', '/', $config['namespace']), $basePath);
        }

        Yii::setAlias('@' . $config['id'], $basePath);
        if (isset($config['aliases']) && is_array($config['aliases'])) {
            foreach ($config['aliases'] as $name => $value) {
                Yii::setAlias($name, $value);
            }
        }

        if (!Yii::$app->params['installed'] && $isInstallerModule) {
            $this->enabledModules[] = $config['id'];
        }

        // Не включено и не является модулем ядра
        if (!$isCoreModule && !in_array($config['id'], $this->enabledModules)) {
            return;
        }

        // Обработка вложенных модулей
        if (!isset($config['modules'])) {
            $config['modules'] = [];
        }

        if ($isCoreModule) {
            $this->coreModules[] = $config['class'];
        }

        // Добавить правила URL
        if (isset($config['urlManagerRules'])) {
            Yii::$app->urlManager->addRules($config['urlManagerRules'], false);
        }

        $moduleConfig = [
            'class' => $config['class'],
            'modules' => $config['modules']
        ];

        // Добавить значения файла конфигурации в модуль
        if (isset(Yii::$app->modules[$config['id']]) && is_array(Yii::$app->modules[$config['id']])) {
            $moduleConfig = \yii\helpers\ArrayHelper::merge($moduleConfig, Yii::$app->modules[$config['id']]);
        }

        // Зарегистрировать модуль Yii
        Yii::$app->setModule($config['id'], $moduleConfig);

        // Регистрация обработчиков событий
        if (isset($config['events'])) {
            foreach ($config['events'] as $event) {
                if (isset($event['class'])) {
                    Event::on($event['class'], $event['event'], $event['callback']);
                } else {
                    Event::on($event[0], $event[1], $event[2]);
                }
            }
        }

        // регистрация вложенных модулей
        if(isset($config['modules'])){
            $this->registerSubModules($config['id'], $config['modules']);
        }
    }

    public function registerSubModules($parent, $modules)
    {
        $subModules = [];

        foreach ($modules as $module => $subModuleId) {
            $subModule = Yii::$app->getModule($parent)->getModule($module);
            $subModuleBasePath = $subModule->getBasePath();

            if(isset($subModuleId['class']) && is_file( $subModuleBasePath . DIRECTORY_SEPARATOR . 'config.php')){
                if (is_dir($subModuleBasePath)) {
                    try {
                        $subModules[$subModuleBasePath] = require($subModuleBasePath . DIRECTORY_SEPARATOR . 'config.php');
                    } catch (\Exception $ex) {
                        Yii::error($ex);
                    }
                }
            }
        }

        //modules recursion registered, only core modules
        Yii::$app->moduleManager->registerBulk($subModules);
    }

    /**
     * Возвращает все модули (также отключенные модули).
     *
     * Примечание. Возвращаются только модули, которые расширяют ..\core\components\Module.
     *
     * @param array $options options (name => config)
     * Доступны следующие параметры:
     *
     * - includeCoreModules: boolean, возвращает также основные модули (по умолчанию: false)
     * - returnClass: boolean, возвращает имя класса вместо объекта модуля (по умолчанию: false)
     *
     * @return array
     */
    public function getModules($options = [])
    {
        $modules = [];

        foreach ($this->modules as $id => $class) {

            // Пропустить модули ядра
            if (!isset($options['includeCoreModules']) || $options['includeCoreModules'] === false) {
                if (in_array($class, $this->coreModules)) {
                    continue;
                }
            }

            if (isset($options['returnClass']) && $options['returnClass']) {
                $modules[$id] = $class;
            } else {
                $module = $this->getModule($id);
                if ($module instanceof Module) {
                    $modules[$id] = $module;
                }
            }
        }

        return $modules;
    }

    public function getCoreModules($options = [])
    {
        $coreModules = [];

        foreach ($this->modules as $id => $class) {

            if (in_array($class, $this->coreModules)) {
                if (isset($options['returnClass']) && $options['returnClass']) {
                    $modules[$id] = $class;
                } else {
                    $module = $this->getModule($id);
                    if ($module instanceof Module) {
                        $coreModules[$id] = $module;
                    }
                }
            }
        }

        return $coreModules;
    }

    /**
     * Проверяет, существует ли moduleId, независимо от того, активирован он или нет
     */
    public function hasModule(string $id) : bool
    {
        return (array_key_exists($id, $this->modules));
    }

    /**
     * Возвращает экземпляр модуля по идентификатору
     */
    public function getModule(string $id) : \yii\base\Module
    {
        // Активация модуля
        if (Yii::$app->hasModule($id)) {
            return Yii::$app->getModule($id, true);
        }

        // Деактивация модуля
        if (isset($this->modules[$id])) {
            $class = $this->modules[$id];
            return Yii::createObject($class, [$id, Yii::$app]);
        }

        throw new Exception("Could not find/load requested module: " . $id);
    }

    /**
     * Кэш диспетчера модулей
     */
    public function flushCache()
    {
        Yii::$app->cache->delete(ModuleAutoLoader::CACHE_ID);
    }

    /**
     * Проверяет, может ли модуль быть удаленным
     */
    public function canRemoveModule(string $moduleId)
    {
        $module = $this->getModule($moduleId);

        if ($module === null) {
            return false;
        }

        // Проверка находится в папке пользовательских модулей
        if (strpos($module->getBasePath(), Yii::getAlias(Yii::$app->params['moduleCustomPath'])) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Удаляет модуль
     */
    public function removeModule(string $moduleId, bool $disableBeforeRemove = true)
    {
        $module = $this->getModule($moduleId);

        if ($module == null) {
            throw new Exception("Could not load module to remove!");
        }

        /**
         * Деактивация модуля
         */
        if ($disableBeforeRemove && Yii::$app->hasModule($moduleId)) {
            $module->disable();
        }

        /**
         * Удаление папки
         */
        if ($this->createBackup) {
            $moduleBackupFolder = Yii::getAlias("@runtime/module_backups");
            if (!is_dir($moduleBackupFolder)) {
                if (!@mkdir($moduleBackupFolder)) {
                    throw new Exception("Could not create module backup folder!");
                }
            }

            $backupFolderName = $moduleBackupFolder . DIRECTORY_SEPARATOR . $moduleId . "_" . time();
            $moduleBasePath = $module->getBasePath();
            FileHelper::copyDirectory($moduleBasePath, $backupFolderName);
            FileHelper::removeDirectory($moduleBasePath);
        } else {
            //TODO: Delete directory
        }

        $this->flushCache();
    }

    /**
     * Активация модуля
     *
     * @param \zikwall\encore\modules\core\components\Module $module
     */
    public function enable(Module $module)
    {
        $moduleEnabled = ModuleEnabled::findOne(['module_id' => $module->id]);
        if ($moduleEnabled == null) {
            $moduleEnabled = new ModuleEnabled();
            $moduleEnabled->module_id = $module->id;
            $moduleEnabled->save();
        }

        $this->enabledModules[] = $module->id;
        $this->register($module->getBasePath());
    }

    public function enableModules($modules = [])
    {
        foreach ($modules as $module) {
            $module = ($module instanceof Module) ? $module : $this->getModule($module);
            if ($module != null) {
                $module->enable();
            }
        }
    }

    /**
     * Деактивацяи модуля
     *
     * @param \zikwall\encore\modules\core\components\Module $module
     */
    public function disable(Module $module)
    {
        $moduleEnabled = ModuleEnabled::findOne(['module_id' => $module->id]);
        if ($moduleEnabled != null) {
            $moduleEnabled->delete();
        }

        if (($key = array_search($module->id, $this->enabledModules)) !== false) {
            unset($this->enabledModules[$key]);
        }

        Yii::$app->setModule($module->id, 'null');
    }


    /**
     * Пакетная деактивацяи модулей
     */
    public function disableModules(array $modules = [])
    {
        foreach ($modules as $module) {
            $module = ($module instanceof Module) ? $module : $this->getModule($module);
            if($module != null) {
                $module->disable();
            }
        }
    }

}

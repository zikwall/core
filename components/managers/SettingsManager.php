<?php

namespace zikwall\encore\modules\core\components\managers;

use Yii;
use zikwall\encore\modules\core\libs\BaseSettingsManager;
use zikwall\encore\modules\content\components\ContentContainerActiveRecord;
use zikwall\encore\modules\content\components\ContentContainerSettingsManager;
use zikwall\encore\modules\user\models\User;

class SettingsManager extends BaseSettingsManager
{
    /**
     * @var ContentContainerSettingsManager[] уже загруженные настройки контейнеров содержимого
     */
    protected $contentContainers = [];

    /**
     * Возвращает контейнер содержимого
     */
    public function contentContainer(ContentContainerActiveRecord $container) : ContentContainerSettingsManager
    {
        if (isset($this->contentContainers[$container->contentcontainer_id])) {
            return $this->contentContainers[$container->contentcontainer_id];
        }
        $this->contentContainers[$container->contentcontainer_id] = new ContentContainerSettingsManager([
            'moduleId' => $this->moduleId,
            'contentContainer' => $container,
        ]);
        return $this->contentContainers[$container->contentcontainer_id];
    }

    /**
     * Возвращает ContentContainerSettingsManager для данного пользователя или текущего зарегистрированного пользователя
     */
    public function user(User $user = null) : ContentContainerSettingsManager
    {
        if(!$user) {
            $user = Yii::$app->user->getIdentity();
        }
        return $this->contentContainer($user);
    }

    /**
     * Указывает, что этот параметр зафиксирован в файле конфигурации и не может быть изменен во время работы приложения.
     */
    public function isFixed(string $name) : bool
    {
        return isset(Yii::$app->params['fixed-settings'][$this->moduleId][$name]);
    }

    /**
     * @inheritdoc
     */
    public function get($name, $default = null)
    {
        if ($this->isFixed($name)) {
            return Yii::$app->params['fixed-settings'][$this->moduleId][$name];
        }

        return parent::get($name, $default);
    }

}

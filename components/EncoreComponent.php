<?php

namespace zikwall\encore\modules\core\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

class EncoreComponent extends Component
{
    /**
     * @var Module
     */
    public $module;

    public function __construct($module, $config = [])
    {
        parent::__construct($config);
        $this->module = $module;
    }

    public function registerComponents()
    {
        $this->registerAuthorization();
    }

    public function registerConsoleConmonents()
    {
        $this->registerAuthorization();
    }

    public function getComponent($name)
    {
        $configurationName = $name . 'Component';
        if (is_string($this->module->$configurationName)) {
            return Yii::$app->get($this->module->$configurationName);
        }
        return $this->module->get('encore_' . $name);
    }

    /**
     * Registers user authorization.
     */
    public function registerAuthorization()
    {
        if ($this->module->rbacComponent !== true
            && !is_string($this->module->rbacComponent)
            && !is_array($this->module->rbacComponent)) {
            throw new InvalidConfigException('Invalid value for the rbacComponent parameter.');
        }
        if (is_string($this->module->rbacComponent)) {
            return;
        }
        $this->module->set('encore_rbac', is_array($this->module->rbacComponent)
            ? $this->module->rbacComponent
            : [
                'class' => 'yii\rbac\DbManager',
                'db' => $this->module->db,
                'itemTable' => '{{%encore_auth_item}}',
                'itemChildTable' => '{{%encore_auth_item_child}}',
                'assignmentTable' => '{{%encore_auth_assignment}}',
                'ruleTable' => '{{%encore_auth_rule}}',
                'cache' => $this->module->cache
            ]);
    }
}
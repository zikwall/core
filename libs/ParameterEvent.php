<?php

namespace zikwall\encore\modules\core\libs;

class ParameterEvent extends \yii\base\Event
{
    public $parameters;

    /**
     * @inheritdoc
     */
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
        $this->init();
    }
}
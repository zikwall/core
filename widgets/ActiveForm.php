<?php

namespace zikwall\encore\modules\core\widgets;

/**
 * ActiveForm
 */
class ActiveForm extends \yii\bootstrap\ActiveForm
{

    /**
     * @inheritdoc
     */
    public $enableClientValidation = false;

    /**
     * @inheritdoc
     */
    public $fieldClass = 'zikwall\encore\modules\core\widgets\ActiveField';

}

<?php

namespace zikwall\encore\modules\core\components\web;

use zikwall\encore\modules\core\models\Setting;
use Yii;

/**
 * @inheritdoc
 *
 */
class Request extends \yii\web\Request
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->cookieValidationKey == '') {
            $this->cookieValidationKey = 'enCore successfully--istaller--generated___crfs__p-arams_v_1.0.0';
        }
    }

}

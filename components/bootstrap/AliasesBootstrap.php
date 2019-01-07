<?php

namespace zikwall\encore\modules\core\components\bootstrap;

use yii\base\Component;
use Yii;

class AliasesBootstrap extends Component
{
    public function init()
    {
        Yii::setAlias('@zikwall', '@vendor/zikwall');
        Yii::setAlias('@encore', '@zikwall/encore');

        if (Yii::getAlias('@web-static', false) === false) {
            Yii::setAlias('@web-static', '@web/static');
        }

        if (Yii::getAlias('@webroot-static', false) === false) {
            Yii::setAlias('@webroot-static', '@webroot/static');
        }
    }
}
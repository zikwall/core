<?php

namespace zikwall\encore\modules\core\assets;

use yii\web\AssetBundle;
use zikwall\encore\modules\admin\assets\AdminAsset;

class EncoreWithoutBootstrapAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    public $depends = [
        'zikwall\encore\modules\core\assets\EncoreWithoutBootstrapCore',
        'zikwall\encore\modules\core\assets\jquery\JqueryWidgetAsset',
        'zikwall\encore\modules\core\assets\jquery\JqueryTimeAgoAsset',
        'zikwall\encore\modules\content\assets\ContentContainerAsset',
        'zikwall\encore\modules\admin\assets\AdminAsset',
        'zikwall\encore\modules\user\assets\UserAsset',
        'zikwall\encore\modules\user\assets\UserPickerAsset',
        'zikwall\encore\modules\user\assets\User',
    ];
}
<?php

namespace zikwall\encore\modules\core\assets;

use yii\web\AssetBundle;

class EncoreApplicationAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    public $depends = [
        /*'zikwall\encore\modules\core\assets\AnimateCssAsset',*/
        'zikwall\encore\modules\core\assets\EncoreCoreAssetBundle',
        'zikwall\encore\modules\core\assets\jquery\JqueryTimeAgoAsset',
        'zikwall\encore\modules\content\assets\ContentContainerAsset',
        'zikwall\encore\modules\user\assets\UserAsset',
    ];
}
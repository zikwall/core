<?php

namespace zikwall\encore\modules\core\assets\jquery;

use yii\web\AssetBundle;

class JqueryWidgetAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/jquery-ui';

    /**
     * @inheritdoc
     */
    public $js = ['ui/minified/widget.min.js'];

}

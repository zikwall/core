<?php

namespace zikwall\encore\modules\core\modules\developer\controllers;

use Yii;
use zikwall\encore\modules\core\components\base\Controller;

class IndexController extends Controller
{
    public $subLayout = "@zikwall/encore/modules/core/modules/developer/views/index/_layout";

    public function actionIndex()
    {
        return $this->render('index');
    }
}

?>

<?php

namespace zikwall\encore\modules\core\controllers;

use zikwall\encore\modules\core\components\extended\FrontendController;

class DefaultController extends FrontendController
{
    public function init()
    {
        $this->appendPageTitle('Home');
        parent::init();
    }

    public function actionHome()
    {
        return $this->render('home');
    }

}

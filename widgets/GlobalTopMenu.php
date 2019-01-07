<?php

namespace zikwall\encore\modules\core\widgets;

use Yii;
use zikwall\encore\modules\user\components\User;

class GlobalTopMenu extends BaseMenu
{

    /**
     * @inheritdoc
     */
    public $template = "topNavigation";

    /**
     * @inheritdoc
     */
    public $id = 'top-menu-nav';

    /**
     * Минималистические иконки меню (опционально указываются в шаблоне)
     */
    public $mini = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if($this->mini){
            $this->template = 'topNavigationMini';
        }

        // Отключаем меню для гостей
        if (Yii::$app->user->isGuest && !User::isGuestAccessEnabled()) {
            $this->template = '';
        }
    }

}

?>

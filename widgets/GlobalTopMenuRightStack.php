<?php

namespace zikwall\encore\modules\core\widgets;

use Yii;
use zikwall\encore\modules\user\components\User;

class GlobalTopMenuRightStack extends BaseStack
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        // Не показывать стек, если гостевой доступ отключен, и пользователь не зашел в систему
        if (Yii::$app->user->isGuest && !User::isGuestAccessEnabled()) {
            return;
        }

        return parent::run();
    }

}

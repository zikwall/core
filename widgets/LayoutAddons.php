<?php

namespace zikwall\encore\modules\core\widgets;

use Yii;
use yii\widgets\Pjax;

/**
 * LayoutAddons вставляются в конце всех макетов (стандарт или логин).
 */
class LayoutAddons extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        if(!Yii::$app->request->isPjax) {
            $this->addWidget(GlobalModal::className());
            $this->addWidget(GlobalConfirmModal::className());

            $this->addWidget(LoaderWidget::className(), ['show' => false, 'id' => "encore-ui-loader-default"]);

            if (Yii::$app->params['enablePjax']) {
                $this->addWidget(Pjax::className());
            }
        }
        parent::init();
    }

}

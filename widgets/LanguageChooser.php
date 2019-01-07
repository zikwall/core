<?php

namespace zikwall\encore\modules\core\widgets;

use Yii;
use zikwall\encore\modules\core\models\forms\ChooseLanguage;

class LanguageChooser extends \yii\base\Widget
{

    public function run()
    {
        $model = new ChooseLanguage();
        $model->language = Yii::$app->language;
        return $this->render('languageChooser', ['model' => $model, 'languages' => Yii::$app->i18n->getAllowedLanguages()]);
    }

}

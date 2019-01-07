<?php

namespace zikwall\encore\modules\core\widgets;

use zikwall\encore\modules\core\helpers\TestHelper;

class PrerequisitesList extends \yii\base\Widget
{

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('prerequisitesList', ['checks' => TestHelper::getResults()]);
    }

    /**
     * Check there is an error
     *
     * @return boolean
     */
    public static function hasError()
    {
        foreach (TestHelper::getResults() as $check) {
            if ($check['state'] == 'ERROR') {
                return true;
            }
        }

        return false;
    }

}

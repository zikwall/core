<?php

namespace zikwall\encore\modules\core\components\managers;

use yii\helpers\FileHelper;

class AssetManager extends \yii\web\AssetManager
{
    public function clear()
    {
        if ($this->basePath == '') {
            return;
        }

        foreach (scandir($this->basePath) as $file) {
            if (substr($file, 0, 1) === '.') {
                continue;
            }
            FileHelper::removeDirectory($this->basePath . DIRECTORY_SEPARATOR . $file);
        }
    }

}

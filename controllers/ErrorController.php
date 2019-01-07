<?php

namespace zikwall\encore\modules\core\controllers;

use Yii;
use yii\web\HttpException;
use yii\base\UserException;
use zikwall\encore\modules\core\components\extended\FrontendController;

class ErrorController extends FrontendController
{
    /**
     * This is the action to handle external exceptions.
     */
    public function actionIndex()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            return '';
        }

        if ($exception instanceof UserException || $exception instanceof HttpException) {
            $message = $exception->getMessage();
        } else {
            $message = Yii::t('error', 'An internal server error occurred.');
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';
            return [
                'error' => true,
                'message' => $message
            ];
        }

        /**
         * Показать специальный вход для гостей
         */
        if (Yii::$app->user->isGuest && $exception instanceof HttpException && $exception->statusCode == "401" && Yii::$app->getModule('user')->settings->get('auth.allowGuestAccess')) {
            return $this->render('401_guests', ['message' => $message]);
        }

        return $this->render('index', [
            'message' => $message
        ]);
    }

}

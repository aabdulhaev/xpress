<?php

/**
 * @copyright Copyright (c) 2021 VadimTs
 * @link https://tsvadim.dev/
 * Creator: VadimTs
 * Date: 14.04.2021
 */

namespace api\modules\v1\controllers;

use common\forms\SupportRequestForm;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;

class ContactController extends Controller
{
    protected function verbs()
    {
        return [
            'support-request' => ['POST', 'OPTIONS'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }


    public function actionSupportRequest()
    {
        $model = new SupportRequestForm();

        if ($model->load(\Yii::$app->request->getBodyParams(), '') && $model->validate()) {
            $user = \Yii::$app->user->identity;
            $model->sendEmail($user);
            $model->sendThanksEmail($user);
            \Yii::$app->response->setStatusCode(200);
        } elseif (!$model->getErrors()) {
            throw new ServerErrorHttpException('Failed to support request for unknown reason');
        }

        return $model;
    }
}

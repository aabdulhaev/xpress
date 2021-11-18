<?php

namespace api\modules\v1\controllers;

use light\swagger\SwaggerAction;
use light\swagger\SwaggerApiAction;
use Yii;
use yii\filters\ContentNegotiator;
use yii\helpers\Url;
use yii\web\ErrorAction;
use yii\web\Response;

/**
 * @OA\OpenApi(
 *    security={{"bearerAuth": {}}}
 * )
 * @OA\Info(
 *     version="1.0",
 *     title="XPress API"
 * )
 * @OA\Server(
 *     description="Api server",
 *     url=API_HOST,
 * )
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ErrorModel",
 *     required={"code"},
 *     @OA\Property(
 *         property="code",
 *         type="integer",
 *         format="int32"
 *     )
 * )
 */
class DocsController extends \yii\web\Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'text/html' => Response::FORMAT_HTML
            ]
        ];
        return $behaviors;
    }

    public function actions()
    {
        return [
            'index' => [
                'class' => SwaggerAction::class,
                'restUrl' => Url::to(['docs/json-schema'])
            ],
            'json-schema' => [
                'class' => SwaggerApiAction::class,
                'scanDir' => [
                    Yii::getAlias('@app/modules/v1'),
                    Yii::getAlias('@app/../common/models'),
                    Yii::getAlias('@app/../common/forms')
                ]
            ],
            'error' => [
                'class' => ErrorAction::class
            ]
        ];
    }
}

<?php

namespace common\models\traits;

use Exception;
use Firebase\JWT\JWT;
use Yii;
use yii\web\Request as WebRequest;
use yii\web\UnauthorizedHttpException;

trait JwtTrait
{

    /**
     * @param WebRequest $request
     * @return mixed|null
     * @throws UnauthorizedHttpException
     */
    public static function findByRequest(WebRequest $request)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        $authPost = $request->post('Authorization');

        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {
            return static::findIdentityByAccessToken($matches[1]);
        }

        if ($authPost !== null && preg_match('/^Bearer\s+(.*?)$/', $authPost, $matches)) {
            return static::findIdentityByAccessToken($matches[1]);
        }

        return null;
    }

    /**
     * @param $token
     * @param null $type
     * @return mixed
     * @throws UnauthorizedHttpException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $errorText = "Incorrect token";
        $decodedArray = static::decodeJWT($token);
        if (!isset($decodedArray['pk'])) {
            throw new UnauthorizedHttpException($errorText);
        }
        if (!isset($decodedArray['pka'])) {
            throw new UnauthorizedHttpException($errorText);
        }
        // pk is unique identifier of model.
        $value = $decodedArray['pk'];
        $attribute = $decodedArray['pka'];
        return static::findByPk($value, $attribute);
    }

    /**
     * @param $token
     * @return array
     * @throws UnauthorizedHttpException
     */
    public static function decodeJWT($token): array
    {
        $secret = static::getSecretKey();
        $errorText = "Incorrect token";
        // Decode token and transform it into array.
        // Firebase\JWT\JWT throws exception if token can not be decoded
        try {
            $decoded = JWT::decode($token, $secret, [static::getAlgo()]);
        } catch (Exception $e) {
            throw new UnauthorizedHttpException($e->getMessage(), $e->getCode());
        }

        return (array)$decoded;
    }

    /**
     * @return mixed
     */
    protected static function getSecretKey()
    {
        return Yii::$app->params['JWT_SECRET'];
    }

    /**
     * @return string
     */
    public static function getAlgo(): string
    {
        return 'HS256';
    }

    /**
     * @param $value
     * @param $attribute
     * @return mixed
     * @throws UnauthorizedHttpException
     */
    public static function findByPk($value, $attribute)
    {
        $model = static::findOne([$attribute => $value]);
        $errorText = 'Incorrect token';
        // Throw error if user is missing
        if (empty($model)) {
            throw new UnauthorizedHttpException($errorText);
        }
        return $model;
    }

    /**
     * @param $attribute
     * @param array $payload
     * @return string
     */
    public function getJWT($attribute, $payload = []): string
    {
        $secret = static::getSecretKey();
        $currentTime = time();
        $request = Yii::$app->request;
        $hostInfo = '';

        // There is also a \yii\console\Request that doesn't have this property
        if ($request instanceof WebRequest) {
            $hostInfo = $request->hostInfo;
        }
        $payload['iss'] = $hostInfo;
        $payload['aud'] = $hostInfo;
        $payload['iat'] = $currentTime;
        $payload['nbf'] = $currentTime;

        // Set up model pk
        $payload['pk'] = $this->getPayloadPk($attribute);
        $payload['pka'] = $attribute;
        if (!isset($payload['exp'])) {
            $payload['exp'] = $currentTime + static::getJwtExpire();
        }
        return JWT::encode($payload, $secret, static::getAlgo());
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function getPayloadPk($attribute)
    {
        return $this->{$attribute};
    }

    /**
     * @return mixed
     */
    protected static function getJwtExpire()
    {
        return Yii::$app->params['JWT_EXPIRE'];
    }
}

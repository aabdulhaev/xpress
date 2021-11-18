<?php

namespace common\tests\fixtures;

use common\models\Client;
use yii\test\ActiveFixture;

class ClientFixture extends ActiveFixture
{
    public const CLIENT_1_UUID = '1eb147ad-87da-6290-fc63-37af89523de0';
    public const CLIENT_2_UUID = '1eb147ad-87da-6291-4dd8-a7954cbdc768';
    public const CLIENT_3_UUID = '1eb241d9-07c5-6150-8d40-b555f790810e';
    public const CLIENT_4_UUID = '1eb2a46a-a28a-65c0-2ebf-529b82fa1987';

    public $modelClass = Client::class;
    public $depends = [
        UserFixture::class,
    ];
}

<?php

declare(strict_types=1);

namespace common\validators;

use Exception;
use yii\helpers\ArrayHelper;

class UniqueValidator extends \yii\validators\UniqueValidator
{
    /**
     * @param string $message
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function formatMessage($message, $params): array
    {
        $message = parent::formatMessage($message, $params);
        preg_match_all('/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/', $message, $m);
        return [
            'not_unique' => 1,
            'employee_uuid' => ArrayHelper::getValue($m, '0.0'),
            'mentor_uuid' => ArrayHelper::getValue($m, '0.1'),
        ];
    }
}

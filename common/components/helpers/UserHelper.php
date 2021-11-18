<?php

namespace common\components\helpers;


use yii\helpers\ArrayHelper;

class UserHelper
{
    /**
     * @param array $val
     * @param string $column
     * @return array
     */
    public static function getUniqueValues(array $val, string $column): array
    {
        $subjectUuids = ArrayHelper::getColumn($val, $column, false);
        $subjectTitles = ArrayHelper::getColumn($val, 'title', false);

        $uuids = [];
        $titles = [];

        for ($i=0; $i < count($subjectUuids); $i++) {
            if (in_array($subjectTitles[$i], $titles)) {
                continue;
            }
            if (empty($subjectUuids[$i])) {
                continue;
            }
            $uuids[] = $subjectUuids[$i];
            $titles[] = $subjectTitles[$i];
        }
        return $uuids;
    }
}

<?php

namespace common\helpers;

use Yii;

class DB
{
    public static function getFK($tableFrom, $tableTo)
    {
        $fks = Yii::$app->db->schema->getTableSchema($tableFrom)->foreignKeys;
        $fks = array_filter(
            $fks,
            static function ($v) use ($tableTo) {
                return $v[0] === $tableTo;
            },
            ARRAY_FILTER_USE_BOTH
        );
        if ($fks) {
            return array_key_first($fks);
        }
        return null;
    }
}

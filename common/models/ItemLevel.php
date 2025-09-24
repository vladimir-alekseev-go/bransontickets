<?php

namespace common\models;

class ItemLevel
{
    const LEVEL_PREMIUM = 'PREMIUM';
    const LEVEL_UPGRADE = 'UPGRADE';
    const LEVEL_BASIC = 'BASIC';
    const LEVEL_NONE = 'NONE';

    public static function getLevelList(): array
    {
        return [
            self::LEVEL_PREMIUM => 10,
            self::LEVEL_UPGRADE => 20,
            self::LEVEL_BASIC => 30,
            self::LEVEL_NONE => 999,
        ];
    }

    public static function getLevelValue($val): string
    {
        $ar = self::getLevelList();

        return $ar[$val] ?? (string)$val;
    }
}
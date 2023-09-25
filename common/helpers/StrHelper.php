<?php

namespace common\helpers;

use Yii;
use yii\helpers\StringHelper;

class StrHelper
{
    public static function getCharset()
    {
        return Yii::$app ? Yii::$app->charset : 'UTF-8';
    }
    
    public static function strlen($str)
    {
        return mb_strlen($str, static::getCharset());
    }
    
    public static function strpos($haystack, $needle, $offset = 0)
    {
        return mb_strpos($haystack, $needle, $offset, static::getCharset());
    }
    
    public static function strrpos($haystack, $needle, $offset = 0)
    {
        return mb_strrpos($haystack, $needle, $offset, static::getCharset());
    }
    
    public static function substr($str, $start, $length = null)
    {
        return mb_substr($str, $start, $length, static::getCharset());
    }
    
    public static function strtoupper($str)
    {
        return mb_strtoupper($str, static::getCharset());
    }
    
    public static function strtolower($str)
    {
        return mb_strtolower($str, static::getCharset());
    }
    
    public static function ucfirst($str)
    {
        return static::strtoupper(static::substr($str, 0, 1)) . static::substr($str, 1);
    }
    
    public static function stripTags($str)
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags($str)));
    }
}

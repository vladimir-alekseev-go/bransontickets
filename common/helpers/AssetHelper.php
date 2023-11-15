<?php

namespace common\helpers;

use yii\base\ErrorException;
use yii\helpers\FileHelper;

class AssetHelper
{
    /**
     * Delete assets in a dir
     *
     * @param $dir
     *
     * @return bool
     * @throws ErrorException
     */
    public static function clear($dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }
        $files = array_diff(scandir($dir), array('..', '.'));
        if (empty($files)) {
            return false;
        }
        foreach ($files as $it) {
            if ((strlen($it) === 6 || strlen($it) === 7 || strlen($it) === 8) && is_dir($dir . '/' . $it)) {
                FileHelper::removeDirectory($dir . '/' . $it);
            }
        }
        return true;
    }

    /**
     * Delete minify in a dir
     *
     * @param $dir
     *
     * @return bool
     */
    public static function clearMinify($dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }
        $files = array_diff(scandir($dir), array('..', '.'));
        if (empty($files)) {
            return false;
        }
        foreach ($files as $it) {
            $file = $dir . '/' . $it;
            if (!is_dir($file) && file_exists($file)) {
                FileHelper::unlink($file);
            }
        }
        return true;
    }
}

<?php

namespace common\helpers;

use common\models\upload\UploadForm;
use yii\bootstrap\Html;

class General
{
    public static function formatPhoneNumber($phone)
    {
        $phone = str_replace(["(", ")", "-", " "], "", $phone);
        if (strlen($phone) === 10) {
            return substr($phone, -10, 3) . "-" . substr($phone, -7, 3) . "-" . substr($phone, -4, 4);
        }
        if (strlen($phone) === 11) {
            return $phone[strlen($phone) - 11] . "-"
                . substr($phone, -10, 3)
                . "-" . substr($phone, -7, 3)
                . "-" . substr($phone, -4, 4);
        }

        return $phone;
    }

    public static function base64($path): string
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public static function unsetArrayElementByValue(string $value, array $array): array
    {
        if (($key = array_search($value, $array, true)) !== false) {
            unset($array[$key]);
        }
        return $array;
    }

    public static function view(object $model, $field, $options = []): string
    {
        $fileInfo = !empty($model->{$field}) ? pathinfo($model->{$field}->getUrl()) : null;
        $extension = $fileInfo ? $fileInfo['extension'] : null;
        $viewFile = null;
        if (in_array($extension, UploadForm::IMG_EXTENSIONS, true)) {
            return Html::img($model->{$field}->getUrl(), $options);
        }

        if ($extension) {
            return Html::a($fileInfo['basename'], $model->{$field}->getUrl(), ['target' => '_blank']);
        }
        return '';
    }
}

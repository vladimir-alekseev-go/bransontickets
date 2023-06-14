<?php

namespace common\tripium;

use Exception;
use yii\base\Model;

class TripiumUpdater extends Model
{
    public const SHOW_ERROR_COUNT = 'show_error_count';
    public const SHOW_ERROR_DETAIL = 'show_error_detail';
    public const MODE_HIDE_MESSAGE = 'hide';

    public $mode = self::SHOW_ERROR_COUNT;
    public $models = [];

    public function rules()
    {
        return [
            [['mode', 'models'], 'safe'],
        ];
    }

    public function run(): void
    {
        if ($this->mode !== self::MODE_HIDE_MESSAGE) {
            echo "-- tripium start update --";
            echo "\n\r";
        }
        foreach ($this->models as $modelData) {
            //echo "-- memory_get_usage(): ".memory_get_usage();
            //echo "\n\r";
            try {
                $model = new $modelData['class'];
                if (method_exists($model, 'updateFromTripium')) {
                    if (!empty($modelData['params'])) {
                        foreach ($modelData['params'] as $param => $value) {
                            if (property_exists($model, $param) || method_exists($model, 'set' . $param)) {
                                $model->{$param} = $value;
                            }
                        }
                    }

                    if (isset($modelData['arg'])) {
                        $model->updateFromTripium($modelData['arg']);
                    } else {
                        $model->updateFromTripium();
                    }

                    if (in_array($this->mode, self::getModeList(), true)) {
                        echo "-- " . $modelData['class'] . " updated";
                        echo "\n\r";
                    }

                    if ($this->mode === self::SHOW_ERROR_COUNT) {
                        if (isset($model->errors_add) && count($model->errors_add)) {
                            echo "-- - errors_add: " . count($model->errors_add);
                            echo "\n\r";
                        }

                        if (isset($model->errors_update) && count($model->errors_update)) {
                            echo "-- - errors_update: " . count($model->errors_update);
                            echo "\n\r";
                        }

                        if (isset($model->errors_absent_parent_row) && count($model->errors_absent_parent_row)) {
                            echo "-- - errors_absent_parent_row: " . count($model->errors_absent_parent_row);
                            echo "\n\r";
                        }

                        if (property_exists($model, 'updated') && count($model->updated)) {
                            echo "-- - updated: " . count($model->updated);
                            echo "\n\r";
                        }

                        if (property_exists($model, 'added') && count($model->added)) {
                            echo "-- - added: " . count($model->added);
                            echo "\n\r";
                        }
                    }
                    if ($this->mode === self::SHOW_ERROR_DETAIL) {
                        if (isset($model->errors_add) && count($model->errors_add)) {
                            echo "-- - errors_add:";
                            echo "\n\r";
                            echo "<pre>";
                            var_export($model->errors_add);
                            echo "</pre>";
                            echo "\n\r";
                        }

                        if (isset($model->errors_update) && count($model->errors_update)) {
                            echo "-- - errors_update: ";
                            echo "\n\r";
                            echo "<pre>";
                            var_export($model->errors_update);
                            echo "</pre>";
                            echo "\n\r";
                        }

                        if (isset($model->errors_absent_parent_row) && count($model->errors_absent_parent_row)) {
                            echo "-- - errors_absent_parent_row: ";
                            echo "\n\r";
                            echo "<pre>";
                            var_export($model->errors_absent_parent_row);
                            echo "</pre>";
                            echo "\n\r";
                        }

                        if (property_exists($model, 'updated') && count($model->updated)) {
                            echo "-- - updated: " . implode(', ', $model->updated);
                            echo "\n\r";
                        }

                        if (property_exists($model, 'added') && count($model->added)) {
                            echo "-- - added: " . implode(', ', $model->added);
                            echo "\n\r";
                        }
                    }
                }
            } catch (Exception $e) {
                if ($this->mode !== self::MODE_HIDE_MESSAGE) {
                    echo $e;
                    echo "\n\r";
                }
            }
        }
        if ($this->mode !== self::MODE_HIDE_MESSAGE) {
            echo "-- tripium end update --";
            echo "\n\r";
        }
    }

    public static function getModeList(): array
    {
        return [
            self::SHOW_ERROR_COUNT,
            self::SHOW_ERROR_DETAIL,
        ];
    }
}

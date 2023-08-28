<?php

namespace common\models;

use yii\base\Model;

class Compare extends Model
{
    public $data = [];

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $compare = $_COOKIE['compare'] ?? '';
        $compare = explode(";", $compare);
        if ($compare) {
            foreach ($compare as &$types) {
                if (!empty($types)) {
                    $types = explode(":", $types);
                    $this->data[$types[0]] = explode(",", $types[1]);
                }
            }
            unset($types);
        }
        parent::init();
    }

    /**
     * Return selected
     *
     * @param $model
     *
     * @return array
     * @deprecated Use `getIDsByType()`.
     */
    public function getIDs($model): array
    {
        return $this->getIDsByType($model::TYPE);
    }

    /**
     * Return selected items.
     *
     * @param string $type
     *
     * @return array
     */
    public function getIDsByType(string $type): array
    {
        $ids = [];
        $data = !empty($this->data[$type]) ? $this->data[$type] : [];

        if (!empty($data) && !empty($data[0])) {
            $ids = $data;
        }

        return $ids;
    }
}

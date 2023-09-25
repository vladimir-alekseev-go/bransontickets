<?php

namespace common\models;

use yii\base\Model;

class Itinerary extends Model
{
    /**
     * @var array $data
     */
    private $data;

    public $session;

    public function loadData(array $data): Itinerary
    {
        if (!empty($data)) {
            $this->data = $data;
        }
        if (!empty($data['itinerary']['session'])) {
            $this->session = $data['itinerary']['session'];
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}

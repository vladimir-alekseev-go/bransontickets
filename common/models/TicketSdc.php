<?php
namespace common\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class TicketSdc extends Model
{
    public $ticket_name;
    public $description;
    public $ticket;
    
    public function loadData($data)
    {
        if (!empty($data['ticketName'])) {
            $this->ticket_name = $data['ticketName'];
        }
        if (!empty($data['description'])) {
            $this->description = $data['description'];
        }
        if (!empty($data['ticket'])) {
            $this->ticket = $data['ticket'];
        }
    }
    
    public function getBarCode()
    {
        return $this->ticket ? $this->ticket['barCode'] : null;
    }
    
    public function getTdssn()
    {
        return $this->ticket ? $this->ticket['tdssn'] : null;
    }
}

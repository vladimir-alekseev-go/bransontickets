<?php
namespace common\models;

use Yii;
use yii\base\Model;

class CartForm extends Model
{
    const AGREE_YES = 1;
    
    public $agree;
    
	public function rules()
    {
        return [
            [['agree'], 'required'],
            ['agree', 'isAgree'],
            ['agree', 'in', 'range' => [1], 'message'=>'Need to accept terms'],
        ];
    }
    
    public function isAgree($attribute, $params)
    {
    	if (!$this->agree) {
    		$this->addError($attribute, 'Need to accept terms');
    	}
    }
    
    public function run()
    {
        if (!$this->validate()) {
            return false;
        }
        
        Yii::$app->basket->basket->setAgreementHaveAccepted(true);
        
    	return true;
    }

}

<?php
namespace common\models;

use common\models\PaymentFormAddCard;

class PaymentFormAddCardBooking extends PaymentFormAddCard
{
	public function formName()
	{
		return 'PaymentFormAddCard';
	}
	
	public function rules()
    {
        return array_merge(parent::rules(),[
        	[['name_card', 'street_address_1', 'country', 'city', 'state', 'zip'], 'required'],
        ]);
    }
}

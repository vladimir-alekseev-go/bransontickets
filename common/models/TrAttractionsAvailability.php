<?php

namespace common\models;

use Yii;

use common\tripium\Tripium;

class TrAttractionsAvailability extends _source_TrAttractionsAvailability
{
    use \common\models\PricesAvailabilityExention;
    
    public function getSourceData($params)
	{
		$tripium = new Tripium;
		
		$this->statusCodeTripium = $tripium->statusCode;
		$res = $tripium->getAttractionsAvailability($params);
		$this->statusCodeTripium = $tripium->statusCode;
		return $res;
	}
}

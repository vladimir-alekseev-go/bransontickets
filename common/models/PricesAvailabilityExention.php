<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

trait PricesAvailabilityExention 
{
	public $statusCodeTripium = null;
	
	public function	updateFromTripium()
    {
		$classNamePath = self::className();
		$className = self::formName();
    	
    	$data = self::find()->select("id, id_external, date")->asArray()->all();
    	
    	$data = ArrayHelper::index($data, function ($it) {
		    return md5($it["id_external"]."_".$it["date"]);
		});
		
		$tripiumData = $this->getSourceData([]);
		
		if($this->statusCodeTripium != \common\tripium\Tripium::STATUS_CODE_SUCCESS)
			return false;
			
		foreach($tripiumData as $id_external => $items)
		{
			if($items)
			{
				foreach($items as $date)
				{
					$date_format = date("Y-m-d H:i:s", strtotime($date));
					$dataItem = [
						"id_external" => $id_external,
						"date" => $date_format,
					];
					
					$hash = md5($id_external."_".$date_format);
					
					if(empty($data[$hash]))
					{
						$model = new $classNamePath;
						$model->load([$className=>$dataItem]);
						$model->save();
					}
					unset($data[$hash]);
				}
			}
		}
    
		//delete
		if(!empty($data))
		{
			$data = ArrayHelper::getColumn($data, 'id');
			$classNamePath::deleteAll(["id"=>$data]);
		}
    }
  
}

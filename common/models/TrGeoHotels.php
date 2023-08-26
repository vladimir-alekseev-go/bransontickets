<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseJson;

use common\tripium\Tripium;

class TrGeoHotels extends _source_TrGeoHotels
{
    const STATUS_ACTIVE = 1;
    
	public function	updateFromTripium()
    {
    	$data = $this->find()->select("id, destination_id, hash_summ")->asArray()->all();
    	
    	$data = ArrayHelper::index($data, 'destination_id');
    	
		$tripium = new Tripium;
		$tripiumData = $tripium->getGeoHotels();

		if($tripium->statusCode != Tripium::STATUS_CODE_SUCCESS)
			return false;
			
		$classNamePath = $this->className();
		$className = $this->formName();
		
		if(!empty($tripiumData))
		{
			foreach($tripiumData as $d)
			{
				$dataNew = [
				    "destination_id" => !empty($d["destinationId"]) ? $d["destinationId"] : null,
				    "description" => !empty($d["description"]) ? $d["description"] : null,
				    "active" => !empty($d["active"]) && $d["active"] == 'active' ? 1 : 0,
				];
				
				$dataNew["hash_summ"] = md5(BaseJson::encode($dataNew));
				if(empty($d["destinationId"]) || !empty($d["destinationId"]) && empty($data[$d["destinationId"]]))
				{
					$Model = new $classNamePath;
					$Model->load([$className=>$dataNew]);
					$Model->save();
				}
				else if($dataNew["hash_summ"] != $data[$d["destinationId"]]["hash_summ"])
				{
					$Model = new $classNamePath;
					$Model = $Model->find()->where(["destination_id"=>$d["destinationId"]])->one();
					$Model->load([$className=>$dataNew]);
					$Model->save();
				}
				if (!empty($d["destinationId"])) unset($data[$d["destinationId"]]);
			}
					
	    	//delete
			if(!empty($data))
			{
				foreach($data as $d)
				{
					$model = new $classNamePath;
					$model->deleteAll("destination_id = '".$d["destination_id"]."'");
				}
			}
		}
    }
}

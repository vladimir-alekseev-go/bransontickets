<?php

namespace common\models;

use Yii;
use yii\helpers\BaseJson;

use common\tripium\Tripium;

class TrLocations extends _source_TrLocations
{
    public function	updateFromTripium()
    {
    	$data = $this->find()->select("id, external_id, hash_summ")->asArray()->all();
		$tmp = [];
		foreach($data as $s)
		{
			$tmp[$s["external_id"]] = $s;
		}
		$data = $tmp;
		unset($tmp);
		
		$tripium = new Tripium;
		$tripiumData = $tripium->getShowslocation();

		if($tripium->statusCode != Tripium::STATUS_CODE_SUCCESS)
			return false;
		
		$classNamePath = $this->className();
		$className = $this->formName();
		
		if(!empty($tripiumData))
		{
			foreach($tripiumData as $d)
			{
				$dataNew = [
					"external_id" => $d["id"],
					"name" => $d["name"],
					"description" => $d["description"],
				];
				$dataNew["hash_summ"] = md5(BaseJson::encode($dataNew));
				if(empty($data[$d["id"]]))
				{
					$Model = new $classNamePath;
					$Model->load([$className=>$dataNew]);
					$Model->save();
				}
				else if($dataNew["hash_summ"] != $data[$d["id"]]["hash_summ"])
				{
					$Model = new $classNamePath;
					$Model = $Model->find()->where(["external_id"=>$d["id"]])->one();
					$Model->load([$className=>$dataNew]);
					$Model->save();
				}
				unset($data[$d["id"]]);
				
			}
					
	    	//delete
			if(!empty($data))
			{
				foreach($data as $d)
				{
					$model = new $classNamePath;
					$model->deleteAll("external_id = '".$d["external_id"]."'");
				}
			}
		}
    }
}

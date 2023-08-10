<?php

namespace common\models;

use yii\helpers\BaseJson;

use common\tripium\Tripium;

class TrAdmissions extends _source_TrAdmissions
{
    public function getSourceData()
	{
		$tripium = new Tripium;
		return $tripium->getAttractions();
	}

	public function	updateFromTripium()
    {
		$classNamePath = $this->className();
		$className = $this->formName();

    	$datas = $this->find()->select("id, id_external, id_external_item, hash_summ")->asArray()->all();
		$tmp = [];
		foreach($datas as $data)
		{
			$tmp[$data["id_external_item"]."_".$data["id_external"]] = $data;
		}
		$datas = $tmp;
		unset($tmp);

		$tripium = new Tripium;
		$tripiumData = $tripium->getAttractions();

		if($tripium->statusCode != Tripium::STATUS_CODE_SUCCESS)
			return false;

		foreach($tripiumData as $data)
		{
			if($data["admissions"])
			{
				foreach($data["admissions"] as $admission)
				{
				    $dataAdmission = [
						"id_external" => $admission["id"],
						"id_external_item" => $data["id"],
						"name" => trim($admission["name"]),
						"inclusions" => $admission["inclusions"],
						"exclusions" => $admission["exclusions"],
					];
					$dataAdmission["hash_summ"] = md5(BaseJson::encode($dataAdmission));
					$hash = $data["id"]."_".$admission["id"];

					if(empty($datas[$hash]))
					{
						$Shows = new $classNamePath;
						$Shows->load([$className=>$dataAdmission]);
						$Shows->save();
					}
					else if($dataAdmission["hash_summ"] != $datas[$hash]["hash_summ"])
					{
						$Shows = new $classNamePath;
						$Shows = $Shows->find()->where(["id_external"=>$admission["id"], "id_external_item" => $data["id"]])->one();
						$Shows->load([$className=>$dataAdmission]);
						$Shows->save();
					}
					unset($datas[$hash]);
				}
			}
		}


		//delete
		if(!empty($datas))
		{
			foreach($datas as $data)
			{
				$Model = new $classNamePath;
				$Model->deleteAll("id_external = ".$data["id_external"]." and id_external_item = ". $data["id_external_item"]);
			}
		}
    }

    public function getActivePrices()
    {
        return $this->getTrAttractionsPrices()
            ->andOnCondition(['or','available > 0','free_sell=1']);
    }

    public function getPrices()
    {
        return $this->getTrAttractionsPrices();
    }
}

<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseJson;

use common\tripium\Tripium;

class TrCategories extends _source_TrCategories
{
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'sort_shows' => 'Sort in a shows section',
            'sort_attractions' => 'Sort in an attractions section',
            /*'sort_hotels' => 'Sort in a hotels section',*/
        ]);
    }

    /**
     * Gets query for [[TrShowsCategories]].
     *
     * @return ActiveQuery
     */
    public function getShows()
	{
	    return $this->hasOne(TrShowsCategories::class, ['id_external_category' => 'id_external']);
	}
	
	/**
     * Gets query for [[TrAttractionsCategories]].
     *
     * @return ActiveQuery
     */
    /*public function getAttractions()
	{
	    return $this->hasOne(TrAttractionsCategories::class, ['id_external_category' => 'id_external']);
	}*/

    /**
     * Gets all [[TrAttractions]].
     *
     * @return ActiveQuery
     */
    /*public function getTrAttractions()
    {
        return $this->hasMany(TrAttractions::class, ['id_external' => 'id_external_show'])->viaTable(TrAttractionsCategories::tableName(), ['id_external_category' => 'id_external']);
    }*/

    /**
     * Gets all [[TrShows]].
     *
     * @return ActiveQuery
     */
    public function getTrShows()
    {
        return $this->hasMany(TrShows::class, ['id_external' => 'id_external_show'])->viaTable(TrShowsCategories::tableName(), ['id_external_category' => 'id_external']);
    }
    
    /*
     * update data from Tripium
     */
	public function	updateFromTripium()
    {
    	$data = self::find()->select("id, id_external, hash_summ")->asArray()->indexBy('id_external')->all();
		
		$tripium = new Tripium;
		$tripiumData = $tripium->getCategories();

		if ($tripium->statusCode != Tripium::STATUS_CODE_SUCCESS) {
			return false;
		}
		
		if (!empty($tripiumData)) {
    		foreach ($tripiumData as $tData) {
    			$dataNew = [
    				"id_external" => $tData["id"],
    				"name" => $tData["name"],
    			];
    			
    			$dataNew["hash_summ"] = md5(BaseJson::encode($dataNew));
    			
    			if (empty($data[$tData["id"]])) {
    				$model = new self;
    				$model->setAttributes($dataNew);
    				$model->save();
    			} else if($dataNew["hash_summ"] != $data[$tData["id"]]["hash_summ"]) {
    				$model = self::find()->where(["id_external"=>$tData["id"]])->one();
    				$model->setAttributes($dataNew);
    				$model->save();
    			}
    			
    			if (!empty($data[$tData["id"]])) {
    			    unset($data[$tData["id"]]);
    			}
    		}
    					
    	    //delete
    		if (!empty($data)) {
    		    $model = new self;
    			$model->deleteAll(['id_external' => array_values(ArrayHelper::getColumn($data,'id_external'))]);
    		}
  		}
  		
  		return true;
    }
}

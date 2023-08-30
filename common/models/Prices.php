<?php

namespace common\models;

use Exception;
use yii\helpers\BaseJson;
use yii\helpers\ArrayHelper;

use common\models\AttractionsPrices;

abstract class Prices extends \yii\db\ActiveRecord
{
	public abstract function getSourceData($params);
	
	public $errors_add = [];
	public $errors_update = [];
	
	public $statusCodeTripium = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hash', 'id_external', 'hash_summ', 'start', 'name', 'retail_rate', 'stop_sell'], 'required'],
            [['start', 'end'], 'safe'],
            [['retail_rate', 'special_rate', 'tripium_rate'], 'number'],
            [['available', 'sold', 'stop_sell', 'id_external'], 'integer'],
            [['hash', 'hash_summ'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 128],
            [['hash'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_external' => 'ID External',
            'hash' => 'Hash',
            'hash_summ' => 'Hash Summ',
            'start' => 'Start',
            'end' => 'End',
            'name' => 'Name',
            'description' => 'Description',
            'retail_rate' => 'Retail Rate',
            'special_rate' => 'Special Rate',
            'tripium_rate' => 'Tripium Rate',
            'available' => 'Available',
            'sold' => 'Sold',
            'stop_sell' => 'Stop Sell',
        ];
    }
    
    public function validateDates($attribute, $params)
    {
        if (strtotime($this->end) <= strtotime($this->start)) {
            $this->addError('end', 'The end date has to be greater than the start date.');
        }
    }
    /*
	static function getTableMinPrice($params = [])
	{
		$sql = "
    	(SELECT 
    		MIN(IF( special_rate <> 0, special_rate, retail_rate )) AS min_price,
    		price.id_external as min_id_external,
    		MIN(retail_rate) as retail_rate,
    		MIN(price.start) as min_start
    	FROM
    		".static::tableName()." as price
    	LEFT JOIN tr_shows as ts ON ts.id_external = price.id_external
    	WHERE 1
    		and price.name != 'CHILD' and price.name != 'LAP CHILD'
    		".(static::tableName() == "tr_prices" ? "and start > NOW( ) + INTERVAL cut_off HOUR " : "")." 
    		and stop_sell = 0 
    		".(!empty($params["dateStart"]) ? " and end >= '".date("Y-m-d 00:00:00", strtotime($params["dateStart"]))."'" : "")."
    		".(!empty($params["dateEnd"]) ? " and start <= '".date("Y-m-d 00:00:00", strtotime($params["dateEnd"]))."'" : "")."
    		".(!empty($params["dateFrom"]) ? " and start >= '".date("Y-m-d 00:00:00", strtotime($params["dateFrom"]))."'" : "")."
    		".(!empty($params["dateTo"]) ? " and start <= '".date("Y-m-d 23:59:59", strtotime($params["dateTo"]))."'" : "")."
    		".(!empty($params["priceFrom"]) ? " and retail_rate >= ".$params["priceFrom"] : "")."
    		".(!empty($params["priceTo"]) ? " and retail_rate <= ".$params["priceTo"] : "")."
    	GROUP BY 
    		price.id_external)
    	";
		return $sql;
	}
	*/
	public function	updateFromTripium($params=[])
    {
		$period = 180;
    	$classNamePath = static::className();
    	
    	// remove old price
    	$this::deleteAll("end < '".date("Y-m-d H:i:s")."'");
    	
    	// remove duplicates price
		$this::removeDuplicates();
		
		// remove with wrong dates
		if (   $classNamePath::type == AttractionsPrices::type	) {
            $this::deleteAll("end < start");
		}
    	
		$start = !empty($params['start']) ? $params['start'] : date("m/d/Y");
		$end = !empty($params['end']) ? $params['end'] : date("m/d/Y",time()+3600*24*0);
    	
		$mk_start = strtotime($start); 
		$mk_end = strtotime($end); 
		for($i=$mk_start; $i<=$mk_end; $i=$i+3600*24*$period)	
		{
			$from = $i;
			$to = $mk_end < $i+3600*24*$period ? $mk_end : $i+3600*24*$period;
			
			$data = self::find()->select("id, hash, hash_summ")->where("end >= '".date("Y-m-d H:i:s",$from)."' and start <= '".date("Y-m-d 23:59:59",$to)."'")->asArray()->all();
	    	$data = ArrayHelper::index($data, 'hash');
	    	$tripiumData = $this->getSourceData(["start"=>date("m/d/Y",$from),"end"=>date("m/d/Y",$to)]);
	    	
	    	if($this->statusCodeTripium == \common\tripium\Tripium::STATUS_CODE_SUCCESS)
	    	{
	    		$this->updateData($tripiumData, $data);
	    	}
			
	    	unset($data);
			unset($tripiumData);
		}
		
    	// remove duplicates price
		$this::removeDuplicates();
    }
    
    static function removeDuplicates()
    {
    	$classNamePath = self::className();
    	$hash = $classNamePath::find()->select("hash")
	    	->groupby(["hash"])
	    	->having("count(*) > 1")
	    	->asArray()->column();
    	
    	if($hash)
    		$classNamePath::deleteAll(["hash"=>$hash]);
    }
    
    function updateData($tripiumData, $data)
    {
    	$classNamePath = self::className();
		$className = self::formName();

    	if(!empty($tripiumData))
		{
			foreach($tripiumData as $d)
			{
				$dateStart = date("y-m-d H:i:s", strtotime($d["start"]." ".$d["time"]));
				
				foreach($d["prices"] as $p)
				{
					$hash = md5($d["id"]."_".$dateStart."_".$p["name"]."_".$p["description"]);
					
					$dataNew = [
						"id_external" => $d["id"],
						"hash" => $hash,
						"start" => $dateStart,
						"end" => $d["end"] ? date("y-m-d H:i:s", strtotime($d["end"])) : null,
						"name" => $p["name"],
						"retail_rate" => $p["retailRate"],
						"special_rate" => $p["specialRate"] == 0 ? null : $p["specialRate"],
						"description" => $p["description"],
						"tripium_rate" => $p["tripiumRate"] == 0 ? null : $p["tripiumRate"],
						"price" => $p["specialRate"] != null && $p["specialRate"]*1 > 0 ? $p["specialRate"]*1 : $p["retailRate"]*1,
						"available" => $p["available"],
						"sold" => $p["sold"],
						"stop_sell" => $p["stopSell"] ? 1 : 0,
			            
					];
					$dataNew["hash_summ"] = md5(BaseJson::encode($dataNew));

					if(empty($data[$hash]))
					{
						try {
							$Model = new $classNamePath;
							$Model->load([$className=>$dataNew]);
							$Model->save();
							$err = $Model->getErrors();
							if($err)
								$this->errors_add[] = $err;
						} catch (Exception $e) {
						    $this->errors_add[] = $e->getMessage();
						}
					}
					else if($dataNew["hash_summ"] != $data[$hash]["hash_summ"])
					{
						$Model = new $classNamePath;
						$Model = $Model->find()->where(["hash"=>$hash])->one();
						$Model->load([$className=>$dataNew]);
						$Model->save();
						$err = $Model->getErrors();
						if($err)
							$this->errors_update[] = $err->getErrors();
					}
					unset($data[$hash]);
				}
			}

			$this::removeByHash($data);
		}
    }
    
    static function removeByHash($hash)
    {
    	if(!empty($hash))
		{
			$classNamePath = self::className();
    		$classNamePath::deleteAll(["hash" => $hash]);
		}
    }
    /*
    static function getGeneral($id_external)
    {
    	$prices = self::find()
    		->select(["*","DATE_FORMAT(start, '%Y-%m-%d') as start_date","MIN(start) as min_start"])
    		->where(["id_external"=>$id_external, "stop_sell"=>0])
    		->orderby("start asc")
    		->groupby(["name","description"])
    		->asArray()->all();

    	
    	if(empty($prices))
    		return false;
    	
    	unset($tmp);
    	
    	if(!empty($prices))
    	{
    		foreach($prices as $key => $p)
    		{
    			$prices[$key]["price"] = $p["special_rate"]*1 > 0 ? $p["special_rate"]*1 : $p["retail_rate"]*1;
				//$prices[$key]["price"] = $p["tripium_rate"]*1 > 0 ? $p["tripium_rate"]*1 : $prices[$key]["price"];
    		}
    		
	    	usort($prices, function ($a, $b)
			{
			    if ($a["price"] == $b["price"]) {
			        return 0;
			    }
			    return ($a["price"] > $b["price"]) ? -1 : 1;
			});
    	}
    	
    	return [$prices];
    }*/
}

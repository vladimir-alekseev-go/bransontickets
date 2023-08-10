<?php

namespace common\models;

use common\tripium\Tripium;
use DateTime;
use yii\db\ActiveQuery;

class TrAttractionsPrices extends _source_TrAttractionsPrices
{
    use PricesExtensionTrait;

    public const ANY_TIME = 'Any time';
    public const TYPE_ID = 3;
   
    public const type = "attractions";
    public const TYPE = 'attractions';

    public const PRICE_TYPE_FAMILY_PASS = 'FAMILY PASS';
    public const PRICE_TYPE_FAMILY_PASS_4_PACK = "FAMILY PASS 4 PACK";
    public const PRICE_TYPE_FAMILY_PASS_8_PACK = "FAMILY PASS 8 PACK";

    public const MAIN_CLASS = TrAttractions::class;

    public const NAME_ADULT = 'ADULT';

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['end'], 'validateDates'],
            [['name'], 'trim'],
        ]);
    }

    public function getSourceData($params)
	{
		$tripium = new Tripium;
		$res = $tripium->getAttractionsPrice($params);
		$this->statusCodeTripium = $tripium->statusCode;
		return $res;
	}

	public function getType()
	{
	    return self::type;
	}

    public function dateRange($attraction_id_external)
    {
        return self::find()
            ->innerJoin(
                TrAdmissions::tableName() . " as admissions",
                "admissions.id_external = " . self::tableName() . ".id_external"
            )
            ->select(
                [
                    "id_external_item",
                    "min(UNIX_TIMESTAMP(start)) as unix_start",
                    "max(UNIX_TIMESTAMP(end)) as unix_end",
                    "special_rate"
                ]
            )
            ->where(["id_external_item" => $attraction_id_external, "stop_sell" => 0])
            ->groupby("id_external_item")
            ->asArray()->one();
    }

	public static function getActualPrices()
	{
		// don't use it, use model TrAttraction::getActivePrices()
		return self::find()
			->where("start < NOW( )")
			->andWhere("end > NOW( )")
			->andWhere(["stop_sell"=>0])
		;
	}


    /**
     * @return ActiveQuery
     */
    public static function getActive()
    {
        return self::find()
            ->andOnCondition(['stop_sell'=>0])
            ->andOnCondition('start >= NOW( ) and any_time=0 or start >= CURDATE( ) and any_time=1')
        ;
    }

    /**
     * @return ActiveQuery
     */
    public static function getAvailable()
    {
        return self::getActive()
            ->andOnCondition(['or','available > 0','free_sell=1'])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public static function getAvailableSpecial()
    {
        return self::getAvailable()
            ->andOnCondition(['not', ['special_rate'=>false]]);
    }

    public function getAllotments()
    {
        return $this->getAllotment();
    }

    public function getAllotment()
    {
        return $this->getExternal();
    }

    public function getAttractions()
    {
        return $this->getAttraction();
    }

    public function getAttraction()
    {
        return $this->hasOne(TrAttractions::class, ['id_external' => 'id_external_item'])
            ->viaTable(TrAdmissions::tableName(), ['id_external' => 'id_external'])
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getMain()
    {
        return $this->getAttraction();
    }

    /**
     * @param DateTime $date
     * @param array    $ids
     *
     * @return ActiveQuery
     */
    public static function getNearestAvailable(DateTime $date, array $ids)
    {
        return self::getAvailable()
            ->joinWith(['attractions'], false)
            ->select([
                TrAttractions::tableName().'.id',
                TrAttractions::tableName().'.id_external',
                self::tableName().'.price_external_id',
                'start',
                'code',
                'delta' => 'ABS(UNIX_TIMESTAMP(start) - '.$date->getTimestamp().')'
            ])
            ->distinct()
    	    ->orderby('delta')
    	    ->groupby([
    	        TrAttractions::tableName().'.id',
    	        TrAttractions::tableName().'.id_external',
    	        self::tableName().'.price_external_id',
    	        'start',
    	        'delta'])
            ->where(['id_external_item' => $ids]);
    }
}

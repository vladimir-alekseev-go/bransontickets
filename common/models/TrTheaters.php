<?php

namespace common\models;

use yii\helpers\Json;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class TrTheaters extends _source_TrTheaters
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
	{
		return [
			'timestamp' => [
			    'class' => TimestampBehavior::className(),
			    'attributes' => [
			        ActiveRecord::EVENT_BEFORE_INSERT => ['updated_at'],
			        ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
			    ],
			    'value' => new Expression('NOW()'),
			],
		];
	}
	
	/**
	 * Return Status List
	 * @return []
	 */
	public static function getStatusList()
	{
	    return [
	        self::STATUS_ACTIVE => 'Active',
	        self::STATUS_INACTIVE => 'Inactive',
	    ];
	}
	
	/**
	 * Return Status Value
	 * @return string
	 */
	public static function getStatusValue($val)
	{
	    $ar = self::getStatusList();
	    
	    return isset($ar[$val]) ? $ar[$val] : $val;
	}
	
	/**
	 * Return Hash of data
	 * @param $item
	 * @return string
	 */
	public static function makeHash($item)
    {
        return md5(Json::encode($item));
    }
    
    /**
     * Return the whole address
     * @return string
     */
    public function getSearchAddress()
    {
        return trim(str_replace(["\n","\t","\r"], "", $this->address1.", ".$this->city.", ".$this->state." ".$this->zip_code));
    }
}

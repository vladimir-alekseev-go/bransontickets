<?php

namespace common\models;

use common\tripium\Tripium;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class TrHotels
 *
 * @deprecated
 */
class TrHotels extends _source_TrHotels
{
    use ItemsExtensionTrait;

    /**
     * @deprecated
     */
    public const type = 'hotels';
    /**
     * @deprecated
     */
    public const name = 'Hotel';
    public const TYPE = 'hotels';
    public const NAME = 'Lodging';
    public const NAME_PLURAL = 'Lodging';

	public const SMOKING_NS = 'NS';
	public const SMOKING_S = 'S';
	public const SMOKING_E = 'E';
	
	public const OPTIONS_ROOM_TYPES = 'ROOM_TYPES';

    public const photoJoinClass = HotelsPhotoJoin::class;
    public const priceClass = '';

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    
    public const EXTERNAL_SERVICE_SDC = 'SDC';
    
    private $tripiumData;
    
    public $fullUpdate = true;
    public $minPrice = 0;
    public $statusDefault = 1;
    
    public function setTripiumData($tripiumData)
    {
        $this->tripiumData = $tripiumData;
        $this->setMinPrice();
    }
    
    public function setMinPrice()
    {
        $priceMin = [];
        if (isset($this->tripiumData['roomRateDetails'])) {
            foreach ($this->tripiumData['roomRateDetails'] as $roomRateDetails) {
                $ar = $roomRateDetails['RateInfos']['list'][0]['ChargeableRateInfo']['@averageRate'];
                $abr = $roomRateDetails['RateInfos']['list'][0]['ChargeableRateInfo']['@averageBaseRate'];
                $priceMin[] = ['ar' => $ar, 'abr' => $abr];
            }
            ArrayHelper::multisort($priceMin, ['ar'], [SORT_ASC]);
        }
        
        $res = $priceMin[0] ?? $priceMin;
        $this->minPrice = $res['ar'];
    }
    
    public function getPriceMin()
    {
        $priceMin = [];
        if (isset($this->tripiumData['roomRateDetails'])) {
            foreach ($this->tripiumData['roomRateDetails'] as $roomRateDetails) {
                $ar = $roomRateDetails['RateInfos']['list'][0]['ChargeableRateInfo']['@averageRate'];
                $abr = $roomRateDetails['RateInfos']['list'][0]['ChargeableRateInfo']['@averageBaseRate'];
                $priceMin[] = ['ar' => $ar, 'abr' => $abr];
            }
            ArrayHelper::multisort($priceMin, ['ar'], [SORT_ASC]);
        }
        return $priceMin[0] ?? $priceMin;
    }
    
    public static function detailURL($code = null)
    {
        return $code ? Yii::$app->urlManager->createUrl(['lodging/detail', 'code'=>$code]) : Yii::$app->urlManager->createUrl(['lodging/index']);
    }

    /**
     * @return string
     * @deprecated
     */
    public function getType()
    {
    	return $this::TYPE;
    }

    /**
     * @return ActiveQuery
     */
    public function getItemsPhoto()
    {
        //TODO: delete this method
        return $this->getRelatedPhotos();
    }

    /**
     * @deprecated use getItemsPhoto()
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHotelsPhotos()
    {
        //TODO: delete this method
        return $this->getRelatedPhotos();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    function getRelatedPhotos()
    {
        return $this->hasMany(HotelsPhotoJoin::class, ['item_id' => 'id']);
    }

	public function getSourceData($params = [])
	{
		$tripium = new Tripium;
		$res = $tripium->getHotels([], false);
		if (empty($res['results'])) {
		    return [];
		}
		$res['results'] = ArrayHelper::index($res['results'], 'id');
		
		$arrivalDate = isset($params['arrivalDate']) ? strtotime($params['arrivalDate']) : time();
		$departureDate = isset($params['departureDate']) ? strtotime($params['departureDate']) : time()+3600*24*25;
		
		$res2 = $tripium->getHotels([
			'arrivalDate' => date('m/d/Y', $arrivalDate), 
    		'departureDate' => date('m/d/Y', $departureDate),
    		'maxRatePlanCount' => 8, 
    		'numberOfResults' => 200, 
		], false);
		
		$res2['results'] = ArrayHelper::index($res2['results'], 'id');
		
		$res['results'] = array_merge($res['results'], $res2['results']);
		
		$res['results'] = ArrayHelper::index($res['results'], 'id');
		
		$this->statusCodeTripium = $tripium->statusCode;
		return $res['results'];
	}
    
    public static function getSmokingList(): array
    {
    	return [
    		self::SMOKING_NS => 'Non-smoking',
			self::SMOKING_S => 'Smoking',
			self::SMOKING_E => 'Either',
    	];
    }
    
	public static function getSmokingValue($val)
	{
		$ar = self::getSmokingList();

		return $ar[$val] ?? $val;
	}
    
	public function	updateFromTripium($params = [])
    {
        return false;

		$hotels_all = self::find()
    		->select([
                         'id',
                         'id_external',
                         'hash_summ',
                         'hash_summ_fast_update',
                         'updated_at',
    		])
    		->asArray()->all();
		if (!empty($hotels_all)) {
		    $hotels_all = ArrayHelper::index($hotels_all, 'id_external');
		}		
		$tripiumHotels = $this->getSourceData($params);
		if ($this->statusCodeTripium !== Tripium::STATUS_CODE_SUCCESS) {
			return false;
		}
		
		$counter = 0;
		foreach ($tripiumHotels as $hotel) {
		    $counter++;
		    if ($this->maxCountItemUpdate > 0 && $this->maxCountItemUpdate < $counter) {
		        continue;
		    }
		    
			$dataShow = [
				'id_external' => (int)$hotel['id'],
				'name' => $hotel['name'],
				'description' => strip_tags(html_entity_decode($hotel['description'])),
				'address' => $hotel['address'],
				'city' => $hotel['city'],
				'state' => $hotel['state'],
				'zip_code' => $hotel['zipCode'],
				'phone' => $hotel['phone'],
				'fax' => $hotel['fax'],
				'email' => $hotel['email'],
				'hotel_rating' => $hotel['hotelRating'],
				'location_lng' => (string)$hotel['longitude'],
				'location_lat' => (string)$hotel['latitude'],
			    'voucher_procedure' => $hotel['voucherProcedure'],
				'amenities' => implode(', ', $hotel['amenities']),
				'rating' => floor($hotel['hotelRating']),
			    'external_service' => !empty($show['externalService']) ? $show['externalService'] : null,
			];
			$dataShow['hash_summ_fast_update'] = md5(Json::encode($dataShow));
			    
			if ($this->fullUpdate || empty($hotels_all[$hotel['id']])) {
    			$tripium = new Tripium;
    			$hotelDetail = $tripium->getHotel($hotel['id']);
                if (!empty($hotelDetail['hotelImages']['list'])) {
                    $photosList = ArrayHelper::getColumn($hotelDetail['hotelImages']['list'], 'url');
                    if (!empty($hotel['photos'][0])) {
                        $photosFirst = [str_replace('_t.','_b.',$hotel['photos'][0])];
                        $ar = array_merge($photosFirst, $photosList);
                        asort($ar);
                        $dataShow['photos'] = implode(',', $ar);
                    }
                }
                if (!empty($hotelDetail['propertyAmenities']['list'])) {
                    $dataShow['property_amenities'] = implode(';', ArrayHelper::getColumn($hotelDetail['propertyAmenities']['list'], 'amenity'));
                }
    			$prop = [
    				'amenities_description' => 'amenitiesDescription',
    				'area_information' => 'areaInformation',
    				'property_description' => 'propertyDescription',
    				'hotel_policy' => 'hotelPolicy',
    				'deposit_credit_cards_accepted' => 'depositCreditCardsCccepted',
    				'room_information' => 'roomInformation',
    				'driving_directions' => 'drivingDirections',
    				'check_in_instructions' => 'checkInInstructions',
    				'location_description' => 'locationDescription',
    				'room_detail_description' => 'roomDetailDescription',
    			];
    
    			foreach ($prop as $field => $source) {
    			    if (!empty($hotelDetail['hotelDetails'][$source])) {
    				    $dataShow[$field] = strip_tags(html_entity_decode($hotelDetail['hotelDetails'][$source]), '<br><b><i><strong><p>');
    			    }
    			}
    			
    			$dataShow['hash_summ'] = md5(Json::encode($dataShow));
			}
			
			if (empty($hotels_all[$hotel['id']])) {
				$model = new TrHotels;
				$model->setAttributes($dataShow);
				$model->setAttributes(['status' => $this->statusDefault]);
				if ($model->save()) {
				    $photos = explode(',', $model->photos);
				    $model->updatePreview(isset($photos[0]) ? $photos[0] : '');
				    $model->setPhotoAndPreview();
				} else {
					$err = $model->getErrors();
					if ($err) {
						$this->errors_add[] = $err;
					}
				}
			} else if ( 
			    $this->updateForce 
		        || ($this->fullUpdate && $dataShow['hash_summ'] !== $hotels_all[$hotel['id']]['hash_summ'])
			    || (!$this->fullUpdate && $dataShow['hash_summ_fast_update'] !== $hotels_all[$hotel['id']]['hash_summ_fast_update'])
			) {
			    $model = TrHotels::find()->where(['id_external' =>$hotel['id']])->one();
				$model->setAttributes($dataShow);
				if ($model->save()) {
				    $photos = explode(',', $model->photos);
				    $model->updatePreview($photos[0] ?? '');
				    $model->setPhotoAndPreview();
				} else {
					$err = $model->getErrors();
					if($err) {
                        $this->errors_add[] = $err;
                    }
				}
			}
			unset($hotels_all[$hotel['id']]);
		}
	}
	
	/**
	 * @return ActiveQuery
	 */
	public static function getActive()
	{
	    return self::find()->andOnCondition([self::tableName(). '.status' => 1]);
	}
	
    /**
     * Sort hotels array by city
     *
     * @param array $hotels
     *
     * @return array
     */
	public static function resortByCity(array $hotels)
	{
	    if (empty(Yii::$app->params['hotel-city-first'])) {
	        return $hotels;
	    }
	    $first = [];
	    $second = [];
	    foreach ($hotels as $item) {
	        if ($item['city'] == Yii::$app->params['hotel-city-first']) {
	            $first[] = $item;
	        } else {
	            $second[] = $item;
	        }
	    }
	    $hotels = $first;
	    $hotels = array_merge($hotels, $second);
	    return $hotels;
	}
	
	/**
	 * Return item url
	 *
     * @return string
	 */
	public function getUrl()
	{
	    return Yii::$app->urlManager->createUrl(['lodging/detail', 'code'=>$this->code]);
	}
}

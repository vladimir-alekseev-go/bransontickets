<?php

namespace common\models;

use common\helpers\Media;
use common\models\upload\UploadVacationPackageImage;
use common\models\upload\UploadVacationPackagePreview;
use common\tripium\Tripium;
use DateTime;
use Exception;
use yii\base\InvalidConfigException;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;

class VacationPackage extends _source_VacationPackage
{
    public const STATUS_ACTIVE    = 1;
    public const STATUS_IN_ACTIVE = 0;

    public const ITEM_TYPE_SHOW       = 2;
    public const ITEM_TYPE_ATTRACTION = 3;

    public const CHANNEL_TYPE_ALL     = 'all';
    public const CHANNEL_TYPE_DESKTOP = 'desktop';
    public const CHANNEL_TYPE_MOBILE  = 'mobile';

    public $added = [];
    public $updated = [];
    public $errors_add = [];
    public $errors_update = [];
    public $updateForce = false;
    public $updateForceImages = false;

	/**
	 * {@inheritdoc}
	 */
    public function behaviors()
	{
		return [
			'timestamp' => [
			    'class' => TimestampBehavior::class,
			    'attributes' => [
			        ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
			        ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
			    ],
			    'value' => new Expression('NOW()'),
			],
		    'code' => [
		        'class' => SluggableBehavior::class,
		        'attribute' => 'name',
		        'slugAttribute' => 'code',
		        'ensureUnique' => true,
		        'immutable' => false,
		    ],
		];
    }

    /**
     * Return Status List
     *
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_IN_ACTIVE => 'Inactive',
        ];
    }

    /**
     * Return Status Value
     *
     * @param $val
     *
     * @return string
     */
    public static function getStatusValue($val)
    {
        $ar = self::getStatusList();

        return $ar[$val] ?? $val;
    }

    /**
     * @return ActiveQuery
     */
    public static function getActive(): ActiveQuery
    {
        return self::find()->andOnCondition(['status' => self::STATUS_ACTIVE]);
    }

	/**
	 * Get channel of data
     *
	 * @param array|null $channels
	 * @return string
	 */
	public static function getChannelData($channels): string
    {
        if (empty($channels)) {
            return self::CHANNEL_TYPE_ALL;
        }

        foreach ($channels as $channel) {
            if ($channel['type'] === null) {
                return self::CHANNEL_TYPE_ALL;
            }

            if ($channel['type'] === true) {
                return self::CHANNEL_TYPE_DESKTOP;
            }

            if ($channel['type'] === false) {
                return self::CHANNEL_TYPE_MOBILE;
            }
        }

        return self::CHANNEL_TYPE_ALL;
	}

	/**
	 * Sorting
     *
	 * @param &$ar
	 * @param $by
	 */
	private static function sort(&$ar, $by)
	{
	    uasort($ar, static function ($a, $b) use ($by) {
	        if ($a[$by] === $b[$by]) {
	            return 0;
	        }
	        return ($a[$by] < $b[$by]) ? -1 : 1;
	    });
	    $ar = array_values($ar);
	}

    /**
     * Update packages from tripium
     *
     * @return bool
     * @throws Exception|\Throwable
     */
	public function	updateFromTripium()
    {
		$items = self::find()
		->select([
			'id',
			'vp_external_id',
			'hash'
		])
		->asArray()->indexBy('vp_external_id')->all();

		$tripium = new Tripium;
		$tripiumData = $tripium->getVacationPackages();
		if ((int)$tripium->statusCode !== Tripium::STATUS_CODE_SUCCESS) {
			return false;
		}

		foreach ($tripiumData as $item) {
			self::sort($item['ticketTypes'], 'id');
			$item["ticketTypes"] = ArrayHelper::index($item["ticketTypes"], 'id');
		    self::sort($item['items'], 'category');
		    self::sort($item['items'], 'vendorId');
		    self::sort($item['items'], 'typeId');
		    if (is_array($item['types'])) {
                sort($item['types']);
            }
		    if (is_array($item['image'])) {
                sort($item['image']);
            }

		    $dataItem = [
				'vp_external_id' => (int)$item['id'],
				'name' => $item['name'],
 				'description' => $item['description'],
				'status' => $item['status'] ? self::STATUS_ACTIVE : self::STATUS_IN_ACTIVE,
				'period_start' => (new DateTime($item['periodStart']))->format('Y-m-d H:i:s'),
				'period_end' => (new DateTime($item['periodEnd']))->format('Y-m-d H:i:s'),
				'valid_start' => (new DateTime($item['validDateStart']))->format('Y-m-d H:i:s'),
				'valid_end' => (new DateTime($item['validDateEnd']))->format('Y-m-d H:i:s'),
		        'channel' => self::getChannelData($item['channels']),
		        'hashData' => md5(
		            $item['image'].
		            $item['preview'].
                    $item['saveUpTo'].
		            Json::encode($item['prices']).
		            Json::encode($item['items']).
		            count($item['channels']).
		            count($item['ticketTypeQty']).
		            Json::encode($item['types']).
		            Json::encode($item['ticketTypes']).
		            ''
		        ),
			];
		    $dataItem["hash"] = md5(Json::encode(array_values($dataItem)));
			$dataItem["data"] = Json::encode($item);

			if (empty($items[$item["id"]])) {
				$Model = new self;
				$Model->setAttributes($dataItem);
				if ($Model->save()) {
				    $this->added[] = $Model->vp_external_id;
				    $this->updatePrices($item["id"], $item['prices']);
				    $this->updateItems($item["id"], $item['items']);
				    $this->updateCategories($item["id"], $item['types']);
				    $Model->updateImage($item['image']);
				    if (!empty($item['preview'])) {
				        $Model->updatePreview($item['preview']);
				    }
				} else {
					$err = $Model->getErrors();
					if ($err) {
						$this->errors_add[] = $err;
					}
				}
			} else if ($this->updateForce || ($dataItem["hash"] !== $items[$item["id"]]["hash"])) {
				$Model = self::find()->where(["vp_external_id"=>$item["id"]])->one();
				$Model->setAttributes($dataItem);
				if ($Model->save()) {
				    $this->updated[] = $Model->vp_external_id;
				    $this->updatePrices($item["id"], $item['prices']);
				    $this->updateItems($item["id"], $item['items']);
				    $this->updateCategories($item["id"], $item['types']);
				    $Model->updateImage($item['image']);
				    if (!empty($item['preview'])) {
				        $Model->updatePreview($item['preview']);
				    }
				} else {
					$err = $Model->getErrors();
					if ($err) {
					    $this->errors_update[] = $err;
					}
				}
			}

			unset($items[$item["id"]]);
		}

		if (!empty($items)) {
		  self::deleteAll(['vp_external_id' => array_keys($items)]);
		}

        return true;
    }

    /**
     * Updating items.
     *
     * @param int   $vp_external_id
     * @param array $items
     */
    protected function updateItems($vp_external_id, $items)
    {
        $attractionIds = ArrayHelper::getColumn($items, function ($el) {
            return (int)$el['category'] === self::ITEM_TYPE_ATTRACTION ? $el['vendorId'] : 0;
        });

    	VacationPackageShow::deleteAll(['vp_external_id' => $vp_external_id]);
		VacationPackageAttraction::deleteAll(['vp_external_id' => $vp_external_id]);

        $attractions = [];
		if (!empty($attractionIds)) {
		    $attractions = TrAttractions::getActive()->select(['id_external'])->where(['id_external'=>$attractionIds])->column();
		}

    	foreach ($items as $itemData) {
    	    $data = ['vp_external_id'=>$vp_external_id, 'item_external_id'=>$itemData['vendorId'], 'item_type_id'=>$itemData['typeId']];
    	    if ((int)$itemData['category'] === self::ITEM_TYPE_SHOW) {
				$VacationPackageItem = new VacationPackageShow($data);
    	    } else if ((int)$itemData['category'] === self::ITEM_TYPE_ATTRACTION
                && in_array($itemData['vendorId'], $attractions, false)) {
				$VacationPackageItem = new VacationPackageAttraction($data);
    	    } else {
				continue;
			}
			if (!$VacationPackageItem->save()) {
				$err = $VacationPackageItem->getErrors();
				if ($err) {
				    $this->errors_update[] = $err;
                    /**
                     * @var VacationPackage $Model
                     */
					$Model = self::find()->where(['vp_external_id'=>$vp_external_id])->one();
					$Model->hash = '';
					$Model->save();
				}
			}
		}
    }

    /**
     * Updating price.
     *
     * @param int   $vp_external_id
     * @param array $prices
     */
    protected function updatePrices($vp_external_id, $prices)
    {
    	VacationPackagePrice::deleteAll(['vp_external_id' => $vp_external_id]);
		foreach ($prices as $count => $price) {
			$VacationPackagePrice = new VacationPackagePrice(['vp_external_id'=>$vp_external_id, 'count'=>$count, 'price'=>$price]);
			if (!$VacationPackagePrice->save()) {
				$err = $VacationPackagePrice->getErrors();
				if ($err) {
					$this->errors_update[] = $err;
					$Model = self::find()->where(['vp_external_id'=>$vp_external_id])->one();
					$Model->hash = '';
					$Model->save();
				}
			}
		}
    }

    /**
     * Updating categories.
     *
     * @param int $vp_external_id
     * @param array $names
     */
    protected function updateCategories($vp_external_id, $names)
    {
    	VacationPackageCategory::deleteAll(['vp_external_id' => $vp_external_id]);
		foreach ($names as $name) {
			$VacationPackageCategory = new VacationPackageCategory(['vp_external_id'=>$vp_external_id, 'name'=>$name]);
			if (!$VacationPackageCategory->save()) {
				$err = $VacationPackageCategory->getErrors();
				if ($err) {
					$this->errors_update[] = $err;
					$Model = self::find()->where(['vp_external_id'=>$vp_external_id])->one();
					$Model->hash = '';
					$Model->save();
				}
			}
		}
    }

    /**
     * Get full package data
     *
     * @return array
     */
    public function getData()
    {
        return Json::decode($this->data);
    }

    /**
     * Get minimal count of items with could buy.
     *
     * @return int|null
     */
    public function getCountMin()
    {
        return $this->getData() && !empty($this->getData()['itemCount']) && $this->getData()['itemCount']['min']
            ? $this->getData()['itemCount']['min'] : null;
    }

    /**
     * Get maximum count of imtes with could buy
     *
     * @return int|null
     */
    public function getCountMax()
    {
        return $this->getData() && !empty($this->getData()['itemCount'])
            ? $this->getCountMaxFromData($this->getData()['itemCount']) : null;
    }

    /**
     * Get maximum count of items with could buy in data.
     *
     * @param array $data
     * @return int
     */
    private function getCountMaxFromData(array $data)
    {
        $max = $data && $data['max'] ? $data['max'] : 0;
        if ($data['and']) {
            $max += $this->getCountMaxFromData($data['and']);
        }
        if ($data['or']) {
            $max = $max > $this->getCountMaxFromData($data['or']) ? $max : $this->getCountMaxFromData($data['or']);
        }
        return $max;
    }

    /**
     * All items of package
     *
     * @return TrShows[]|TrAttractions[]
     */
    public function getItems(): array
    {
        $groupBy = ['vp_external_id','item_external_id'];
        $itemsAttractions = $this->getVacationPackageAttractions()->select($groupBy)->groupby($groupBy)->all();
        $itemsShows = $this->getVacationPackageShows()->select($groupBy)->groupby($groupBy)->all();
        $items = array_merge($itemsShows, $itemsAttractions);
        shuffle($items);
        return $items;
    }

    /**
     * Return Categories
     *
     * @return ActiveQuery TrCategories
     */
    public function getCategories()
    {
        $categoriesArray = ArrayHelper::map($this->getItems(), static function ($item) {
            return $item->itemExternal->id;
        }, static function ($item) {
            if (!empty($item->itemExternal->trShowsCategories)) {
                return ArrayHelper::getColumn($item->itemExternal->trShowsCategories, 'id_external_category');
            }
            if (!empty($item->itemExternal->trAttractionsCategories)) {
                return ArrayHelper::getColumn($item->itemExternal->trAttractionsCategories, 'id_external_category');
            }
            if (!empty($item->itemExternal->trLunchsCategories)) {
                return ArrayHelper::getColumn($item->itemExternal->trLunchsCategories, 'id_external_category');
            }
            return null;
        });
        $categories = [];
        foreach ($categoriesArray as $values) {
            if (is_array($values)) {
                $ar = array_merge($categories, $values);
                $categories = $ar;
            }
        }
        $categories = array_unique($categories);

        return TrCategories::find()->where(['id_external' => $categories]);
    }

    /**
     * Valid Start DateTime
     * @return DateTime
     * @throws Exception
     */
    public function getValidStart()
    {
        return (new DateTime($this->valid_start));
    }

    /**
     * Valid End DateTime
     * @return DateTime
     * @throws Exception
     */
    public function getValidEnd()
    {
        return (new DateTime($this->valid_end));
    }

    /**
     * Return prices of item
     *
     * @param VacationPackageShow|VacationPackageAttraction $vacationPackageItem
     *
     * @return ActiveQuery TrPrices|TrAttractionsPrices|TrLunchsPrices
     * @throws InvalidConfigException
     */
    public function getItemPrices($vacationPackageItem)
    {
        $ticketTypesByCategory = ArrayHelper::map($this->getData()['ticketTypes'], 'id', 'id', 'category');

        if ($vacationPackageItem instanceof VacationPackageAttraction) {
            $ids = $ticketTypesByCategory[self::ITEM_TYPE_ATTRACTION];
        }
        if ($vacationPackageItem instanceof VacationPackageShow) {
            $ids = $ticketTypesByCategory[self::ITEM_TYPE_SHOW];
        }
        $ids = array_values($ids);
        $query = $vacationPackageItem->itemExternal->getAvailablePrices()
        ->andOnCondition(['price_external_id'=>$ids])
        ;
        if (!empty($vacationPackageItem->item_type_id)) {
            $query->andOnCondition(['id_external'=>$vacationPackageItem->item_type_id]);
        }
        return $query;
    }

    /**
     * Update image of vacation package
     * @param $url
     * @return bool
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function updateImage($url)
    {
        $url = Media::getRealUrl($url);
        $fileTime = Media::getFileTime($url);
        if ($this->updateForceImages || ($fileTime > 0 && (empty($this->image->id)
                    || (!empty($this->image->id) && $url != $this->image->source_url && !empty($url))
                    || (!empty($this->image->id) && $url == $this->image->source_url && $fileTime > $this->image->source_file_time)
                )) || ($fileTime == -1 && empty($this->image->id) && !empty($url))) {
                $uploadVacationPackageImage = new UploadVacationPackageImage;
                $uploadVacationPackageImage->downloadByUrl($url);
                if ($uploadVacationPackageImage->id) {
                    if ($this->image) {
                        $this->image->delete();
                    }
                    $this->image_id = $uploadVacationPackageImage->id;
                    $this->save();
                }
            } else if (!empty($this->image->id) && (empty($url) || (!empty($url) && $fileTime == -1))) {
                if ($this->image) {
                    $this->image->delete();
                }
                $this->image_id = null;
                return $this->save();
            }
         return false;
    }

    /**
     * Update preview of vacation package
     *
     * @param $url
     *
     * @return bool
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function updatePreview($url)
    {
        $url = Media::getRealUrl($url);
        $fileTime = Media::getFileTime($url);
        if ($this->updateForceImages || ($fileTime > 0 && (empty($this->preview->id)
                    || (!empty($this->preview->id) && $url != $this->preview->source_url && !empty($url))
                    || (!empty($this->preview->id) && $url == $this->preview->source_url && $fileTime > $this->preview->source_file_time)
                )) || ($fileTime == -1 && empty($this->preview->id) && !empty($url))) {
                $uploadVacationPackagePreview = new UploadVacationPackagePreview;
                $uploadVacationPackagePreview->downloadByUrl($url);
                if ($uploadVacationPackagePreview->id) {
                    if ($this->preview) {
                        $this->preview->delete();
                    }
                    $this->preview_id = $uploadVacationPackagePreview->id;
                    $this->save();
                }
            } else if (!empty($this->preview->id) && (empty($url) || (!empty($url) && $fileTime == -1))) {
                if ($this->preview) {
                    $this->preview->delete();
                }
                $this->preview_id = null;
                return $this->save();
            }
         return false;
    }

    /**
     * Get all active categories of vacation packages
     * @return array
     */
    public static function getAllActiveTypes()
    {
        $types = [];
        foreach (self::getActive()->select('data')->all() as $item) {
            $ar = array_merge($types, $item->getData()['types']);
            $types = $ar;
        }
        return array_unique($types);
    }

    /**
     * Get types
     * @return array
     */
    public function getTypes()
    {
        return $this->getData()['types'];
    }

    /**
     * Get type ids
     *
     * @param $vendorId
     *
     * @return array
     */
    public function getTypeIdByVendorId($vendorId)
    {
        $ar = [];
        foreach ($this->getData()['items'] as $item) {
			if (!empty($item['typeId']) && $item['vendorId'] == $vendorId) {
				$ar[] = $item['typeId'];
			}
		}
		return $ar;
    }

    /**
     * Get part of conditions as text
     *
     * @param $cond
     *
     * @return string
     */
    public static function getPartConditionsAsText($cond)
    {
        if (empty($cond)) {
            return 'all items';
        }
        if ($cond['min'] == $cond['max']) {
            $text = $cond['min'].' '.substr($cond["category"], 0, ($cond['min'] == 1 ? -1 : strlen($cond["category"])));
        } else {
            $text = 'from '.$cond['min'].' to '.$cond['max'].' '.$cond["category"];
        }
        if (!empty($cond['or'])) {
            $text .= ' or '.self::getPartConditionsAsText($cond['or']);
        }
        if (!empty($cond['and'])) {
            $text .= ' and '.self::getPartConditionsAsText($cond['and']);
        }
        return $text;
    }

    /**
     * Return conditions as text
     *
     * @return string
     */
    public function getConditionsAsText()
    {
        return self::getPartConditionsAsText($this->getData()['itemCount']);
    }

    /**
     * Return SaveUpTo
     *
     * @return double|null
     */
    public function getSaveUpTo()
    {
        if (!empty($this->getData()['saveUpTo'])) {
            $output = preg_replace('/[^0-9\.,]/', '', $this->getData()['saveUpTo']);
            return number_format(str_replace(',', '.', $output), 2, '.', '');
        }

        return null;
    }

    /**
     * Return ticketType
     *
     * @return array|null
     */
    public function getTicketTypes()
    {
        if (!empty($this->getData()['ticketTypes'])) {
            return (array)$this->getData()['ticketTypes'];
        }

        return null;
    }

    /**
     * Return ticketTypeQty
     *
     * @return array|null
     */
    public function getTicketTypeQty()
    {
        if (!empty($this->getData()['ticketTypeQty'])) {
            return (array)$this->getData()['ticketTypeQty'];
        }

        return null;
    }

    /**
     * Return prices
     *
     * @return array|null
     */
    public function getPrices()
    {
        if (!empty($this->getData()['prices'])) {
            return (array)$this->getData()['prices'];
        }

        return null;
    }

    /**
     * Return url
     *
     * @return string
     */
    public function getUrl(): string
    {
        return Url::to(['packages/overview', 'code' => $this->code]);
    }
}

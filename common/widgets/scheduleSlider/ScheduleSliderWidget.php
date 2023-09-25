<?php

namespace common\widgets\scheduleSlider;

use common\models\Package;
use common\models\TrAttractions;
use common\models\TrAttractionsPrices;
use common\models\TrPrices;
use common\models\TrShows;
use DateInterval;
use DatePeriod;
use DateTime;
use Yii;
use yii\base\Exception;
use yii\base\Widget;
use yii\db\ActiveRecord;

class ScheduleSliderWidget extends Widget
{
    public const VIEW_SHOW_IN_ORDER = 'show';
    public const VIEW_SHOW_IN_DESCRIPTION = 'show-description';
    public const VIEW_ATTRACTION_IN_ORDER = 'attraction';
    public const VIEW_ATTRACTION_IN_DESCRIPTION = 'attraction-description';
    
    public const ANY_TIME_YES = 1;
    public const ANY_TIME_NO = 0;
    
	public $viewPreview;
	public $inDescription = false;

    /**
     * @var TrShows|TrAttractions
     */
    public $model;
    /**
     * @var DateTime $date
     */
	public $date;
	public $prices = [];
	public $range = [];
	public $controller;

    /**
     * @var Package $package
     */
    public $package;
    public $scheduleIsShow = true;
    public $scheduleUrl;
    
    private $startDate;

    /**
     * @throws Exception
     */
    public function init()
    {
        $this->assetRegister();
        if (!is_bool($this->inDescription)) {
            throw new Exception(Yii::t('yii', 'class must specify "inDescription" property value.'));
        }
        if (!($this->model instanceof ActiveRecord)) {
            throw new Exception(Yii::t('yii', 'class must specify "model" property value.'));
        }
        if (!empty($this->date) && !($this->date instanceof DateTime)) {
            throw new Exception(Yii::t('yii', 'class must specify "date" property value.'));
        }
        if($this->model::TYPE === TrShows::TYPE && !$this->inDescription) {
            $this->viewPreview = self::VIEW_SHOW_IN_ORDER;
        }
        if($this->model::TYPE === TrShows::TYPE && $this->inDescription) {
            $this->viewPreview = self::VIEW_SHOW_IN_DESCRIPTION;
        }
        if($this->model::TYPE === TrAttractions::TYPE && !$this->inDescription) {
            $this->viewPreview = self::VIEW_ATTRACTION_IN_ORDER;
        }
        if($this->model::TYPE === TrAttractions::TYPE && $this->inDescription) {
            $this->viewPreview = self::VIEW_ATTRACTION_IN_DESCRIPTION;
        }
	}

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getStartDate()
    {
        $this->getRange();
        $this->getPrice();
        return $this->startDate;
	}

    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        if (in_array($this->viewPreview, [self::VIEW_SHOW_IN_ORDER, self::VIEW_ATTRACTION_IN_ORDER], false)) {
            $this->view->registerJs('setTimeout(function(){scheduleSlider.init()}, 300);');
            $this->view->registerJs('setTimeout(function(){scheduleSlider.initHash()}, 400);');
        }
        $this->view->registerJs('setTimeout(function(){scheduleSlider.initInfoOver()}, 500);');
        
        $this->getRange();
        $this->getPrice();
        if (!empty($this->prices)) {
            return $this->render(
                in_array($this->viewPreview, self::getViewPreviewList(), false) ? $this->viewPreview :
                    self::VIEW_ATTRACTION_IN_ORDER, [
            	'model' => $this->model,
            	'prices' => $this->prices,
            	'range' => $this->range,
            ]);
        }

        return '<div class="no-events text-center">There are no events in the near future!</div>';
    }
    
    protected function assetRegister()
    {
        $view = $this->getView();
        ScheduleSliderWidgetAsset::register($view);
    }

    public static function getViewPreviewList(): array
    {
        return array(
            self::VIEW_SHOW_IN_ORDER,
            self::VIEW_SHOW_IN_DESCRIPTION,
            self::VIEW_ATTRACTION_IN_ORDER,
            self::VIEW_ATTRACTION_IN_DESCRIPTION,
        );
    }

    public function setPackage(Package $package = null): void
    {
        $this->package = $package;
    }

	/**
     * Range period of price
     * @return bool
     * @throws \Exception
     */
	public function getRange()
	{
        $class = get_class($this->model);
	    $range = $this->model->getActivePrices()
	        ->joinWith([$this->model->type])
	        ->addSelect([
            	$class::tableName().'.id_external',
            	'start_min' => 'MIN(start)',
	    		'start_max' => 'MAX(start)',
	    	])
            ->asArray()
	        ->one();
	        
        $start = new DateTime();
        $start_min = new DateTime($range['start_min']);
        $start_min = $start_min > $start ? $start_min : $start;
        $start_max = new DateTime($range['start_max']);
	    $start_max->add(new DateInterval('P1D'));
	    
	    if (!($start_min instanceof DateTime && $start_max instanceof DateTime)) {
	        return false;
	    }
	        
	    $diffDays = $start_min->diff($start_max)->days; 
        if ($diffDays < 5) {
            $start_max->add(new DateInterval('P'.(5 - $diffDays).'D'));
        }
        
	    $this->range = new DatePeriod(
    		$start_min, 
    		new DateInterval('P1D'),
    		$start_max
    	);
        return true;
	}
	
	/**
	 * Get price
	 */
	public function getPrice()
	{
	    $priceClass = $this->model->getPriceClass();

	    $tmp = [];
	    
	    if ($this->model::TYPE === TrShows::TYPE) {
	        $AllPrice = $this->model->getAvailablePrices()
    	        ->addSelect([
                	$priceClass::tableName().'.id',
                	$priceClass::tableName().'.id_external',
                	'start',
                	'any_time' => 'concat("0")',
                	'price_min' => 'MIN(price)',
    	    		'price_max' => 'MAX(price)',
    	    		'price',
    	    		'special_rate',
    	    		'retail_rate',
    	    		$priceClass::tableName().'.name',
    	    		$priceClass::tableName().'.description'
    	    	])
                ->groupby($priceClass::tableName().'.id')
                ->orderby('rank, name');
                
    	    if (!empty($this->package)) {
                $AllPrice->orOnCondition(
                    [
                        TrPrices::tableName() . '.id_external' => $this->package->id,
                        'start' => $this->package->getStartDataTime()->format('Y-m-d H:i:s')
                    ]
                );
        	}   
            $AllPrice = $AllPrice->asArray()->all();
            foreach ($AllPrice as $p) {
                unset($p['shows']);
                $date = new DateTime($p['start']);
		        $tmp[0]['list'][$date->format("YMd")][$p['any_time']][$date->format("h:iA")] = $p;
		        $tmp[0]['list_by_time'][$date->format("YMd")][$date->format("h:iA")][] = $p;
		        $tmp[0]['has_not_any_time'] = true;
		        if (empty($this->startDate)) {
		            $this->startDate = $date;
		        } else if ($date < $this->startDate) {
		            $this->startDate = $date;
		        }
    		}
	    }
	    
	    if ($this->model::TYPE === TrAttractions::TYPE) {

    	    $AllPrice = $this->model->getAvailablePrices()
    	        ->joinWith('allotments allotments')
    	        ->addSelect([
                	$priceClass::tableName().'.id',
                	$priceClass::tableName().'.id_external',
                	'start',
                	'any_time',
                	'price_min' => 'MIN(price)',
    	    		'price_max' => 'MAX(price)',
    	    		'price',
    	    		'special_rate',
    	    		'retail_rate',
    	    		$priceClass::tableName().'.name',
    	    		$priceClass::tableName().'.description',
    	    	])
                ->groupby($priceClass::tableName().'.id')
                ->orderby('rank, name');
                
            if (!empty($this->package)) {
                $class = $this->model::priceClass;
                $AllPrice->orOnCondition(
                    [

                        $class::tableName() . '.id_external' => $this->package->type_id,
                        'start' => $this->package->getStartDataTime()->format('Y-m-d H:i:s')
                    ]
                );
        	}

            $AllPrice = $AllPrice->asArray()->all();
    	    foreach ($AllPrice as $p) {
    	        unset($p['attractions']);
    		    $date = new DateTime($p['start']);
                if (!empty($this->package)
                    && (int)$p['allotments']['id_external'] !== (int)$this->package->type_id) {
                    continue;
                }
                $tmp[$p['allotments']['name']]['allotmentId'] = $p['allotments']['id_external'];
    		    if ((int)$p['any_time'] === self::ANY_TIME_YES) {
    		        $tmp[$p['allotments']['name']]['list'][$date->format("YMd")][$p['any_time']][] = $p;
    		        $tmp[$p['allotments']['name']]['has_any_time'] = true;
    		    } else {
    		        $tmp[$p['allotments']['name']]['list'][$date->format("YMd")][$p['any_time']][$date->format("h:iA")] = $p;
    		        $tmp[$p['allotments']['name']]['list_by_time'][$date->format("YMd")][$date->format("h:iA")][] = $p;
    		        $tmp[$p['allotments']['name']]['has_not_any_time'] = true;
    		    }

    		    if (!isset($tmp[$p['allotments']['name']]['min'])) {
                    $tmp[$p['allotments']['name']]['min'] = 9999999999;
                } else if ($p['price_min'] < $tmp[$p['allotments']['name']]['min']) {
                    $tmp[$p['allotments']['name']]['min'] = $p['price_min'];
                }

    		    if (!isset($tmp[$p['allotments']['name']]['max'])) {
                    $tmp[$p['allotments']['name']]['max'] = 0;
                } else if ($p['price_max'] > $tmp[$p['allotments']['name']]['max']) {
                    $tmp[$p['allotments']['name']]['max'] = $p['price_max'];
                }
    		}
            ksort($tmp);
	    }

	    /**
	     * don't use &$
	     * sort data by ticket name
	     * */
	    foreach ($tmp as $k1 => $data) {
	    	foreach ($data as $k2 => $ar) {
	    		if (is_array($ar)) {
			    	foreach ($ar as $k3 => $dataByType) {
			    	    foreach ($dataByType as $k4 => $byType) {
			    	        uasort($byType, function ($a, $b) {
							    if ($a['start'] == $b['start']) {
							        return 0;
							    }
							    return ($a['start'] < $b['start']) ? -1 : 1;
							});
							$dataByType[$k4] = $byType;
			    		}
			    		$ar[$k3] = $dataByType;
			    	}
	    		}
	    		$data[$k2] = $ar;
	    	}
	    	$tmp[$k1] = $data;
	    }

		foreach ($tmp as &$data) {
	        if (!empty($data['list'])) {

				$data['max_offers_by_day'] = 0;
				
				foreach ($data['list'] as $day => $types) {

				    $max_offers_by_day = 0;

				    foreach ($types as $type => $price) {
				        if($type == self::ANY_TIME_YES) {
                            $max_offers_by_day++;
                        }
				        if($type == self::ANY_TIME_NO) {
                            $max_offers_by_day += count($price);
                        }
				    }
				    
				    $data['max_offers_by_day'] = $data['max_offers_by_day'] < $max_offers_by_day ? $max_offers_by_day : $data['max_offers_by_day'];
				}
	        }
		}
		unset($data);

		$this->prices = $tmp;
	}
}

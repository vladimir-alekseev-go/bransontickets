<?php

namespace common\models;

use common\helpers\General;
use common\tripium\Tripium;
use DateTime;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class TrAttractions extends _source_TrAttractions
{
    use ItemsExtensionTrait;

    public const TAG_ORIGINAL_ON_SALE = 'On Sale';
    public const TAG_ORIGINAL_PREMIUM = 'Premium';
    public const TAG_ORIGINAL_FEATURED = 'Featured';
    public const TAG_ORIGINAL_FAMILY_PASS = 'Family Pass';
    public const TAG_ORIGINAL_LIMITED = 'Limited';

    public const TAG_USD = 'usd';
    public const TAG_PREMIUM = 'star';
    public const TAG_FEATURED = 'Featured';
    public const TAG_FAMILY_PASS = 'tags';
    public const TAG_LIMITED = 'clock-o';

    public const CALL_US_TO_BOOK_YES = 1;
    public const CALL_US_TO_BOOK_NO = 0;

    public const priceClass = TrAttractionsPrices::class;
    public const priceGroup = TrAdmissions::class;
    public const categoriesClass = Categories::class;
    public const joinCategoriesClass = AttractionsCategories::class;
    public const photoJoinClass = AttractionsPhotoJoin::class;

    public const TYPE_ID = 3;
   
    public const type = 'attractions';
    public const TYPE = 'attractions';
  
    public const name = 'Attraction';
    public const NAME = 'Attraction';
    public const NAME_PLURAL = 'Attractions';

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    public const STATUS_WL_ACTIVE = 1;
    public const STATUS_WL_INACTIVE = 0;

    public const EXTERNAL_SERVICE_SDC = 'SDC';
    
    public static $type = 'attractions';
   
    public static $name = 'Attraction';
    
    public $priceClass = AttractionsPrices::class;
   
    public $categoriesClass = AttractionsCategories::class;
   
    public $categoriesClassName = 'AttractionsCategories';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    ['theatre_id'],
                    'exist',
                    'skipOnError' => true,
                    'targetClass' => TrTheaters::class,
                    'targetAttribute' => ['theatre_id' => 'id_external']
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), ['show_in_footer' => 'Display In Footer']);
    }

    /**
     * @return ActiveQuery
     * @deprecated use getRelatedPhotos()
     */
    public function getAttractionsPhoto()
    {
        //TODO: delete this method
        return $this->getRelatedPhotos();
    }

    /**
     * @return ActiveQuery
     * @deprecated use getRelatedPhotos()
     */
    public function getItemsPhoto()
    {
        //TODO: delete this method
        return $this->getRelatedPhotos();
    }

    /**
     * @return ActiveQuery
     */
    function getRelatedPhotos()
    {
        return $this->hasMany(AttractionsPhotoJoin::class, ['item_id' => 'id']);
    }

    public static function getTagsList()
    {
        return array(
            self::TAG_USD => self::TAG_ORIGINAL_ON_SALE,
            self::TAG_PREMIUM => self::TAG_ORIGINAL_PREMIUM,
            self::TAG_LIMITED => self::TAG_ORIGINAL_LIMITED,
        );
    }

    public function getSourceData()
    {
        $tripium = new Tripium;
        $res = $tripium->getAttractions($this->updateOnlyIdExternal);
        $this->statusCodeTripium = $tripium->statusCode;
        return $res;
    }

    /**
     * Return item url.
     *
     * @param mixed $options
     *
     * @return string
     */
    public function getUrl($options = null): string
    {
        if (is_array($options)) {
            return Yii::$app->urlManager->createUrl(
                array_merge(['attractions/detail'], $options, ['code' => $this->code])
            );
        }
        return Yii::$app->urlManager->createUrl(['attractions/detail', 'code' => $this->code]);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getCategory()
    {
        return $this->getCategories();
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getCategories()
    {
        return $this->hasMany(TrCategories::class, ['id_external' => 'id_external_category'])
            ->viaTable(TrAttractionsCategories::tableName(), ['id_external_show' => 'id_external']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRelatedCategories(): ActiveQuery
    {
        return $this->getTrAttractionsCategories();
    }

    /**
     * @return ActiveQuery
     */
    public static function getAllCategories(): ActiveQuery
    {
        return TrAttractionsCategories::find()->joinWith('idExternalShow', false, 'INNER JOIN');
    }

    /**
     * @return string
     * @deprecated Use Class::TYPE or self::TYPE
     */
    public static function getType()
    {
        return self::TYPE;
    }

    public static function getAllPrices($params = [])
    {
        $price = self::priceClass;
        $priceGroup = self::priceGroup;

        return $price::find()->from($price::tableName() . ' as price')
            ->select(
                [
                    'price.*',
                    'main.id as main_id',
                    'main.name as main_name',
                    'main.code as main_code',
                    'main.id_external as main_id_external',
                    'pricegroup.id_external as pricegroup_id_external',
                    'pricegroup.name as pricegroup_name',
                ]
            )
            ->innerJoin($priceGroup::tableName() . ' as pricegroup', 'pricegroup.id_external = price.id_external')
            ->innerJoin(self::tableName() . ' as main', 'main.id_external = pricegroup.id_external_item');
    }

    public static function getActualPrices($params = [])
    {
        $query = self::getAllPrices($params);
        $query
            ->where(['stop_sell' => 0])
            ->andWhere(['main.status' => 1])
            ->andWhere('end > NOW( )')
            ->andOnCondition('start >= NOW( ) and any_time=0 or start >= CURDATE( ) and any_time=1');
        if (!(isset($params['without_availability']) && $params['without_availability'] == 1)) {
            $query->andWhere(['or', 'available > 0', 'free_sell=1']);
        }

        return $query;
    }

    /**
     * Return Original Tags Title
     *
     * @return array
     */
    public static function getOriginalTagTitleList()
    {
        return [
            self::TAG_ORIGINAL_ON_SALE => 'On Sale',
            self::TAG_ORIGINAL_PREMIUM => 'Premium',
            self::TAG_ORIGINAL_FEATURED => 'Featured',
            self::TAG_ORIGINAL_FAMILY_PASS => 'Family Pass',
            self::TAG_ORIGINAL_LIMITED => 'Limited Engagement',
        ];
    }

    public static function getPriceByFilter(\common\models\form\Search $Search)
    {
        $dateTimeFromMax = clone $Search->getDateTimeFrom();
        $dateTimeFromMax->add(new \DateInterval('P7D'));

        $query = self::getActualPrices()
            ->select(
                [
                    //'price.id',
                    'price.start',
                    'price.price',
                    'price.any_time',
                    'pricegroup.name',
                    'pricegroup.id_external',
                    'price_min' => 'MIN(price.price)',
                    'price_max' => 'MAX(price.price)',
                    'main.id_external as main_id_external',
                    'special_rate',
                    'retail_rate',
                    'price.name',
                    'price.description',
                    'pricegroup.name as price_group_name',
                ]
            )
            ->groupby('price.id')
            ->andFilterWhere(['>=', 'price.start', $Search->getDateTimeFrom()->format('Y-m-d 00:00:00')])
            ->andFilterWhere(['<=', 'price.start', $dateTimeFromMax->format('Y-m-d 23:59:59')]);
        if (!empty($Search->externalIds)) {
            $query->andWhere(['main.id_external' => $Search->externalIds]);
        }
        return $query->asArray()->all();
    }

    public static function preparePriceForList($priceAll): array
    {
        $tmp = [];
        usort(
            $priceAll,
            static function ($a, $b) {
                if ($a['start'] === $b['start']) {
                    return 0;
                }
                return ($a['start'] < $b['start']) ? -1 : 1;
            }
        );
        foreach ($priceAll as $p) {
            $date = new DateTime($p['start']);
            if ((int)$p['any_time'] === 1) {
                $tmp[$p['main_id_external']][$p['price_group_name']]['list'][$date->format('Md')][$p['any_time']][] = $p;
            } else if (
                !empty($p['special_rate']) ||
                empty($tmp[$p['main_id_external']][$p['price_group_name']]['list'][$date->format('Md')][$p['any_time']][$date->format('h:iA')])
            ) {
                $tmp[$p['main_id_external']][$p['price_group_name']]['list'][$date->format('Md')][$p['any_time']][$date->format('h:iA')] = $p;
            }
            $tmp[$p['main_id_external']][$p['price_group_name']]['id_external'] = $p['id_external'];
        }
        foreach ($tmp as $k => $p) {
            ksort($p);
            $tmp[$k] = $p;
        }
        foreach ($tmp as &$itemData) {
            foreach ($itemData as &$data) {
                if (!empty($data['list'])) {
                    $min = 9999999999999;
                    $max = 0;

                    foreach ($data['list'] as $types) {
                        foreach ($types as $price) {
                            foreach ($price as $p) {
                                $min = $p['price_min'] < $min ? $p['price_min'] : $min;
                                $max = $p['price_max'] > $max ? $p['price_max'] : $max;
                            }
                        }
                    }
                    $data['min'] = $min;
                    $data['max'] = $max;
                }
            }
        }
        unset($itemData, $data);
        $priceAll = $tmp;
        return $priceAll;
    }

    /**
     * @return ActiveQuery
     */
    public function getAllotments()
    {
        return $this->getTrAdmissions();
    }

    /**
     * @return array
     */
    public static function getConditionActive()
    {
        return [self::tableName() . '.status' => 1];
    }

    /**
     * @return ActiveQuery
     */
    public static function getActive()
    {
        return self::find()
            ->andOnCondition(self::getConditionActive());
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getPrices()
    {
        return $this->hasMany(TrAttractionsPrices::class, ['id_external' => 'id_external'])
            ->viaTable(TrAdmissions::tableName(), ['id_external_item' => 'id_external']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getActivePrices()
    {
        return $this->getPrices()
            ->andOnCondition([TrAttractionsPrices::tableName() . '.stop_sell' => 0])
            ->andOnCondition(
                TrAttractionsPrices::tableName() . '.start >= NOW( ) and ' . TrAttractionsPrices::tableName(
                ) . '.any_time=0 or ' . TrAttractionsPrices::tableName(
                ) . '.start >= CURDATE( ) and ' . TrAttractionsPrices::tableName() . '.any_time=1'
            );
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getAvailablePrices()
    {
        return $this->getActivePrices()
            ->andOnCondition(
                [
                    'or',
                    TrAttractionsPrices::tableName() . '.available > 0',
                    TrAttractionsPrices::tableName() . '.free_sell=1'
                ]
            );
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getAvailableSpecialPrices()
    {
        return $this->getAvailablePrices()
            ->andOnCondition(['not', ['special_rate' => false]]);;
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getAvailablePricesByRange()
    {
        $query = $this->getAvailablePrices();

        $range = General::getDatePeriod();

        if (!empty($range->start)) {
            $query->andOnCondition("start >='" . $range->start->format('Y-m-d') . "'");
        }

        if (!empty($range->end)) {
            $query->andOnCondition("start <='" . $range->end->format('Y-m-d 23:59:59') . "'");
        }

        return $query;
    }

    /**
     * @return ActiveQuery
     */
    public static function getAvailable()
    {
        return self::getActive()
            ->distinct()
            ->joinWith('availablePrices', false, 'INNER JOIN');
    }

    /**
     * @return ActiveQuery
     */
    public function getPreview()
    {
        return $this->hasOne(ContentFiles::class, ['id' => 'preview_id']);
    }

    public static function getPriceGroupClass()
    {
        return self::priceGroup;
    }

    public static function getPriceClass()
    {
        return self::priceClass;
    }

    /**
     * @return ActiveQuery
     */
    public static function getActualCategories()
    {
        return TrCategories::find()
            ->joinWith(
                [
                    'trAttractions' => static function (ActiveQuery $query) {
                        $query->joinWith('availablePrices', false, 'INNER JOIN');
                    }
                ],
                false,
                'INNER JOIN'
            )
            ->andOnCondition(self::getConditionActive());
    }

    /**
     * Build query by $Search
     *
     * @param null|\common\models\form\Search $Search
     *
     * @return ActiveQuery
     */
    public static function getByFilter($Search = null)
    {
        if (!$Search) {
            return self::getAvailable();
        }

        $tags = [];
        if ($Search->tags) {
            foreach ($Search->tags as $tag) {
                if (self::TAG_ORIGINAL_ON_SALE !== self::getTagsValue($tag)) {
                    $tags[] = self::getTagsValue($tag);
                }
            }
        }

        if ($Search->without_availability) {
            $query = self::getActive()
                ->distinct()
                ->joinWith('prices', false, 'INNER JOIN');
        } else {
            $query = self::getAvailable()
                ->andFilterWhere(['>=', 'start', $Search->getDateTimeFrom()->format('Y-m-d 00:00:00')])
                ->andFilterWhere(['<=', 'start', $Search->getDateTimeTo()->format('Y-m-d 23:59:59')]);
        }

        if (in_array(self::TAG_USD, $Search->tags ? $Search->tags : [], true)) {
            $query->andFilterWhere(['not', ['special_rate' => false]]);
        }

        if (in_array(self::TAG_USD, $Search->tags ?: [], true)
            || in_array(self::TAG_ORIGINAL_ON_SALE, $Search->tags ?: [], true)) {
            $query->andFilterWhere(['not', ['special_rate' => false]]);
        }

        if (!empty($Search->c) && !empty($Search->c[0])) {
            $query->joinWith('categories')->andWhere(['id_external_category' => $Search->c]);
        }

        if ($Search->title) {
            $query->joinWith('theatre')->andFilterWhere(
                [
                    'or',
                    ['like', self::tableName() . '.name', $Search->title],
                    ['like', TrTheaters::tableName() . '.name', $Search->title]
                ]
            );
        }

        if (!empty($Search->externalIds)) {
            $query->andWhere([self::tableName() . '.id_external' => $Search->externalIds]);
        }

        if (isset($Search->statusWl)) {
            $query->andWhere([self::tableName() . '.status_wl' => $Search->statusWl]);
        }
        if (!empty($Search->alternativeRate)) {
            $query->andWhere(['>', TrAttractionsPrices::tableName() . '.alternative_rate', 0]);
        }

        $query->leftJoin(
            '(' . self::actualMinPrice()
                ->andWhere(['>', 'start', $Search->getDateTimeFrom()->format('Y-m-d 00:00:00')])
                ->andWhere(['<=', 'start', $Search->getDateTimeTo()->format('Y-m-d 23:59:59')])
                ->createCommand()
                ->getRawSql() . ') as ' . self::getAliasMinPrice(),
            self::getAliasMinPrice() . '.id = ' . self::tableName() . '.id'
        );

        $query
            ->andFilterWhere(['or like', self::tableName() . '.location_external_id', $Search->l])
            ->andFilterWhere(['>=', TrAttractionsPrices::tableName() . '.price', $Search->priceFrom])
            ->andFilterWhere(['<=', TrAttractionsPrices::tableName() . '.price', $Search->priceTo])
            ->andFilterWhere(['or like', self::tableName() . '.tags', $tags])
            ->orderby($Search->orderby);

        return $query;
    }

    /**
     * @return ActiveQuery
     */
    public static function actualMinPrice()
    {
//        $queryNoAdult = TrAttractionsPrices::find()
//            ->select([TrAttractionsPrices::tableName() . '.id_external'])
//            ->where(
//                [
//                    'not in',
//                    'id_external',
//                    (new \yii\db\Query())
//                        ->select('p.id_external')
//                        ->from(['p' => TrAttractionsPrices::tableName()])
//                        ->where(['p.name' => 'ADULT'])
//                        ->groupby('p.id_external')
//                ]
//            )
//            ->groupby(TrAttractionsPrices::tableName() . '.id_external');

        return TrAttractionsPrices::find()
            ->joinWith('main')
            ->select(
                [
                    self::tableName() . '.id',
                    self::tableName() . '.id_external',
                    'min_rate' => 'MIN(IF( IF( alternative_rate <> 0, alternative_rate, special_rate ) <> 0, IF( alternative_rate <> 0, alternative_rate, special_rate ), retail_rate ))',
                    'min_rate_source' => 'MIN(retail_rate)',
                ]
            )
            ->andWhere('retail_rate > 0')
            ->groupby(self::tableName() . '.id_external');
    }

    /**
     * @return ActiveQuery
     */
    public function getTrSimilar(): ActiveQuery
    {
        return $this->getTrAttractionsSimilars();
    }

    public function getCalendarEvents(): array
    {
        $scheduleQuery = $this->getAvailablePrices()
            ->joinWith(['allotment'])
            ->select(
                [
                    'concat(' . TrAdmissions::tableName() . '.name, start) as mix_name',
                    'start',
                    'retail_rate',
                    'special_rate',
                    TrAdmissions::tableName() . '.name',
                    TrAdmissions::tableName() . '.id_external',
                    'any_time'
                ]
            )
            ->groupBy('mix_name')
            ->asArray();

        $schedule = $scheduleQuery->all();

        $scheduleSpecialRate = $scheduleQuery->where(['not', ['special_rate' => false]])->all();

        $schedule = ArrayHelper::merge($schedule, $scheduleSpecialRate);

        $schedule = ArrayHelper::index($schedule, 'start');

        return $this->groupCalendarEvents($schedule);
    }
}

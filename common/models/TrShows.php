<?php

namespace common\models;

use common\helpers\General;
use common\models\theaters\TheatersShows;
use common\tripium\Tripium;
use DateInterval;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\Expression;

class TrShows extends _source_TrShows
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

    public const priceClass = TrPrices::class;
    public const joinCategoriesClass = ShowsCategories::class;
    public const photoJoinClass = ShowsPhotoJoin::class;

    public const TYPE_ID = 2;
    
    public const type = 'shows';
    public const TYPE = 'shows';

    public const name = 'Show';
    public const NAME = 'Show';
    public const NAME_PLURAL = 'Shows';

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    public static $type = 'shows';

    public static $name = 'Show';

    public $categoriesClass = ShowsCategories::class;

    public $categoriesClassName = 'ShowsCategories';

    public static $actualMinPriceCash;

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
        return array_merge(
            parent::attributeLabels(),
            [
                'amenities' => 'Amenities',
                'show_in_footer' => 'Display In Footer',
            ]
        );
    }

    /**
     * @return array
     */
    public static function getTagsList()
    {
        return [
            self::TAG_USD => self::TAG_ORIGINAL_ON_SALE,
            self::TAG_PREMIUM => self::TAG_ORIGINAL_PREMIUM,
            self::TAG_FAMILY_PASS => self::TAG_ORIGINAL_FAMILY_PASS,
            self::TAG_LIMITED => self::TAG_ORIGINAL_LIMITED,
        ];
    }

    public function getSourceData()
    {
        $tripium = new Tripium;
        $res = $tripium->getShows($this->updateOnlyIdExternal);
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
                array_merge(['shows/detail'], $options, ['code' => $this->code])
            );
        }
        return Yii::$app->urlManager->createUrl(['shows/detail', 'code' => $this->code]);
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
        return $this->hasMany(Categories::class, ['id_external' => 'id_external_category'])
            ->viaTable(ShowsCategories::tableName(), ['id_external_show' => 'id_external']);
    }

    /**
     * @return ActiveQuery
     */
    function getRelatedCategories()
    {
        return $this->getTrShowsCategories();
    }

    /**
     * @return string
     */
    public static function getType()
    {
        return self::TYPE;
    }

    /**
     * @return ActiveQuery
     */
    public function getPrices()
    {
        return $this->hasMany(TrPrices::class, ['id_external' => 'id_external']);
    }

    /**
     * @return ActiveQuery
     */
    public function getShowsPhoto()
    {
        return $this->hasMany(ShowsPhotoJoin::class, ['item_id' => 'id']);
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
     * @return ActiveQuery
     */
    function getRelatedPhotos()
    {
        return $this->hasMany(ShowsPhotoJoin::class, ['item_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function getConditionActive(): array
    {
        $cond = [self::tableName() . '.status' => self::STATUS_ACTIVE];
        return $cond;
    }

    /**
     * @return ActiveQuery
     */
    public static function getActive(): ActiveQuery
    {
        return self::find()
            ->andOnCondition(self::getConditionActive());
    }

    /**
     * @return ActiveQuery
     */
    public function getActivePrices()
    {
        return $this->getPrices()
            ->andOnCondition([TrPrices::tableName() . '.stop_sell' => 0])
            ->andOnCondition(['>', TrPrices::tableName() . '.start', new Expression('NOW( )')]);
    }

    /**
     * @return ActiveQuery
     */
    public function getActivePricesCutOff()
    {
        return $this->getActivePrices()
            ->andOnCondition(
                [
                    '>',
                    TrPrices::tableName() . '.start',
                    new Expression(
                        'NOW( ) + INTERVAL (main.cut_off) HOUR'
                    )
                ]
            );
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getVacationPackages()
    {
        return $this->hasMany(VacationPackage::class, ['vp_external_id' => 'vp_external_id'])
            ->viaTable(VacationPackageShow::tableName(), ['item_external_id' => 'id_external']);
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
     * @throws Exception
     */
    public function getAvailablePricesByRange()
    {
        return $this->getAvailablePrices()
            ->andOnCondition(
                [
                    '>=',
                    TrPrices::tableName() . '.start',
                    General::getDatePeriod()->start->format('Y-m-d')
                ]
            )
            ->andOnCondition(
                [
                    '<=',
                    TrPrices::tableName() . '.start',
                    General::getDatePeriod()->end->format('Y-m-d 23:59:59')
                ]
            );
    }

    /**
     * @return ActiveQuery
     */
    public function getAvailablePrices()
    {
        return $this->getActivePrices()->andOnCondition(
            [
                'or',
                TrPrices::tableName() . '.available > 0',
                TrPrices::tableName() . '.free_sell=1'
            ]
        );
    }

    /**
     * @return ActiveQuery
     */
    public function getTheatersShows()
    {
        return $this->hasOne(TheatersShows::class, ['id_external' => 'id_external']);
    }

    public static function getPriceClass()
    {
        return self::priceClass;
    }

    /**
     * @return ActiveQuery
     */
    public function getPreview()
    {
        return $this->hasOne(ContentFiles::class, ['id' => 'preview_id']);
    }

    /**
     * @param \common\models\form\Search $Search
     * @param DateInterval           $DateInterval
     *
     * @return ActiveQuery
     */
    public static function getPriceByFilter(\common\models\form\Search $Search, DateInterval $DateInterval)
    {
        $dateTimeFromMax = clone $Search->getDateTimeFrom();
        $dateTimeFromMax->add($DateInterval);

        $query = TrPrices::getAvailable()
            ->select(
                [
                    TrPrices::tableName() . '.id',
                    TrPrices::tableName() . '.id_external',
                    'start',
                    'tr_shows.id_external as main_id_external',
                    'special_rate',
                    'retail_rate',
                    TrPrices::tableName() . '.name',
                    TrPrices::tableName() . '.description',
                ]
            )
            ->joinWith(['main'])
            ->orderby('start')
            ->where(['>=', 'start', $Search->getDateTimeFrom()->format('Y-m-d 00:00:00')])
            ->andWhere(['<', 'start', $dateTimeFromMax->format('Y-m-d 00:00:00')]);
        if ($Search->timeFrom) {
            $query->andFilterWhere(
                ['>=', 'DATE_FORMAT(' . TrPrices::tableName() . '.start,"%k")', (int)$Search->timeFrom]
            );
        }
        if ($Search->timeTo) {
            $query->andFilterWhere(
                [
                    '<=',
                    'DATE_FORMAT(' . TrPrices::tableName() . '.start,"%k")*60 + DATE_FORMAT(' . TrPrices::tableName(
                    ) . '.start,"%i")*1',
                    (int)$Search->timeTo * 60
                ]
            );
        }
        if (!empty($Search->externalIds)) {
            $query->andWhere([self::tableName() . '.id_external' => $Search->externalIds]);
        }
        return $query;
    }

    public static function preparePriceForList($priceAll)
    {
        $priceAll = $priceAll->asArray()->all();

        $tmp = [];
        foreach ($priceAll as $p) {
            $date = strtotime($p['start']);
            if (empty(
                $tmp[$p['main_id_external']][date('Md', $date)][date(
                    'h:iA',
                    $date
                )]
                ) || !empty($p['special_rate'])) {
                $tmp[$p['main_id_external']][date('Md', $date)][date('h:iA', $date)] = $p;
            }
        }

        return $tmp;
    }

    public static function preparePricesForList($priceAll): array
    {
        $priceAll = $priceAll->asArray()->all();
        $tmp = [];
        foreach ($priceAll as $p) {
            $date = strtotime($p['start']);
            unset($p['main']);
            $tmp[$p['main_id_external']][date('Md', $date)][date('h:iA', $date)][] = $p;
        }

        return $tmp;
    }

    /**
     * Return Original Tags Title.
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

    /**
     * @return ActiveQuery
     */
    public function getTrSimilar(): ActiveQuery
    {
        return $this->getTrShowsSimilars();
    }

    /**
     * @return ActiveQuery
     */
    public static function getActualCategories()
    {
        return TrCategories::find()
            ->joinWith(
                [
                    'trShows' => static function (ActiveQuery $query) {
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
     * @param \common\models\form\Search $Search
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
                ->andFilterWhere(
                    ['>=', TrPrices::tableName() . '.start', $Search->getDateTimeFrom()->format('Y-m-d 00:00:00')]
                )
                ->andFilterWhere(
                    ['<=', TrPrices::tableName() . '.start', $Search->getDateTimeTo()->format('Y-m-d 23:59:59')]
                );
        }

        if (in_array(self::TAG_USD, $Search->tags ?: [], true)
            || in_array(self::TAG_ORIGINAL_ON_SALE, $Search->tags ?: [], true)) {
            $query->andFilterWhere(['not', ['special_rate' => false]]);
        }

        if (!empty($Search->c) && !empty($Search->c[0])) {
            $query->joinWith('categories')->andWhere(['id_external_category' => $Search->c]);
        }

        if (!$Search->without_availability && $Search->timeFrom) {
            $query->andFilterWhere(
                ['>=', 'DATE_FORMAT(' . TrPrices::tableName() . '.start,"%k")', (int)$Search->timeFrom]
            );
        }

        if (!$Search->without_availability && $Search->timeTo) {
            $query->andFilterWhere(
                [
                    '<=',
                    'DATE_FORMAT(' . TrPrices::tableName() . '.start,"%k")*60 + DATE_FORMAT(' .
                    TrPrices::tableName() . '.start,"%i")*1',
                    (int)$Search->timeTo * 60
                ]
            );
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

        if (!empty($Search->alternativeRate)) {
            $query->andWhere(['>', TrPrices::tableName() . '.alternative_rate', 0]);
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
            ->andFilterWhere(['>=', TrPrices::tableName() . '.price', $Search->priceFrom])
            ->andFilterWhere(['<=', TrPrices::tableName() . '.price', $Search->priceTo])
            ->andFilterWhere(['or like', self::tableName() . '.tags', $tags])
            ->orderby($Search->getOrderBy());

        return $query;
    }

    /**
     * @return ActiveQuery
     */
    public static function actualMinPrice()
    {
        $queryNoAdult = TrPrices::find()
            ->select(TrPrices::tableName() . '.id_external')
            ->groupby(TrPrices::tableName() . '.id_external')
            ->where(
                [
                    'not in',
                    'id_external',
                    TrPrices::find()
                        ->select(TrPrices::tableName() . '.id_external')
                        ->groupby(TrPrices::tableName() . '.id_external')
                        ->where(['name' => 'ADULT'])
                ]
            );

        if (self::$actualMinPriceCash === null) {
            self::$actualMinPriceCash = $queryNoAdult->asArray()->column();
        }

        return TrPrices::getActive()
            ->joinWith('main')
            ->select(
                [
                    self::tableName() . '.id',
                    'min_rate' => 'MIN(IF( IF( alternative_rate <> 0, alternative_rate, special_rate ) <> 0, IF( alternative_rate <> 0, alternative_rate, special_rate ), retail_rate ))',
                    'min_rate_source' => 'MIN(retail_rate)',
                ]
            )
            ->andWhere('retail_rate > 0')
            ->andWhere(
                [
                    'or',
                    [TrPrices::tableName() . '.name' => 'ADULT'],
                    [self::tableName() . '.id_external' => self::$actualMinPriceCash]
                ]
            )
            ->groupby(self::tableName() . '.id_external');
    }
}

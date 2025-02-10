<?php

namespace common\models;

use common\helpers\General;
use common\models\theaters\TheatersShows;
use common\tripium\Tripium;
use DateInterval;
use DateTime;
use Exception;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

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
//    public const joinCategoriesClass = ShowsCategories::class;
    public const photoJoinClass = ShowsPhotoJoin::class;

    public const TYPE_ID = 2;
    /**
     * @deprecated
     */
    public const type = 'shows';
    public const TYPE = 'shows';
    /**
     * @deprecated
     */
    public const name = 'Show';
    public const NAME = 'Show';
    public const NAME_PLURAL = 'Shows';

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    public const STATUS_WL_ACTIVE = 1;
    public const STATUS_WL_INACTIVE = 0;

    public const WEEKLY_SCHEDULE_ACTIVE = 1;
    public const WEEKLY_SCHEDULE_INACTIVE = 0;

    public const EXTERNAL_SERVICE_SDC = 'SDC';

    /**
     * @deprecated
     */
    public static $type = 'shows';
    /**
     * @deprecated
     */
    public static $name = 'Show';

    public static $actualMinPriceCash;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
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
    public function attributeLabels(): array
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'amenities' => 'Amenities',
                'seat_map_id' => 'Seat Map ID',
                'show_in_footer' => 'Display In Footer',
            ]
        );
    }

    /**
     * @return bool
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function beforeDelete(): bool
    {
        foreach ($this->showBanners as $showBanner) {
            $showBanner->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * @return array
     */
    public static function getTagsList(): array
    {
        return [
            self::TAG_USD => self::TAG_ORIGINAL_ON_SALE,
            self::TAG_PREMIUM => self::TAG_ORIGINAL_PREMIUM,
            self::TAG_FAMILY_PASS => self::TAG_ORIGINAL_FAMILY_PASS,
            self::TAG_LIMITED => self::TAG_ORIGINAL_LIMITED,
        ];
    }

    public function getSourceData(): ?array
    {
        return (new Tripium)->getShows($this->updateOnlyIdExternal);
    }

    /**
     * @param string $code
     * @param string $date
     *
     * @return string
     * @deprecated use getUrl()
     */
    public static function detailURL($code = null, $date = null)
    {
        if ($date) {
            $date = General::formatDateUrlTicket($date);
            return Yii::$app->urlManager->createUrl(['shows/tickets', 'code' => $code, 'date' => $date, '#' => 'm']);
        }

        if ($code) {
            return Yii::$app->urlManager->createUrl(['shows/detail', 'code' => $code]);
        }

        return Yii::$app->urlManager->createUrl(['shows/index']);
    }

    /**
     * Return item url.
     *
     * @param mixed  $options
     * @param string $urlManager
     *
     * @return string
     */
    public function getUrl($options = null, $urlManager = 'urlManager'): string
    {
        if ($options instanceof DateTime) {
            return Yii::$app->{$urlManager}->createUrl(
                ['shows/tickets', 'code' => $this->code, 'date' => $options->format('Y-m-d_H:i:s')]
            );
        }
        if (is_array($options)) {
            return Yii::$app->{$urlManager}->createUrl(
                array_merge(['shows/detail'], $options, ['code' => $this->code])
            );
        }
        return Yii::$app->{$urlManager}->createUrl(['shows/detail', 'code' => $this->code]);
    }

    /**
     * Return list url.
     *
     * @return string
     */
    public static function getListUrl(): string
    {
        return Url::to(['shows/index']);
    }

    /**
     * Return item description url.
     *
     * @return string
     */
    public function getUrlDescription(): string
    {
        return Yii::$app->urlManager->createUrl(['shows/description', 'code' => $this->code]);
    }

    /**
     * Return item tickets url
     *
     * @param array $params
     *
     * @return string
     */
    public function getUrlTicket($params = []): string
    {
        if (!empty($params['date']) && $params['date'] instanceof DateTime) {
            $params['date'] = $params['date']->format('Y-m-d_H:i:s');
        } elseif (!empty($params['date'])) {
            $params['date'] = str_replace(' ', '_', $params['date']);
        }
        return Yii::$app->urlManager->createUrl(array_merge(['shows/tickets', 'code' => $this->code], $params));
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
    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(TrCategories::class, ['id_external' => 'id_external_category'])
            ->viaTable(TrShowsCategories::tableName(), ['id_external_show' => 'id_external']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRelatedCategories(): ActiveQuery
    {
        return $this->getTrShowsCategories();
    }

    /**
     * @return ActiveQuery
     */
    public static function getAllCategories(): ActiveQuery
    {
        return TrShowsCategories::find()->joinWith('externalShow', false, 'INNER JOIN');
    }

    /**
     * @return string
     * @deprecated Use Class::TYPE or self::TYPE
     */
    public static function getType()
    {
        return self::TYPE;
    }

    /**
     * @return ActiveQuery
     */
    public function getPrices(): ActiveQuery
    {
        return $this->hasMany(TrPrices::class, ['id_external' => 'id_external']);
    }

    /**
     * @return ActiveQuery
     */
    public function getShowsPhoto(): ActiveQuery
    {
        return $this->hasMany(ShowsPhotoJoin::class, ['item_id' => 'id']);
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
    public function getRelatedPhotos(): ActiveQuery
    {
        return $this->hasMany(ShowsPhotoJoin::class, ['item_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function getConditionActive(): array
    {
        return [self::tableName() . '.status' => self::STATUS_ACTIVE];
    }

    /**
     * @return ActiveQuery
     */
    public static function getActive(): ActiveQuery
    {
        return self::find()->andOnCondition(self::getConditionActive());
    }

    /**
     * @return ActiveQuery
     */
    public function getActivePrices(): ActiveQuery
    {
        return $this->getPrices()
            ->andOnCondition([TrPrices::tableName() . '.stop_sell' => 0])
            ->andOnCondition(['>', TrPrices::tableName() . '.start', new Expression('NOW( )')]);
    }

    /**
     * @return ActiveQuery
     */
    public function getActivePricesCutOff(): ActiveQuery
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
     */
    public function getAvailablePrices(): ActiveQuery
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
    public function getAvailableSpecialPrices(): ActiveQuery
    {
        return $this->getAvailablePrices()
            ->andOnCondition(['not', ['special_rate' => false]]);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getVacationPackages(): ActiveQuery
    {
        return $this->hasMany(VacationPackage::class, ['vp_external_id' => 'vp_external_id'])
            ->viaTable(VacationPackageShow::tableName(), ['item_external_id' => 'id_external']);
    }

    /**
     * @return ActiveQuery
     */
    public static function getAvailable(): ActiveQuery
    {
        return self::getActive()
            ->distinct()
            ->joinWith('availablePrices', false, 'INNER JOIN');
    }

    /**
     * @return ActiveQuery
     * @throws Exception
     */
    public function getAvailablePricesByRange(): ActiveQuery
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
     * Return Tags Title.
     *
     * @return array
     *
     * @deprecated Use `getOriginalTagTitleList()`
     */
    /*public static function getTagTitleList()
    {
        return [
            self::TAG_USD => 'On Sale',
            self::TAG_PREMIUM => 'Premium',
            self::TAG_FEATURED => 'Featured',
            self::TAG_FAMILY_PASS => 'Family Pass',
            self::TAG_LIMITED => 'Limited Engagement',
        ];
    }*/

    /**
     * Return Tag Title.
     *
     * @return string
     *
     * @deprecated Use `getOriginalTagTitleValue`
     */
    /*public static function getTagTitleValue($val)
    {
        $ar = self::getTagTitleList();

        return isset($ar[$val]) ? $ar[$val] : $val;
    }*/

    /**
     * Return Original Tags Title.
     *
     * @return array
     */
    public static function getOriginalTagTitleList(): array
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
     * Return Original Tag Title.
     *
     * @return string
     */
    public static function getOriginalTagTitleValue($val)
    {
        $ar = self::getOriginalTagTitleList();

        return $ar[$val] ?? $val;
    }

    /**
     * Return Original Tags Title.
     *
     * @return array
     */
    public static function getWeeklyScheduleList(): array
    {
        return [
            self::WEEKLY_SCHEDULE_ACTIVE => 'Active',
            self::WEEKLY_SCHEDULE_INACTIVE => 'Inactive',
        ];
    }

    /**
     * Return Original Tag Title.
     *
     * @param $val
     *
     * @return string
     */
    public static function getWeeklyScheduleValue($val): string
    {
        $ar = self::getWeeklyScheduleList();

        return $ar[$val] ?? $val;
    }

    /**
     * Return Original Tags Icon.
     *
     * @return array
     */
    public static function getOriginalTagIcon(): array
    {
        return [
            self::TAG_ORIGINAL_ON_SALE => 'dollar-sign',
            self::TAG_ORIGINAL_PREMIUM => 'star',
            self::TAG_ORIGINAL_FEATURED => 'star',
            self::TAG_ORIGINAL_FAMILY_PASS => 'users',
            self::TAG_ORIGINAL_LIMITED => 'clock',
        ];
    }

    /**
     * Return Original Tag Icon.
     *
     * @param $val
     *
     * @return string
     */
    public static function getOriginalTagIconValue($val)
    {
        $ar = self::getOriginalTagIcon();

        return $ar[$val] ?? $val;
    }

    /**
     * @param $ids_external
     * @param $removeText
     *
     * @return bool
     */
    public static function fixName($ids_external, $removeText): bool
    {
        if (empty($removeText)) {
            return false;
        }

        $shows = self::find()->where(['id_external' => $ids_external])->all();

        foreach ($shows as $show) {
            $code = str_replace('---', '-', $show->code);
            $code = str_replace('--', '-', $code);
            $show->name = str_replace($removeText, '', $show->name);
            $show->save();
            $show->code = $code;
            $show->save();
        }

        return true;
    }

    /**
     * @return ActiveQuery
     */
    public function getTheatersShows(): ActiveQuery
    {
        return $this->hasOne(TheatersShows::class, ['id_external' => 'id_external']);
    }

    /**
     * @param null $code
     *
     * @return ActiveQuery
     */
    public static function getAllowable($code = null): ActiveQuery
    {
        $Theater = \common\models\theaters\Theaters::getAllowable()->one();

        if (!empty($Theater)) {
            $ShowsIDs = TheatersShows::find()
                ->select(['id_external'])
                ->where(['theater_id' => $Theater->id])
                ->asArray()
                ->column();
        } else {
            $ShowsIDs = 0;
        }
        if ($code) {
            return self::find()->where(['code' => $code, self::tableName() . '.id_external' => $ShowsIDs]);
        }

        return self::find()->where([self::tableName() . '.id_external' => $ShowsIDs]);
    }

    public static function getPriceClass()
    {
        return self::priceClass;
    }

    /**
     * @return ActiveQuery
     */
    public function getPreview(): ActiveQuery
    {
        return $this->hasOne(ContentFiles::class, ['id' => 'preview_id']);
    }

    /**
     * @return ActiveQuery
     */
    public static function getActualCategories(): ActiveQuery
    {
        return TrCategories::find()
            ->joinWith(
                [
                    'trShows' => static function (ActiveQuery $query) {
//                        $query->joinWith('availablePrices', false, 'INNER JOIN');
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
    public static function getByFilterWithOutDate($Search = null): ActiveQuery
    {
        $subQuery = TrPrices::find()->select(['id_external'])->groupBy('id_external');

        $query = self::getActive();
        $query->innerJoin(['price' => $subQuery], self::tableName() . '.id_external = price.id_external');

        if ($Search->title) {
            $query->joinWith('theatre')->andFilterWhere(
                [
                    'or',
                    ['like', self::tableName() . '.name', $Search->title],
                    ['like', TrTheaters::tableName() . '.name', $Search->title]
                ]
            );
        }

        return $query;
    }

    /**
     * Build query by $Search
     *
     * @param \common\models\form\Search $Search
     *
     * @return ActiveQuery
     */
    public static function getByFilter($Search = null): ActiveQuery
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

        if (isset($Search->statusWl)) {
            $query->andWhere([self::tableName() . '.status_wl' => $Search->statusWl]);
        }
        if (!empty($Search->alternativeRate)) {
            $query->andWhere(['>', TrPrices::tableName() . '.alternative_rate', 0]);
        }

//        $query->joinWith('locationItem');

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
//            ->orderby($Search->getOrderBy())
        ;

        return $query;
    }

    /**
     * @return ActiveQuery
     */
    public static function actualMinPrice(): ActiveQuery
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

    /**
     * @param \common\models\form\Search $Search
     * @param DateInterval           $DateInterval
     *
     * @return ActiveQuery
     */
    public static function getPriceByFilter(\common\models\form\Search $Search, DateInterval $DateInterval): ActiveQuery
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

    public static function preparePriceForList($priceAll): array
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
     * @return ActiveQuery
     */
    public function getTrSimilar(): ActiveQuery
    {
        return $this->getTrShowsSimilars();
    }

    public function getCalendarEvents(bool $booking = false): array
    {
        $scheduleQuery = $this->getAvailablePrices()
            ->select(['start', 'retail_rate', 'special_rate'])
            ->groupBy(['start', 'retail_rate', 'special_rate'])
            ->orderby('special_rate')
            ->asArray();

        $schedule = $scheduleQuery->all();

        $scheduleSpecialRate = $scheduleQuery->where(['not', ['special_rate' => false]])->all();

        $schedule = ArrayHelper::merge($schedule, $scheduleSpecialRate);

        $schedule = ArrayHelper::index($schedule, 'start');

        return $this->groupCalendarEvents($schedule, $booking);
    }
}

<?php

namespace common\models;

use common\helpers\General;
use common\models\theaters\TheatersShows;
use common\tripium\Tripium;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\Expression;

class TrShows extends _source_TrShows
{
    use ItemsExtensionTrait;

    public const TAG_ORIGINAL_FEATURED = 'Featured';

    public const CALL_US_TO_BOOK_YES = 1;
    public const CALL_US_TO_BOOK_NO = 0;
    
    public const type = 'shows';
    public const TYPE = 'shows';

    public const name = 'Show';
    public const NAME = 'Show';
    public const NAME_PLURAL = 'Shows';

    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

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

    public function getSourceData()
    {
        $tripium = new Tripium;
        $res = $tripium->getShows($this->updateOnlyIdExternal);
        $this->statusCodeTripium = $tripium->statusCode;
        return $res;
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

    /**
     * @return ActiveQuery
     */
    public function getPreview()
    {
        return $this->hasOne(ContentFiles::class, ['id' => 'preview_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTrSimilar(): ActiveQuery
    {
        return $this->getTrShowsSimilars();
    }
}

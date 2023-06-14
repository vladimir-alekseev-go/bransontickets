<?php

namespace common\models;

use common\models\redirects\Redirects;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\Inflector;

class SiteSection extends _source_SiteSection
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    public const SECTION_SHOWS = 'shows';
    /*public const SECTION_ATTRACTIONS = 'attractions';
    public const SECTION_LUNCH = 'lunchs';
    public const SECTION_HOTELS = 'hotels'; // Pos hotels
    public const SECTION_LODGING = 'lodging'; // Price line hotels
    public const SECTION_BLOG = 'blog';
    public const SECTION_PACKAGES = 'packages';
    public const SECTION_DEALS = 'deals';
    public const SECTION_CONTACT_US = 'contact-us';*/

    public $saveItemsUrl;

    /**
     * @param object $item
     *
     * @return string|null
     */
    public static function getSectionByObject($item)
    {
        foreach(self::getSectionClassesList() as $section => $class) {
            if ($item instanceof $class) {
                return $section;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    'url',
                    'filter',
                    'filter' => static function ($value) {
                        return Inflector::slug($value);
                    }
                ],
                ['saveItemsUrl', 'safe']
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'code' => [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'url',
                'ensureUnique' => true,
                'immutable' => false,
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        /**
         * @var TrShows|TrAttractions|TrLunchs|TrPosHotels|TrPosPlHotels|VacationPackage $class
         * @var TrShows[]|TrAttractions[]|TrLunchs[]|TrPosHotels[]|TrPosPlHotels[]|VacationPackage[] $items
         */
        parent::afterSave($insert, $changedAttributes);
        if ($this->saveItemsUrl && !empty($changedAttributes['url'])) {
            $class = self::getSectionClass($this->section);
            $category = array_flip(Redirects::categoriesClasses())[$class] ?? null;
            if ($category) {
                $items = $class::getActive()->all();
                foreach ($items as $item) {
                    $redirect = new Redirects(
                        [
                            'status_code' => '301',
                            'old_url' => '/' . $changedAttributes['url'] . '/' . $item->code . '/',
                            'category' => $category,
                            'item_id' => $item->vp_external_id ?? $item->id_external,
                        ]
                    );
                    $redirect->save();
                }
            }
        }
    }

    /**
     * Return status list
     *
     * @return array
     */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
        ];
    }

    /**
     * Return status value
     *
     * @param $val
     *
     * @return string
     */
    public static function getStatusValue($val): string
    {
        $ar = self::getStatusList();
        return $ar[$val] ?? $val;
    }

    /**
     * @return array|string[]
     */
    public static function getSectionClassesList(): array
    {
        return [
            self::SECTION_SHOWS => TrShows::class,
            /*self::SECTION_ATTRACTIONS => TrAttractions::class,
            self::SECTION_LUNCH => TrLunchs::class,
            self::SECTION_HOTELS => TrPosHotels::class,
            self::SECTION_LODGING => TrPosPlHotels::class,
            self::SECTION_PACKAGES => VacationPackage::class,*/
        ];
    }

    /**
     * @param $section
     *
     * @return string
     */
    public static function getSectionClass($section): string
    {
        $ar = self::getSectionClassesList();
        return $ar[$section] ?? $section;
    }

    /**
     * Return status list
     *
     * @return array
     */
    public static function getSectionList(): array
    {
        return [
            self::SECTION_SHOWS => 'Shows',
//            self::SECTION_ATTRACTIONS => 'Attractions',
//            self::SECTION_LUNCH => 'Dining',
//            self::SECTION_LODGING => 'Lodging',
//            self::SECTION_BLOG => 'Blog',
//            self::SECTION_PACKAGES => 'Packages',
//            self::SECTION_DEALS => 'Deals',
//            self::SECTION_CONTACT_US => 'Contact Us',
        ];
    }

    /**
     * Return status value
     *
     * @param string $val
     *
     * @return string
     */
    public static function getSectionValue($val): string
    {
        $ar = self::getSectionList();
        return $ar[$val] ?? $val;
    }
}

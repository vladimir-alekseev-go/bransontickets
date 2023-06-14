<?php

namespace common\models\redirects;

use common\models\TrAttractions;
use common\models\TrLunchs;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use common\models\TrShows;
use common\models\VacationPackage;
use DateTime;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class Redirects extends _source_Redirects
{
    public const CATEGORY_SHOW = 'show';
    public const CATEGORY_ATTRACTION = 'attraction';
    public const CATEGORY_LUNCH = 'lunch';
    public const CATEGORY_HOTEL_POS = 'hotel-pos';
    public const CATEGORY_HOTEL_PRICE_LINE = 'hotel-pl';
    public const CATEGORY_LINK = 'link';
    public const CATEGORY_VACATION_PACKAGE = 'vacation-package';
    public const SITE_GRANDCOUNTRY = 'grandcountry';

    /**
     * @param object $item
     *
     * @return string|null
     */
    public static function getCategoryByObject($item)
    {
        foreach(self::categoriesClasses() as $section => $class) {
            if ($item instanceof $class) {
                return $section;
            }
        }

        return null;
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'created_at',
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return array|string[]
     */
    public static function categoriesClasses(): array
    {
        return [
            self::CATEGORY_SHOW => TrShows::class,
            self::CATEGORY_ATTRACTION => TrAttractions::class,
            self::CATEGORY_LUNCH => TrLunchs::class,
            self::CATEGORY_HOTEL_POS => TrPosHotels::class,
            self::CATEGORY_HOTEL_PRICE_LINE => TrPosPlHotels::class,
            self::CATEGORY_VACATION_PACKAGE => VacationPackage::class,
        ];
    }

    /**
     * @param string $category
     *
     * @return string|null
     */
    public static function getClass(string $category): ?string
    {
        return self::categoriesClasses()[$category] ?? null;
    }

    /**
     * @param string $relativeUrl
     *
     * @return Redirects|ActiveRecord|null
     */
    public static function findUrl(string $relativeUrl)
    {
        $baseUrl = self::getBaseUrl($relativeUrl);
        if ($baseUrl) {
            return self::find()->where(['old_url' => $baseUrl])->one();
        }
        return null;
    }

    public function getUrl(string $currentUrl, $site = null): ?string
    {
        /**
         * @var TrShows|TrAttractions|TrLunchs|TrPosHotels|TrPosPlHotels|VacationPackage $item
         */

        if ($this->category === self::CATEGORY_LINK) {
            return $this->new_url;
        }

        $class = self::getClass($this->category);
        if ($class === VacationPackage::class) {
            $item = $class::getActive()->where(['vp_external_id' => $this->item_id])->one();
        } else {
            $item = $class::getActive()->where(['id_external' => $this->item_id])->one();
        }
        if ($item && self::getBaseUrl($currentUrl) !== self::getBaseUrl($item->getUrl())) {
            $url = $site === self::SITE_GRANDCOUNTRY ? $item->getUrlBooking(new DateTime()) : $item->getUrl();
            if (self::getUrlEnd($currentUrl)) {
                return self::getBaseUrl($url) . self::getUrlEnd($currentUrl);
            }

            return $url;
        }
        return null;
    }

    public function getStatusCode(): string
    {
        return $this->status_code;
    }

    private static function getBaseUrl(string $url)
    {
        $arUrl = explode('/', $url);
        return count($arUrl) >= 3 ? '/' . $arUrl[1] . '/' . $arUrl[2] . '/' : null;
    }

    private static function getUrlEnd(string $url)
    {
        $arUrl = explode('/', $url);
        array_shift($arUrl);
        array_shift($arUrl);
        array_shift($arUrl);
        return count($arUrl) > 0 ? implode('/', $arUrl) : null;
    }

    private static function urlExist(string $url): bool
    {
        $headers = @get_headers($url);

        return $headers && strpos($headers[0], '200');
    }
}

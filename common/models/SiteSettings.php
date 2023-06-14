<?php

namespace common\models;

use common\tripium\Tripium;
use common\widgets\styleAsset\StyleAsset;
use DateTime;
use Exception;
use Yii;
use yii\helpers\Json;

class SiteSettings extends _source_SiteSettings
{
    public const FONT_OPENSANS_MERRIWEATHER = 'opensans_merriweather';
    public const FONT_ROBOTO_RUBIK = 'roboto_rubik';
    public const FONT_LATO_BIORHYME = 'lato_biorhyme';
    public const FONT_ROBOTO_IBMPLEXSANS = 'roboto_ibmplexsans';
    public const FONT_OPENSANS_SPECTRAL = 'opensans_spectral';
    public const COLOR_ANCHOR_COLOR_BRAND = 'color_brand';
    public const COLOR_ANCHOR_COLOR_BRAND_LIGHTEN_30 = 'color_brand_lighten_30';
    public const COLOR_ANCHOR_COLOR_BRAND_DARKEN_10 = 'color_brand_darken_10';
    public const LIST_TYPE_LIST = 'list';
    public const LIST_TYPE_GRID = 'grid';

    /**
     * @var SiteSettings $dataSiteSettings
     */
    public static $dataSiteSettings;
    public $attachVoucher;

    public function init()
    {
        parent::init();
        self::getSiteSettings();
    }

    public static function getSiteSettings(): ?SiteSettings
    {
        if (!empty(self::$dataSiteSettings)) {
            return self::$dataSiteSettings;
        }
        if (!empty(Yii::$app->siteSettings)) {
            self::$dataSiteSettings = Yii::$app->siteSettings->data;
        }
        return self::$dataSiteSettings;
    }

    public function setAttachVoucherFromTripium(): void
    {
        $tripium = new Tripium();
        $config = $tripium->getLocationConfigWl();

        if ($config !== null && isset($config['attachVoucher'])) {
            $this->attachVoucher = (bool)$config['attachVoucher'];
        }
    }

    public function save($runValidation = true, $attributeNames = null): bool
    {
        $tripium = new Tripium();
        $tripium->setLocationConfigWl(
            [
                'attachVoucher' => $this->attachVoucher ? 'true' : 'false'
            ]
        );

        return parent::save($runValidation, $attributeNames);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                ['email', 'email'],
                ['attachVoucher', 'boolean'],
                [['default_date', 'hotel_default_date'], 'filter', 'filter' => [$this, 'defaultDateFormat']],
                [self::getListViewBooleanParameters(), 'boolean'],
                [self::getListViewStringParameters(), 'string'],
                [
                    ['displayShowsMap', 'displayAttractionsMap', 'displayLunchsMap', 'displayHotelMap'],
                    'displayMapValidator'
                ],
                [
                    ['google_map_key', 'google_map_server_key'],
                    'required',
                    'enableClientValidation' => false,
                    'when' => function ($model) {
                        return $this->getDisplayShowsMap() || $this->getDisplayAttractionsMap()
                            || $this->getDisplayLunchsMap() || $this->getDisplayHotelMap();
                    },
                    'message' => 'This key can not be blank or you have to switch off Displaying Map'
                ],
            ]
        );
    }

    public function defaultDateFormat($attribute)
    {
        try {
            return (new DateTime($attribute))->format('Y-m-d');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Validate display map
     *
     * @param $attribute
     */
    public function displayMapValidator($attribute): void
    {
        if (!empty($this->{$attribute}) && empty($this->google_map_key)) {
            $this->addError($attribute, "Need to enter the Google Map Key");
        }
        if (!empty($this->{$attribute}) && empty($this->google_map_server_key)) {
            $this->addError($attribute, "Need to enter the Google Map Server Key");
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (isset($changedAttributes[self::COLOR_ANCHOR_COLOR_BRAND])
            || isset($changedAttributes[self::COLOR_ANCHOR_COLOR_BRAND_LIGHTEN_30])
            || isset($changedAttributes[self::COLOR_ANCHOR_COLOR_BRAND_DARKEN_10])) {
            $this->rebuildCssFile(StyleAsset::CSS_FILE_NAME);
        }
    }

    /**
     * Generate css color file
     *
     * @param string $file
     */
    public function rebuildCssFile($file): void
    {
        $cssTemplateFile = Yii::getAlias('@common/widgets/styleAsset/source/' . $file);
        $cssFile = Yii::getAlias('@common/widgets/styleAsset/assets/css/' . $file . '.css');

        if (file_exists($cssTemplateFile)) {
            $str = file_get_contents($cssTemplateFile);

            foreach (self::getAnchorsList() as $anchor => $name) {
                $str = str_replace('@' . $anchor . ';', $this->{$anchor} . ';', $str);
                $str = str_replace('@' . $anchor . ' !important;', $this->{$anchor} . ' !important;', $str);
            }

            $fp = fopen($cssFile, 'w+');
            fwrite($fp, $str);
            fclose($fp);
        }
    }

    /**
     * Return data of this model. It can be created only 1 row
     *
     * @return SiteSettings|null
     */
    public static function getData(): ?SiteSettings
    {
        return self::find()->one();
    }

    /**
     * Return color anchor list
     *
     * @return array
     */
    public static function getAnchorsList(): array
    {
        return [
            self::COLOR_ANCHOR_COLOR_BRAND => self::COLOR_ANCHOR_COLOR_BRAND,
            self::COLOR_ANCHOR_COLOR_BRAND_LIGHTEN_30 => self::COLOR_ANCHOR_COLOR_BRAND_LIGHTEN_30,
            self::COLOR_ANCHOR_COLOR_BRAND_DARKEN_10 => self::COLOR_ANCHOR_COLOR_BRAND_DARKEN_10
        ];
    }

    /**
     * Return fonts list
     *
     * @return array
     */
    public static function getFontsList(): array
    {
        return [
            self::FONT_OPENSANS_MERRIWEATHER => 'Open Sans and Merriweather',
            self::FONT_ROBOTO_RUBIK => 'Roboto and Rubik',
            self::FONT_LATO_BIORHYME => 'Lato and BioRhyme',
            self::FONT_ROBOTO_IBMPLEXSANS => 'Roboto and IBM Plex Sans',
            self::FONT_OPENSANS_SPECTRAL => 'Open Sans and Spectral',
        ];
    }

    /**
     * Return fonts value
     *
     * @param string $val
     *
     * @return string
     */
    public static function getFontsValue($val): string
    {
        $ar = self::getFontsList();
        return $ar[$val] ?? $val;
    }

    /**
     * @return string[]
     */
    public static function getTypesList(): array
    {
        return [
            self::LIST_TYPE_LIST => 'List',
            self::LIST_TYPE_GRID => 'Grid',
        ];
    }

    /**
     * Return fonts data list
     *
     * @return array
     */
    public static function getFontsDataList(): array
    {
        return [
            self::FONT_OPENSANS_MERRIWEATHER => [
                'css' => [
                    'css/font_' . self::FONT_OPENSANS_MERRIWEATHER . '_style.css',
                    'https://fonts.googleapis.com/css?family=Open+Sans:400,800',
                    'https://fonts.googleapis.com/css?family=Merriweather:400,600'
                ]
            ],
            self::FONT_ROBOTO_RUBIK => [
                'css' => [
                    'css/font_' . self::FONT_ROBOTO_RUBIK . '_style.css',
                    'https://fonts.googleapis.com/css?family=Roboto:400,800',
                    'https://fonts.googleapis.com/css?family=Rubik:400,600',
                ]
            ],
            self::FONT_LATO_BIORHYME => [
                'css' => [
                    'css/font_' . self::FONT_LATO_BIORHYME . '_style.css',
                    'https://fonts.googleapis.com/css?family=Lato:400,800',
                    'https://fonts.googleapis.com/css?family=BioRhyme:400,600',
                ]
            ],
            self::FONT_ROBOTO_IBMPLEXSANS => [
                'css' => [
                    'css/font_' . self::FONT_ROBOTO_IBMPLEXSANS . '_style.css',
                    'https://fonts.googleapis.com/css?family=Roboto:400,800',
                    'https://fonts.googleapis.com/css?family=IBM+Plex+Sans:400,600',
                ]
            ],
            self::FONT_OPENSANS_SPECTRAL => [
                'css' => [
                    'css/font_' . self::FONT_OPENSANS_SPECTRAL . '_style.css',
                    'https://fonts.googleapis.com/css?family=Open+Sans:400,800',
                    'https://fonts.googleapis.com/css?family=Spectral:400,600',
                ]
            ],
        ];
    }

    /**
     * Return fonts data
     *
     * @param $val
     *
     * @return string
     */
    public static function getFontsData($val)
    {
        $ar = self::getFontsDataList();
        return $ar[$val] ?? $val;
    }

    /**
     * Return value of parameter view
     *
     * @param $name
     *
     * @return mixed
     */
    public function getViewParameter($name)
    {
        if (!empty($this->view_params)) {
            $viewParams = Json::decode($this->view_params);
            if (!empty($viewParams[$name])) {
                return $viewParams[$name];
            }
        }
        return null;
    }

    /**
     * Set value of parameter view
     *
     * @param string $name
     * @param        $value
     */
    public function setViewParameter($name, $value): void
    {
        $viewParams = null;
        if (empty($this->view_params)) {
            $viewParams = [];
        } else {
            $viewParams = Json::decode($this->view_params);
        }
        $viewParams[$name] = $value;
        foreach ($viewParams as $k => $v) {
            if (!in_array($k, self::getListViewParameters(), false)) {
                unset($viewParams[$k]);
            }
        }

        $this->view_params = Json::encode($viewParams);
    }

    /**
     * List of dynamic view parameters
     *
     * @return string[]
     */
    public static function getListViewParameters(): array
    {
        return array_merge(self::getListViewBooleanParameters(), self::getListViewStringParameters());
    }

    /**
     * List of dynamic view strings parameters
     *
     * @return string[]
     */
    public static function getListViewStringParameters(): array
    {
        return [
            'defaultShowsListType',
            'defaultAttractionListType',
            'defaultLunchListType',
            'defaultHotelListType',
        ];
    }

    /**
     * List of dynamic view boolean parameters
     *
     * @return string[]
     */
    public static function getListViewBooleanParameters(): array
    {
        return [
            'displayShowsSortPanel',
            'displayShowsDisplayPanel',
            'displayShowsFilterSearch',
            'displayShowsFilterDate',
            'displayShowsFilterTime',
            'displayShowsFilterPrice',
            'displayShowsFilterTag',
            'displayShowsFilterCategory',
            'displayShowsFilterLocation',
            'displayShowsMap',
            'displayAttractionsSortPanel',
            'displayAttractionsDisplayPanel',
            'displayAttractionsFilterSearch',
            'displayAttractionsFilterDate',
            'displayAttractionsFilterTime',
            'displayAttractionsFilterPrice',
            'displayAttractionsFilterTag',
            'displayAttractionsFilterCategory',
            'displayAttractionsFilterLocation',
            'displayAttractionsMap',
            'displayLunchsSortPanel',
            'displayLunchsDisplayPanel',
            'displayLunchsFilterSearch',
            'displayLunchsFilterDate',
            'displayLunchsFilterTime',
            'displayLunchsFilterPrice',
            'displayLunchsFilterTag',
            'displayLunchsFilterCategory',
            'displayLunchsFilterLocation',
            'displayLunchsMap',
            'displayPackagesFilterSearch',
            'displayPackagesFilterDate',
            'displayPackagesFilterPrice',
            'displayPackagesFilterCategory',
            'displayMainPageBanners',
            'displayMainPageFilters',
            'displayMainPageInfoBlock',
            'displayMainPageGeneralBlock',
            'displayMainPageFeatured',
            'displayMainPageParnters',
            'displayHotelFilterSearch',
            'displayHotelFilterDate',
            'displayHotelFilterPrice',
            'displayHotelMap',
            'displayHotelRoomsGuests',
            'displayHotelStar',
            'displayHotelCity',
            'displayHotelAmenity',
            'displayHotelDisplayPanel',
            'displayHotelSortPanel',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'displayShowsSortPanel' => 'Display The Sort Panel',
                'displayShowsDisplayPanel' => 'Display The Display Panel',
                'displayShowsFilterSearch' => 'Display The Search In Filter',
                'displayShowsFilterDate' => 'Display The Date Period In Filter',
                'displayShowsFilterTime' => 'Display The Time Period In Filter',
                'displayShowsFilterPrice' => 'Display The Price Range In Filter',
                'displayShowsFilterTag' => 'Display Tags In Filter',
                'displayShowsFilterCategory' => 'Display Categories In Filter',
                'displayShowsFilterLocation' => 'Display Locations In Filter',
                'displayShowsMap' => 'Display Map',
                'displayAttractionsSortPanel' => 'Display The Sort Panel',
                'displayAttractionsDisplayPanel' => 'Display The Display Panel',
                'displayAttractionsFilterSearch' => 'Display The Search In Filter',
                'displayAttractionsFilterDate' => 'Display The Date Period In Filter',
                'displayAttractionsFilterTime' => 'Display The Time Period In Filter',
                'displayAttractionsFilterPrice' => 'Display The Price Range In Filter',
                'displayAttractionsFilterTag' => 'Display Tags In Filter',
                'displayAttractionsFilterCategory' => 'Display Categories In Filter',
                'displayAttractionsFilterLocation' => 'Display Locations In Filter',
                'displayAttractionsMap' => 'Display Map',
                'displayLunchsSortPanel' => 'Display The Sort Panel',
                'displayLunchsDisplayPanel' => 'Display The Display Panel',
                'displayLunchsFilterSearch' => 'Display The Search In Filter',
                'displayLunchsFilterDate' => 'Display The Date Period In Filter',
                'displayLunchsFilterTime' => 'Display The Time Period In Filter',
                'displayLunchsFilterPrice' => 'Display The Price Range In Filter',
                'displayLunchsFilterTag' => 'Display Tags In Filter',
                'displayLunchsFilterCategory' => 'Display Categories In Filter',
                'displayLunchsFilterLocation' => 'Display Locations In Filter',
                'displayLunchsMap' => 'Display Map',
                'displayPackagesFilterSearch' => 'Display The Search In Filter',
                'displayPackagesFilterDate' => 'Display The Date Period In Filter',
                'displayPackagesFilterPrice' => 'Display The Price Range In Filter',
                'displayPackagesFilterCategory' => 'Display Categories In Filter',
                'displayMainPageBanners' => 'Display Banners on The Main Page',
                'displayMainPageFilters' => 'Display Filters on The Main Page',
                'displayMainPageInfoBlock' => 'Display The Info-Block on The Main Page',
                'displayMainPageGeneralBlock' => 'Display The General-Block on The Main Page',
                'displayMainPageFeatured' => 'Display The Featured on The Main Page',
                'displayMainPageParnters' => 'Display Parnters on The Main Page',
                'displayHotelFilterSearch' => 'Display The Search In Filter',
                'displayHotelFilterDate' => 'Display The Date Period In Filter',
                'displayHotelFilterPrice' => 'Display The Price Range In Filter',
                'displayHotelMap' => 'Display Map',
                'displayHotelRoomsGuests' => 'Display Manage Rooms & Guests',
                'displayHotelStar' => 'Display Stars',
                'displayHotelCity' => 'Display Cities',
                'displayHotelAmenity' => 'Display Amenities',
                'displayHotelDisplayPanel' => 'Display The Display Panel',
                'displayHotelSortPanel' => 'Display The Sort Panel',
            ]
        );
    }

    /**
     * Return value of displayShowsSortPanel
     *
     * @return bool
     */
    public function getDisplayShowsSortPanel(): bool
    {
        return (bool)$this->getViewParameter('displayShowsSortPanel');
    }

    /**
     * Set value of displayShowsSortPanel
     *
     * @param $value
     */
    public function setDisplayShowsSortPanel($value): void
    {
        $this->setViewParameter('displayShowsSortPanel', $value);
    }

    /**
     * Return value of displayShowsDisplayPanel
     *
     * @return bool
     */
    public function getDisplayShowsDisplayPanel(): bool
    {
        return (bool)$this->getViewParameter('displayShowsDisplayPanel');
    }

    /**
     * Set value of displayShowsDisplayPanel
     *
     * @param $value
     */
    public function setDisplayShowsDisplayPanel($value): void
    {
        $this->setViewParameter('displayShowsDisplayPanel', $value);
    }

    /**
     * Return value of displayShowsFilterSearch
     *
     * @return bool
     */
    public function getDisplayShowsFilterSearch()
    {
        return (bool)$this->getViewParameter('displayShowsFilterSearch');
    }

    /**
     * Set value of displayShowsFilterSearch
     *
     * @param $value
     */
    public function setDisplayShowsFilterSearch($value): void
    {
        $this->setViewParameter('displayShowsFilterSearch', $value);
    }

    /**
     * Return value of displayShowsFilterDate
     *
     * @return bool
     */
    public function getDisplayShowsFilterDate(): bool
    {
        return (bool)$this->getViewParameter('displayShowsFilterDate');
    }

    /**
     * Set value of displayShowsFilterDate
     *
     * @param $value
     */
    public function setDisplayShowsFilterDate($value): void
    {
        $this->setViewParameter('displayShowsFilterDate', $value);
    }

    /**
     * Return value of displayShowsFilterTime
     *
     * @return bool
     */
    public function getDisplayShowsFilterTime(): bool
    {
        return (bool)$this->getViewParameter('displayShowsFilterTime');
    }

    /**
     * Set value of displayShowsFilterTime
     *
     * @param $value
     */
    public function setDisplayShowsFilterTime($value): void
    {
        $this->setViewParameter('displayShowsFilterTime', $value);
    }

    /**
     * Return value of displayShowsFilterPrice
     *
     * @return bool
     */
    public function getDisplayShowsFilterPrice(): bool
    {
        return (bool)$this->getViewParameter('displayShowsFilterPrice');
    }

    /**
     * Set value of displayShowsFilterPrice
     *
     * @param $value
     */
    public function setDisplayShowsFilterPrice($value): void
    {
        $this->setViewParameter('displayShowsFilterPrice', $value);
    }

    /**
     * Return value of displayShowsFilterTag
     *
     * @return bool
     */
    public function getDisplayShowsFilterTag(): bool
    {
        return (bool)$this->getViewParameter('displayShowsFilterTag');
    }

    /**
     * Set value of displayShowsFilterTag
     *
     * @param $value
     */
    public function setDisplayShowsFilterTag($value): void
    {
        $this->setViewParameter('displayShowsFilterTag', $value);
    }

    /**
     * Return value of displayShowsFilterCategory
     *
     * @return bool
     */
    public function getDisplayShowsFilterCategory(): bool
    {
        return (bool)$this->getViewParameter('displayShowsFilterCategory');
    }

    /**
     * Set value of displayShowsFilterCategory
     *
     * @param $value
     */
    public function setDisplayShowsFilterCategory($value): void
    {
        $this->setViewParameter('displayShowsFilterCategory', $value);
    }

    /**
     * Return value of displayShowsFilterLocation
     *
     * @return bool
     */
    public function getDisplayShowsFilterLocation(): bool
    {
        return (bool)$this->getViewParameter('displayShowsFilterLocation');
    }

    /**
     * Set value of displayShowsFilterLocation
     *
     * @param $value
     */
    public function setDisplayShowsFilterLocation($value): void
    {
        $this->setViewParameter('displayShowsFilterLocation', $value);
    }

    /**
     * Return value of displayShowsMap
     *
     * @return bool
     */
    public function getDisplayShowsMap(): bool
    {
        return (bool)$this->getViewParameter('displayShowsMap');
    }

    /**
     * Set value of displayShowsMap
     *
     * @param $value
     */
    public function setDisplayShowsMap($value): void
    {
        $this->setViewParameter('displayShowsMap', $value);
    }

    /**
     * Return value of displayAttractionsSortPanel
     *
     * @return bool
     */
    public function getDisplayAttractionsSortPanel(): bool
    {
        return (bool)$this->getViewParameter('displayAttractionsSortPanel');
    }

    /**
     * Set value of displayAttractionsSortPanel
     *
     * @param $value
     */
    public function setDisplayAttractionsSortPanel($value): void
    {
        $this->setViewParameter('displayAttractionsSortPanel', $value);
    }

    /**
     * Return value of displayAttractionsDisplayPanel
     *
     * @return bool
     */
    public function getDisplayAttractionsDisplayPanel(): bool
    {
        return (bool)$this->getViewParameter('displayAttractionsDisplayPanel');
    }

    /**
     * Set value of displayAttractionsDisplayPanel
     *
     * @param $value
     */
    public function setDisplayAttractionsDisplayPanel($value): void
    {
        $this->setViewParameter('displayAttractionsDisplayPanel', $value);
    }

    /**
     * Return value of displayAttractionsFilterSearch
     *
     * @return bool
     */
    public function getDisplayAttractionsFilterSearch(): bool
    {
        return (bool)$this->getViewParameter('displayAttractionsFilterSearch');
    }

    /**
     * Set value of displayAttractionsFilterSearch
     *
     * @param $value
     */
    public function setDisplayAttractionsFilterSearch($value): void
    {
        $this->setViewParameter('displayAttractionsFilterSearch', $value);
    }

    /**
     * Return value of displayAttractionsFilterDate
     *
     * @return bool
     */
    public function getDisplayAttractionsFilterDate(): bool
    {
        return (bool)$this->getViewParameter('displayAttractionsFilterDate');
    }

    /**
     * Set value of displayAttractionsFilterDate
     *
     * @param $value
     */
    public function setDisplayAttractionsFilterDate($value): void
    {
        $this->setViewParameter('displayAttractionsFilterDate', $value);
    }

    /**
     * Return value of displayAttractionsFilterTime
     *
     * @return bool
     */
    public function getDisplayAttractionsFilterTime(): bool
    {
        return (bool)$this->getViewParameter('displayAttractionsFilterTime');
    }

    /**
     * Set value of displayAttractionsFilterTime
     *
     * @param $value
     */
    public function setDisplayAttractionsFilterTime($value): void
    {
        $this->setViewParameter('displayAttractionsFilterTime', $value);
    }

    /**
     * Return value of displayAttractionsFilterPrice
     *
     * @return bool
     */
    public function getDisplayAttractionsFilterPrice(): bool
    {
        return (bool)$this->getViewParameter('displayAttractionsFilterPrice');
    }

    /**
     * Set value of displayAttractionsFilterPrice
     *
     * @param $value
     */
    public function setDisplayAttractionsFilterPrice($value): void
    {
        $this->setViewParameter('displayAttractionsFilterPrice', $value);
    }

    /**
     * Return value of displayAttractionsFilterTag
     *
     * @return bool
     */
    public function getDisplayAttractionsFilterTag(): bool
    {
        return (bool)$this->getViewParameter('displayAttractionsFilterTag');
    }

    /**
     * Set value of displayAttractionsFilterTag
     *
     * @param $value
     */
    public function setDisplayAttractionsFilterTag($value): void
    {
        $this->setViewParameter('displayAttractionsFilterTag', $value);
    }

    /**
     * Return value of displayAttractionsFilterCategory
     *
     * @return bool
     */
    public function getDisplayAttractionsFilterCategory(): bool
    {
        return (bool)$this->getViewParameter('displayAttractionsFilterCategory');
    }

    /**
     * Set value of displayAttractionsFilterCategory
     *
     * @param $value
     */
    public function setDisplayAttractionsFilterCategory($value): void
    {
        $this->setViewParameter('displayAttractionsFilterCategory', $value);
    }

    /**
     * Return value of displayAttractionsFilterLocation
     *
     * @return bool
     */
    public function getDisplayAttractionsFilterLocation(): bool
    {
        return (bool)$this->getViewParameter('displayAttractionsFilterLocation');
    }

    /**
     * Set value of displayAttractionsFilterLocation
     *
     * @param $value
     */
    public function setDisplayAttractionsFilterLocation($value): void
    {
        $this->setViewParameter('displayAttractionsFilterLocation', $value);
    }

    /**
     * Return value of displayAttractionsMap
     *
     * @return bool
     */
    public function getDisplayAttractionsMap(): bool
    {
        return (bool)$this->getViewParameter('displayAttractionsMap');
    }

    /**
     * Set value of displayAttractionsMap
     *
     * @param $value
     */
    public function setDisplayAttractionsMap($value): void
    {
        $this->setViewParameter('displayAttractionsMap', $value);
    }

    /**
     * Return value of displayLunchSortPanel
     *
     * @return bool
     */
    public function getDisplayLunchsSortPanel(): bool
    {
        return (bool)$this->getViewParameter('displayLunchsSortPanel');
    }

    /**
     * Set value of displayLunchSortPanel
     *
     * @param $value
     */
    public function setDisplayLunchsSortPanel($value): void
    {
        $this->setViewParameter('displayLunchsSortPanel', $value);
    }

    /**
     * Return value of displayHotelSortPanel
     *
     * @return bool
     */
    public function getDisplayHotelSortPanel(): bool
    {
        return (bool)$this->getViewParameter('displayHotelSortPanel');
    }

    /**
     * Set value of displayHotelSortPanel
     *
     * @param $value
     */
    public function setDisplayHotelSortPanel($value): void
    {
        $this->setViewParameter('displayHotelSortPanel', $value);
    }

    /**
     * Return value of displayLunchDisplayPanel
     *
     * @return bool
     */
    public function getDisplayLunchsDisplayPanel(): bool
    {
        return (bool)$this->getViewParameter('displayLunchsDisplayPanel');
    }

    /**
     * Set value of displayLunchDisplayPanel
     *
     * @param $value
     */
    public function setDisplayLunchsDisplayPanel($value): void
    {
        $this->setViewParameter('displayLunchsDisplayPanel', $value);
    }

    /**
     * Return value of displayHotelDisplayPanel
     *
     * @return bool
     */
    public function getDisplayHotelDisplayPanel(): bool
    {
        return (bool)$this->getViewParameter('displayHotelDisplayPanel');
    }

    /**
     * Set value of displayHotelDisplayPanel
     *
     * @param $value
     */
    public function setDisplayHotelDisplayPanel($value): void
    {
        $this->setViewParameter('displayHotelDisplayPanel', $value);
    }

    /**
     * Return value of displayLunchFilterSearch
     *
     * @return bool
     */
    public function getDisplayLunchsFilterSearch(): bool
    {
        return (bool)$this->getViewParameter('displayLunchsFilterSearch');
    }

    /**
     * Set value of displayLunchFilterSearch
     *
     * @param $value
     */
    public function setDisplayLunchsFilterSearch($value): void
    {
        $this->setViewParameter('displayLunchsFilterSearch', $value);
    }

    /**
     * Return value of displayHotelFilterSearch
     *
     * @return bool
     */
    public function getDisplayHotelFilterSearch(): bool
    {
        return (bool)$this->getViewParameter('displayHotelFilterSearch');
    }

    /**
     * Set value of displayHotelFilterSearch
     *
     * @param $value
     */
    public function setDisplayHotelFilterSearch($value): void
    {
        $this->setViewParameter('displayHotelFilterSearch', $value);
    }

    /**
     * Return value of displayLunchFilterDate
     *
     * @return bool
     */
    public function getDisplayLunchsFilterDate(): bool
    {
        return (bool)$this->getViewParameter('displayLunchsFilterDate');
    }

    /**
     * Set value of displayLunchFilterDate
     *
     * @param $value
     */
    public function setDisplayLunchsFilterDate($value): void
    {
        $this->setViewParameter('displayLunchsFilterDate', $value);
    }

    /**
     * Return value of displayHotelFilterDate
     *
     * @return bool
     */
    public function getDisplayHotelFilterDate(): bool
    {
        return (bool)$this->getViewParameter('displayHotelFilterDate');
    }

    /**
     * Set value of displayHotelFilterDate
     *
     * @param $value
     */
    public function setDisplayHotelFilterDate($value): void
    {
        $this->setViewParameter('displayHotelFilterDate', $value);
    }

    /**
     * Return value of displayHotelAmenity
     *
     * @return bool
     */
    public function getDisplayHotelAmenity(): bool
    {
        return (bool)$this->getViewParameter('displayHotelAmenity');
    }

    /**
     * Set value of displayHotelAmenity
     *
     * @param $value
     */
    public function setDisplayHotelAmenity($value): void
    {
        $this->setViewParameter('displayHotelAmenity', $value);
    }

    /**
     * Return value of displayHotelCity
     *
     * @return bool
     */
    public function getDisplayHotelCity(): bool
    {
        return (bool)$this->getViewParameter('displayHotelCity');
    }

    /**
     * Set value of displayHotelCity
     *
     * @param $value
     */
    public function setDisplayHotelCity($value): void
    {
        $this->setViewParameter('displayHotelCity', $value);
    }

    /**
     * Return value of displayHotelStar
     *
     * @return bool
     */
    public function getDisplayHotelStar(): bool
    {
        return (bool)$this->getViewParameter('displayHotelStar');
    }

    /**
     * Set value of displayHotelStar
     *
     * @param $value
     */
    public function setDisplayHotelStar($value): void
    {
        $this->setViewParameter('displayHotelStar', $value);
    }

    /**
     * Return value of displayLunchFilterTime
     *
     * @return bool
     */
    public function getDisplayLunchsFilterTime(): bool
    {
        return (bool)$this->getViewParameter('displayLunchsFilterTime');
    }

    /**
     * Set value of displayLunchFilterTime
     *
     * @param $value
     */
    public function setDisplayLunchsFilterTime($value): void
    {
        $this->setViewParameter('displayLunchsFilterTime', $value);
    }

    /**
     * Return value of displayLunchFilterPrice
     *
     * @return bool
     */
    public function getDisplayLunchsFilterPrice(): bool
    {
        return (bool)$this->getViewParameter('displayLunchsFilterPrice');
    }

    /**
     * Set value of displayLunchFilterPrice
     *
     * @param $value
     */
    public function setDisplayLunchsFilterPrice($value): void
    {
        $this->setViewParameter('displayLunchsFilterPrice', $value);
    }

    /**
     * Return value of displayHotelFilterPrice
     *
     * @return bool
     */
    public function getDisplayHotelFilterPrice(): bool
    {
        return (bool)$this->getViewParameter('displayHotelFilterPrice');
    }

    /**
     * Set value of displayHotelFilterPrice
     *
     * @param $value
     */
    public function setDisplayHotelFilterPrice($value): void
    {
        $this->setViewParameter('displayHotelFilterPrice', $value);
    }

    /**
     * Return value of displayLunchFilterTag
     *
     * @return bool
     */
    public function getDisplayLunchsFilterTag(): bool
    {
        return (bool)$this->getViewParameter('displayLunchsFilterTag');
    }

    /**
     * Set value of displayLunchFilterTag
     *
     * @param $value
     */
    public function setDisplayLunchsFilterTag($value): void
    {
        $this->setViewParameter('displayLunchsFilterTag', $value);
    }

    /**
     * Return value of displayLunchFilterCategory
     *
     * @return bool
     */
    public function getDisplayLunchsFilterCategory(): bool
    {
        return (bool)$this->getViewParameter('displayLunchsFilterCategory');
    }

    /**
     * Set value of displayLunchFilterCategory
     *
     * @param $value
     */
    public function setDisplayLunchsFilterCategory($value): void
    {
        $this->setViewParameter('displayLunchsFilterCategory', $value);
    }

    /**
     * Return value of displayLunchFilterLocation
     *
     * @return bool
     */
    public function getDisplayLunchsFilterLocation(): bool
    {
        return (bool)$this->getViewParameter('displayLunchsFilterLocation');
    }

    /**
     * Set value of displayLunchFilterLocation
     *
     * @param $value
     */
    public function setDisplayLunchsFilterLocation($value): void
    {
        $this->setViewParameter('displayLunchsFilterLocation', $value);
    }

    /**
     * Return value of displayLunchMap
     *
     * @return bool
     */
    public function getDisplayLunchsMap(): bool
    {
        return (bool)$this->getViewParameter('displayLunchsMap');
    }

    /**
     * Set value of displayLunchMap
     *
     * @param $value
     */
    public function setDisplayLunchsMap($value): void
    {
        $this->setViewParameter('displayLunchsMap', $value);
    }

    /**
     * Return value of displayHotelMap
     *
     * @return bool
     */
    public function getDisplayHotelMap(): bool
    {
        return (bool)$this->getViewParameter('displayHotelMap');
    }

    /**
     * Set value of displayHotelMap
     *
     * @param $value
     */
    public function setDisplayHotelMap($value): void
    {
        $this->setViewParameter('displayHotelMap', $value);
    }

    /**
     * Return value of displayHotelRoomsGuests
     *
     * @return bool
     */
    public function getDisplayHotelRoomsGuests(): bool
    {
        return (bool)$this->getViewParameter('displayHotelRoomsGuests');
    }

    /**
     * Set value of displayHotelRoomsGuests
     *
     * @param $value
     */
    public function setDisplayHotelRoomsGuests($value): void
    {
        $this->setViewParameter('displayHotelRoomsGuests', $value);
    }

    /**
     * Return value of displayPackagesFilterSearch
     *
     * @return bool
     */
    public function getDisplayPackagesFilterSearch(): bool
    {
        return (bool)$this->getViewParameter('displayPackagesFilterSearch');
    }

    /**
     * Set value of displayPackagesFilterSearch
     *
     * @param $value
     */
    public function setDisplayPackagesFilterSearch($value): void
    {
        $this->setViewParameter('displayPackagesFilterSearch', $value);
    }

    /**
     * Return value of displayPackagesFilterDate
     *
     * @return bool
     */
    public function getDisplayPackagesFilterDate(): bool
    {
        return (bool)$this->getViewParameter('displayPackagesFilterDate');
    }

    /**
     * Set value of displayPackagesFilterDate
     *
     * @param $value
     */
    public function setDisplayPackagesFilterDate($value): void
    {
        $this->setViewParameter('displayPackagesFilterDate', $value);
    }

    /**
     * Return value of displayPackagesFilterPrice
     *
     * @return bool
     */
    public function getDisplayPackagesFilterPrice(): bool
    {
        return (bool)$this->getViewParameter('displayPackagesFilterPrice');
    }

    /**
     * Set value of displayPackagesFilterPrice
     *
     * @param $value
     */
    public function setDisplayPackagesFilterPrice($value): void
    {
        $this->setViewParameter('displayPackagesFilterPrice', $value);
    }

    /**
     * Return value of displayPackagesFilterCategory
     *
     * @return bool
     */
    public function getDisplayPackagesFilterCategory(): bool
    {
        return (bool)$this->getViewParameter('displayPackagesFilterCategory');
    }

    /**
     * Set value of displayPackagesFilterCategory
     *
     * @param $value
     */
    public function setDisplayPackagesFilterCategory($value): void
    {
        $this->setViewParameter('displayPackagesFilterCategory', $value);
    }

    /**
     * Return value of displayMainPageBanners
     *
     * @return bool
     */
    public function getDisplayMainPageBanners(): bool
    {
        return (bool)$this->getViewParameter('displayMainPageBanners');
    }

    /**
     * Set value of displayMainPageBanners
     *
     * @param $value
     */
    public function setDisplayMainPageBanners($value): void
    {
        $this->setViewParameter('displayMainPageBanners', $value);
    }

    /**
     * Return value of displayMainPageFilters
     *
     * @return bool
     */
    public function getDisplayMainPageFilters(): bool
    {
        return (bool)$this->getViewParameter('displayMainPageFilters');
    }

    /**
     * Set value of displayMainPageFilters
     *
     * @param $value
     */
    public function setDisplayMainPageFilters($value): void
    {
        $this->setViewParameter('displayMainPageFilters', $value);
    }

    /**
     * Return value of displayMainPageInfoBlock
     *
     * @return bool
     */
    public function getDisplayMainPageInfoBlock(): bool
    {
        return (bool)$this->getViewParameter('displayMainPageInfoBlock');
    }

    /**
     * Set value of displayMainPageInfoBlock
     *
     * @param $value
     */
    public function setDisplayMainPageInfoBlock($value): void
    {
        $this->setViewParameter('displayMainPageInfoBlock', $value);
    }

    /**
     * Return value of displayMainPageGeneralBlock
     *
     * @return bool
     */
    public function getDisplayMainPageGeneralBlock(): bool
    {
        return (bool)$this->getViewParameter('displayMainPageGeneralBlock');
    }

    /**
     * Set value of displayMainPageGeneralBlock
     *
     * @param $value
     */
    public function setDisplayMainPageGeneralBlock($value): void
    {
        $this->setViewParameter('displayMainPageGeneralBlock', $value);
    }

    /**
     * Return value of displayMainPageFeatured
     *
     * @return bool
     */
    public function getDisplayMainPageFeatured(): bool
    {
        return (bool)$this->getViewParameter('displayMainPageFeatured');
    }

    /**
     * Set value of displayMainPageFeatured
     *
     * @param $value
     */
    public function setDisplayMainPageFeatured($value): void
    {
        $this->setViewParameter('displayMainPageFeatured', $value);
    }

    /**
     * Return value of displayMainPageParnters
     *
     * @return bool
     */
    public function getDisplayMainPageParnters(): bool
    {
        return (bool)$this->getViewParameter('displayMainPageParnters');
    }

    /**
     * Set value of displayMainPageParnters
     *
     * @param $value
     */
    public function setDisplayMainPageParnters($value): void
    {
        $this->setViewParameter('displayMainPageParnters', $value);
    }

    /**
     * Return default shows list type
     *
     * @return string
     */
    public function getDefaultShowsListType(): ?string
    {
        return $this->getViewParameter('defaultShowsListType') ?? self::LIST_TYPE_LIST;
    }

    /**
     * Set default shows list type
     *
     * @param $value
     */
    public function setDefaultShowsListType($value): void
    {
        $this->setViewParameter('defaultShowsListType', $value);
    }

    /**
     * Return default attraction list type
     *
     * @return string
     */
    public function getDefaultAttractionListType(): ?string
    {
        return $this->getViewParameter('defaultAttractionListType') ?? self::LIST_TYPE_LIST;
    }

    /**
     * Set default attraction list type
     *
     * @param $value
     */
    public function setDefaultAttractionListType($value): void
    {
        $this->setViewParameter('defaultAttractionListType', $value);
    }

    /**
     * Return default lunch list type
     *
     * @return string
     */
    public function getDefaultLunchListType(): ?string
    {
        return $this->getViewParameter('defaultLunchListType') ?? self::LIST_TYPE_LIST;
    }

    /**
     * Set default lunch list type
     *
     * @param $value
     */
    public function setDefaultLunchListType($value): void
    {
        $this->setViewParameter('defaultLunchListType', $value);
    }

    /**
     * Return default hotel list type
     *
     * @return string
     */
    public function getDefaultHotelListType(): ?string
    {
        return $this->getViewParameter('defaultHotelListType') ?? self::LIST_TYPE_GRID;
    }

    /**
     * Set default hotel list type
     *
     * @param $value
     */
    public function setDefaultHotelListType($value): void
    {
        $this->setViewParameter('defaultHotelListType', $value);
    }

    /**
     * Is display left column in shows
     *
     * @return bool
     */
    public static function getDisplayLeftColumnShows(): bool
    {
        return self::$dataSiteSettings->getDisplayShowsFilterSearch()
            || self::$dataSiteSettings->getDisplayShowsFilterDate()
            || self::$dataSiteSettings->getDisplayShowsFilterTime()
            || self::$dataSiteSettings->getDisplayShowsFilterPrice()
            || self::$dataSiteSettings->getDisplayShowsFilterTag()
            || self::$dataSiteSettings->getDisplayShowsFilterCategory()
            || self::$dataSiteSettings->getDisplayShowsFilterLocation()
            || self::$dataSiteSettings->getDisplayShowsMap();
    }

    /**
     * Is display left column in attractions
     *
     * @return bool
     */
    public static function getDisplayLeftColumnAttractions(): bool
    {
        return self::$dataSiteSettings->getDisplayAttractionsFilterSearch()
            || self::$dataSiteSettings->getDisplayAttractionsFilterDate()
            || self::$dataSiteSettings->getDisplayAttractionsFilterPrice()
            || self::$dataSiteSettings->getDisplayAttractionsFilterCategory()
            || self::$dataSiteSettings->getDisplayAttractionsFilterLocation()
            || self::$dataSiteSettings->getDisplayAttractionsMap();
    }

    /**
     * Is display left column in lunch
     *
     * @return bool
     */
    public static function getDisplayLeftColumnLunchs(): bool
    {
        return self::$dataSiteSettings->getDisplayLunchsFilterSearch()
            || self::$dataSiteSettings->getDisplayLunchsFilterDate()
            || self::$dataSiteSettings->getDisplayLunchsFilterPrice()
            || self::$dataSiteSettings->getDisplayLunchsFilterCategory()
            || self::$dataSiteSettings->getDisplayLunchsFilterLocation()
            || self::$dataSiteSettings->getDisplayLunchsMap();
    }

    /**
     * Is display left column in packages
     *
     * @return bool
     */
    public static function getDisplayLeftColumnPackages(): bool
    {
        if (self::getSiteSettings() === null) {
            return false;
        }
        return self::$dataSiteSettings->getDisplayPackagesFilterSearch()
            || self::$dataSiteSettings->getDisplayPackagesFilterDate()
            || self::$dataSiteSettings->getDisplayPackagesFilterPrice()
            || self::$dataSiteSettings->getDisplayPackagesFilterCategory();
    }

    /**
     * Is display left column in lodging
     *
     * @return bool
     */
    public static function getDisplayLeftColumnHotel(): bool
    {
        return self::$dataSiteSettings->getDisplayHotelFilterSearch()
            || self::$dataSiteSettings->getDisplayHotelFilterDate()
            || self::$dataSiteSettings->getDisplayHotelFilterPrice()
            || self::$dataSiteSettings->getDisplayHotelRoomsGuests()
            || self::$dataSiteSettings->getDisplayHotelStar()
            || self::$dataSiteSettings->getdisplayHotelCity()
            || self::$dataSiteSettings->getdisplayHotelAmenity()
            || self::$dataSiteSettings->getDisplayHotelMap();
    }

    public function getDefaultDate(): ?DateTime
    {
        try {
            return new DateTime($this->default_date);
        } catch (Exception $e) {
        }
        return null;
    }

    public function getHotelDefaultDate(): ?DateTime
    {
        try {
            return new DateTime($this->hotel_default_date);
        } catch (Exception $e) {
        }
        return null;
    }
}

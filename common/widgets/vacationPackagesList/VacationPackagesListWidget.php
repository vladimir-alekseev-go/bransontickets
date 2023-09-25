<?php

namespace common\widgets\vacationPackagesList;

use common\models\TrAttractions;
use common\models\TrShows;
use common\models\VacationPackage;
use common\models\VacationPackageAttraction;
use common\models\VacationPackageCategory;
use common\models\VacationPackageShow;
use DateTime;
use common\models\form\Search;
use Yii;
use yii\base\Widget;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Expression;

class VacationPackagesListWidget extends Widget
{
    public const LAYOUT_LIST = "list";

    private $hashSort;

    public $search;
    public $rangePrice;
    public $pagination;
    public $totalCount;
    public $pageSize = 4;
    public $categories;
    public $layout;
    public $show_external_id;
    public $attraction_external_id;
    public $useSearchFilter = true;
    public $channel;

    /**
     * Generate hash for sorting.
     */
    public function generateHashSort(): void
    {
        if (!Yii::$app->request->isAjax) {
            $this->hashSort = md5((new DateTime())->format('s-h-i-m-d-Y'));
        } elseif (!empty(Yii::$app->getRequest()->get('hashSort'))) {
            $this->setHashSort(Yii::$app->getRequest()->get('hashSort'));
        }
    }

    /**
     * Get hashSort.
     *
     * @return string
     */
    public function getHashSort(): ?string
    {
        return $this->hashSort;
    }

    /**
     * Set hashSort.
     *
     * @param string $hashSort
     */
    public function setHashSort(string $hashSort): void
    {
        $hashSort = preg_replace("/[^\w]/", "", $hashSort);
        $this->hashSort = $hashSort;
    }

    /**
     * Make general query without filter.
     *
     * @return ActiveQuery
     */
    public function getQuery(): ActiveQuery
    {
        return VacationPackage::find()
            ->innerJoinWith('vacationPackagePrices')
            ->joinWith(
                [
                    'vacationPackageShows' => static function ($query) {
                        $query->joinWith(['itemExternal']);
                    },
                    'vacationPackageAttractions' => static function ($query) {
                        $query->joinWith(['itemExternal']);
                    },
                    'vacationPackageCategories'
                ]
            )
            ->andFilterWhere(
                [
                    'or',
                    ['=', 'channel', $this->channel],
                    ['=', 'channel', Yii::$app->params['vacation_package_channel_type']],
                ]
            )
            ->orderby(new Expression('md5(concat("' . $this->getHashSort() . '",hash))'))
            ->groupby(VacationPackage::tableName() . '.id');
    }

    /**
     * Make general query by filter.
     *
     * @return ActiveQuery
     */
    public function getQueryByFilter(): ActiveQuery
    {
        $query = $this->getQuery()
            ->andFilterWhere(['=', VacationPackageShow::tableName() . '.item_external_id', $this->show_external_id])
            ->andFilterWhere(
                ['=', VacationPackageAttraction::tableName() . '.item_external_id', $this->attraction_external_id]
            )
            ->andFilterWhere(['in', VacationPackageCategory::tableName() . '.name', $this->search->c]);

        if ($this->useSearchFilter) {
            $query->andFilterWhere(
                [
                    'or',
                    ['like', TrShows::tableName() . '.name', $this->search->title],
                    ['like', TrAttractions::tableName() . '.name', $this->search->title],
                    ['like', VacationPackage::tableName() . '.name', $this->search->title],
                ]
            )
                ->andFilterWhere(['<=', 'valid_start', $this->search->dateTimeTo->format('Y-m-d H:i:s')])
                ->andFilterWhere(['>=', 'valid_end', $this->search->dateTimeFrom->format('Y-m-d H:i:s')])
                ->andFilterWhere(['>=', 'price', $this->search->priceFrom])
                ->andFilterWhere(['<=', 'price', $this->search->priceTo]);
        }

        if (isset($this->search->statusWl)) {
            $query->andWhere([VacationPackage::tableName() . '.status_wl' => $this->search->statusWl]);
        }

        return $query;
    }

    /**
     * Return total count of vacation packages.
     *
     * @return int
     */
    public function getTotalCountQuery(): int
    {
        return $this->totalCount = $this->getQueryByFilter()
            ->select([VacationPackage::tableName() . '.id'])
            ->orderby(false)
            ->count();
    }

    /**
     * Get maximal a price.
     *
     * @return double
     */
    public function getMaxPrice()
    {
        $price = $this->getQuery()->select(['price'])->groupby('price')->orderBy('price desc')->column();
        return !empty($price) ? $price[0] : 0;
    }

    /**
     * Get Search model.
     *
     * @return string
     */
    public function getSearchModel(): string
    {
        return Search::class;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->channel = VacationPackage::CHANNEL_TYPE_ALL;

        $this->generateHashSort();

        $search = $this->getSearchModel();
        $this->search = new $search(['model' => new VacationPackage]);
        $this->search->load(Yii::$app->getRequest()->get());
        $this->search->display = Search::DISPLAY_HIDE;

        $this->pagination = new Pagination(
            [
                'totalCount' => $this->getTotalCountQuery(),
                'pageSize'   => $this->pageSize,
                'params'     => array_merge(
                    Yii::$app->getRequest()->get(),
                    [
                        'hashSort' => $this->getHashSort(),
                        'page'     => Yii::$app->getRequest()->get('page')
                    ]
                )
            ]
        );

        $this->rangePrice = [
            "value_from" => $this->search->priceFrom ?: 0,
            "value_to"   => $this->search->priceTo ?: $this->getMaxPrice(),
            "min"        => 0,
            "max"        => $this->getMaxPrice(),
        ];

        $this->categories = VacationPackage::getAllActiveTypes();
        $this->categories = array_combine($this->categories, $this->categories);
    }

    /**
     * Return pagination btn.
     *
     * @return string
     */
    public function getPaginationBtn(): string
    {
        return $this->render('@common/views/pagination-btn', ['pagination' => $this->pagination]);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function run(): string
    {
        if (!empty($this->layout)) {
            return $this->render($this->layout);
        }

        if (Yii::$app->request->isAjax) {
            return $this->render('items') . $this->getPaginationBtn();
        }

        $this->getView()->registerJs('dataList.init($("#show-list"), $("#panel-list"), $("#list-filter"))');
        return $this->render('index');
    }

    /**
     * Register assets.
     */
    public function assetRegister(): void
    {
        $view = $this->getView();
        VacationPackagesListWidgetAsset::register($view);
    }

    /**
     * Return items for current page.
     *
     * @return array
     */
    public function getItem(): array
    {
        return $this->getQueryByFilter()
            ->offset($this->pagination->offset)
            ->limit($this->pagination->limit)
            ->all();
    }
}

<?php

namespace frontend\controllers;

use common\models\Compare;
use common\models\form\Search;
use common\models\TrAttractions;
use common\models\TrAttractionsPrices;
use frontend\widgets\vacationPackagesList\VacationPackagesListWidget;
use DateInterval;
use DateTime;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AttractionsController extends Controller
{
    public const mainClass = TrAttractions::class;

    /**
     * @var string
     */
    protected $searchClass = Search::class;

    public $priceListInterval;

    protected function setPriceListInterval(): void
    {
        $this->priceListInterval = new DateInterval('P7D');
    }

    public function init()
    {
        parent::init();
        $this->setPriceListInterval();
    }

    /**
     * @return array|string
     * @throws Exception
     */
    public function actionIndex()
    {
        /**
         * @var Search          $Search
         * @var TrAttractions[] $items
         */
        $Search = new $this->searchClass(['model' => new TrAttractions]);

        $Search->load(Yii::$app->getRequest()->get());

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $this->layout = 'empty';
        }

        $query = TrAttractions::getByFilterAll($Search);

        $itemCount = $query->count();

        $pagination = new Pagination(['totalCount' => $itemCount, 'pageSize' => 21]);

        $items = $query->offset($pagination->offset)->limit($pagination->limit)->all();
        if ($Search->fieldSort === Search::FIELD_SORT_MARKETING_LEVEL) {
            $items = TrAttractions::reSort($items);
        }

        $priceAll = TrAttractions::getPriceByFilter($Search, $this->priceListInterval);
        $priceAll = TrAttractions::preparePriceForList($priceAll);

        $NearestDates = TrAttractionsPrices::getNearestAvailable(
            new DateTime($Search->dateFrom),
            ArrayHelper::getColumn($items, 'id_external')
        )
            ->andWhere(
                ['<', 'start', (new DateTime($Search->dateFrom))->add(new DateInterval('P7D'))->format('Y-m-d H:i:s')]
            )
            ->asArray()->all();
        $NearestDates = ArrayHelper::index($NearestDates, 'id_external');

        foreach ($items as &$item) {
            if (!empty($NearestDates[$item->id_external])) {
                $item->setBuyNowUrl(
                    $item->getUrl(
                        [
                            'tickets-on-date' => $NearestDates[$item->id_external]['start'],
                            '#' => 'availability',
                        ]
                    )
                );
            }
        }
        unset($item);

        if (!Yii::$app->request->isAjax) {
            $categories = ArrayHelper::index(TrAttractions::getActualCategoriesCash(), 'id_external');
        }

        $rangePrice = $Search->getSliderPriceRange();

        // the output condition of the noindex meta tag
        $fGet = array_filter(Yii::$app->getRequest()->get());
        unset($fGet['page'], $fGet['per-page'], $fGet['sort'], $fGet['by'], $fGet['display']);

//        if ($fGet || $Search->sort !== 'asc' || $Search->by !== 'marketing_level' || $Search->display !== 'list') {
//            $this->view->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
//        }

        Yii::$app->view->params['view']['search'] = $Search;

        if (Yii::$app->request->isAjax) {
            return [
                'listHtml' => $this->render(
                    '@app/views/shows/items',
                    compact(
                        'items',
                        'pagination',
                        'rangePrice',
                        'priceAll',
                        'Search'
                    )
                ),
                'sliderPriceRange' => $rangePrice
            ];
        }

        return $this->render(
            'index',
            compact(
                'items',
                'pagination',
                'categories',
                'rangePrice',
                'priceAll',
                'Search',
                'itemCount'
            )
        );
    }

    /**
     * @param $code
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetail($code)
    {
        $model = TrAttractions::getActive()->where(['code' => $code])->with(
            [
                'itemsPhoto' => static function (ActiveQuery $query) {
                    $query->with(['photo', 'preview']);
                }
            ]
        )->one();

        if (!$model) {
            throw new NotFoundHttpException;
        }

        $showsRecommended = TrAttractions::getActive()
            ->orderBy(new Expression('rand()'))
            ->limit(6)
            ->all();

        $VPLWidget = new VacationPackagesListWidget(
            [
                'layout' => VacationPackagesListWidget::LAYOUT_LIST,
                'attraction_external_id' => $model->id_external,
                'pageSize' => 0,
                'useSearchFilter' => false,
            ]
        );

        return $this->render('@app/views/shows/detail', compact('model', 'showsRecommended', 'VPLWidget'));
    }

    /**
     * @return string|Response
     */
    public function actionPopupCompare()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
        $Compare = new Compare();
        $model = new TrAttractions;
        $ids = $Compare->getIDs($model);
        if (empty($ids)) {
            return $this->redirect(['index']);
        }
        $Search = new $this->searchClass(['model' => $model, 'externalIds' => $ids]);
        $Search->load([]);
        $items = TrAttractions::getByFilterAll($Search)->all();
        $priceAll = TrAttractions::getPriceByFilter($Search);
        $priceAll = TrAttractions::preparePriceForList($priceAll);

        return $this->renderAjax(
            '@app/views/components/popup-compare-data',
            compact('model', 'items', 'Search', 'priceAll')
        );
    }
}

<?php

namespace frontend\controllers;

use common\helpers\General;
use common\models\Compare;
use common\models\TrPrices;
use common\models\TrShows;
use Exception;
use frontend\models\ScheduleForm;
use frontend\widgets\scheduleSlider\ScheduleSliderWidget;
use frontend\widgets\vacationPackagesList\VacationPackagesListWidget;
use DateInterval;
use DateTime;
use common\models\form\Search;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ShowsController extends Controller
{
    use TicketsControllerTrait;

    public const mainClass = TrShows::class;

    /**
     * @var string $searchClass
     */
    protected $searchClass = Search::class;

    /**
     * @var $priceListInterval
     */
    public $priceListInterval;

    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        parent::init();
        $this->setPriceListInterval();
    }

    protected function setPriceListInterval(): void
    {
        $this->priceListInterval = new DateInterval('P7D');
    }

    /**
     * @return array|string
     * @throws Exception
     */
    public function actionIndex()
    {
        /**
         * @var TrShows[] $items
         */

        $Search = new $this->searchClass(['model' => new TrShows]);
        $Search->load(Yii::$app->getRequest()->get());

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $this->layout = BaseController::LAYOUT_EMPTY;
        }

        $query = TrShows::getByFilterAll($Search);

        $query->with(['preview', 'theatre']);

        $itemCount = $query->count();

        $pagination = new Pagination(['totalCount' => $itemCount, 'pageSize' => 21]);

        $items = $query->offset($pagination->offset)->limit($pagination->limit)->all();
        if ($Search->fieldSort === Search::FIELD_SORT_MARKETING_LEVEL) {
            $items = TrShows::reSort($items);
        }

        $cache = Yii::$app->cache;
        $cacheKey = TrShows::TYPE . 'priceAll' . 'from' . $Search->dateTimeFrom->format(
                'Y-m-d'
            ) . 'to' . $Search->dateTimeTo->format('Y-m-d')
            . 'timeFrom' . $Search->timeFrom . 'timeTo' . $Search->timeTo;
        $priceAll = $cache->get($cacheKey);
        if ($priceAll === false) {
            $priceAll = TrShows::getPriceByFilter($Search, $this->priceListInterval);
            $priceAll = TrShows::preparePriceForList($priceAll);
            $cache->set($cacheKey, $priceAll, 60 * 15);
        }

        $NearestDates = TrPrices::getNearestAvailable(
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
            $categories = ArrayHelper::index(TrShows::getActualCategoriesCash(), 'id_external');
        }

        $rangePrice = $Search->getSliderPriceRange();

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
                'sliderPriceRange' => $rangePrice,
                'itemCount' => $itemCount,
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
                'itemCount',
            )
        );
    }

    /**
     * @param $code
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetail($code): string
    {
        /**
         * @var TrShows $model
         */
        $model = TrShows::getActive()->where(['code' => $code])->with(
            [
                'itemsPhoto' => static function (ActiveQuery $query) {
                    $query->with(['photo', 'preview']);
                }
            ]
        )->one();

        if (!$model) {
            throw new NotFoundHttpException;
        }

        $showsRecommended = TrShows::getActive()
            ->orderBy(new Expression('rand()'))
            ->limit(6)
            ->all();

        $d = new DateTime();
        $d->setTime(0, 0);
        $ScheduleSlider = new ScheduleSliderWidget(['model' => $model, 'date' => $d]);

        $VPLWidget = new VacationPackagesListWidget(
            [
                'layout' => VacationPackagesListWidget::LAYOUT_LIST,
                'show_external_id' => $model->id_external,
                'pageSize' => 0,
                'useSearchFilter' => false,
            ]
        );

        return $this->render('detail', compact('model', 'showsRecommended', 'ScheduleSlider', 'VPLWidget'));
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
        $model = new TrShows;
        $ids = $Compare->getIDsByType($model::TYPE);
        if (empty($ids)) {
            return $this->redirect(['index']);
        }
        $Search = new $this->searchClass(['model' => $model, 'externalIds' => $ids]);
        $Search->load([]);
        $items = TrShows::getByFilterAll($Search)->all();
        $priceAll = TrShows::getPriceByFilter($Search, new DateInterval('P5D'));
        $priceAll = TrShows::preparePriceForList($priceAll);

        return $this->renderAjax(
            '@app/views/components/popup-compare-data',
            compact('model', 'items', 'Search', 'priceAll')
        );
    }


    /**
     * @param        $code
     * @param string $date
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSchedule($code, $date = '')
    {
        $ScheduleForm = new ScheduleForm();
        $ScheduleForm->load(Yii::$app->request->get());

        /**
         * @var TrShows $model
         */
        $model = TrShows::getActive()->where(['code' => $code])->one();

        if (!$model) {
            throw new NotFoundHttpException;
        }

        $gotoDate = $ScheduleForm->getDate() ?: General::getDatePeriod()->start;

        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            return Json::encode(
                [
                    'events' => $model->getCalendarEvents(),
                    'gotoDate' => $gotoDate->format('Y-m-d'),
                ]
            );
        }

        return $this->redirect($model->getUrl());
    }

}

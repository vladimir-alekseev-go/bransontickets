<?php
namespace frontend\controllers;

use common\models\Compare;
use common\models\form\SearchPosHotel;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use Exception;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class LodgingController extends Controller
{
    /**
     * @var string
     */
    protected $searchClass = SearchPosHotel::class;

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        /**
         * @var SearchPosHotel $Search
         * @var TrPosHotels[]  $items
         */
        $Search = new $this->searchClass(['model' => new TrPosHotels()]);

        $Search->load(Yii::$app->getRequest()->get());

        $prices = TrPosHotels::getPricesByFilterFromTripium($Search);

        $query = TrPosHotels::getByFilter($Search);
        $query->andWhere(['external_id' => array_keys($prices)]);
        $query->with(['preview', 'theatre']);

        $itemCount = $query->count();

        $pagination = new Pagination(['totalCount' => $itemCount, 'pageSize' => 100]);

        $items = $query->offset($pagination->offset)->limit($pagination->limit)->all();

        TrPosHotels::setPrices($items, $prices);
        $items = TrPosHotels::filterByPrice($items, $Search);
        TrPosHotels::sortByPrice($items, $Search);

        $priceAll = [];

        $rangePrice = $Search->getSliderPriceRange();
        $RestaurantAmenity = $Search::getSliderAmenities();

        // the output condition of the noindex meta tag
        $fGet = array_filter(Yii::$app->getRequest()->get());
        unset($fGet['page'], $fGet['per-page'], $fGet['sort'], $fGet['by'], $fGet['display']);

//        if ($fGet || $Search->sort !== 'asc' || $Search->by !== 'marketing_level' || $Search->display !== 'list') {
//            $this->view->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
//        }

        Yii::$app->view->params['view']['search'] = $Search;

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $this->layout = 'empty';
            return [
                'listHtml'         => $this->render(
                    'items',
                    compact(
                        'items',
                        'pagination',
                        'priceAll',
                        'Search',
                    )
                ),
                'sliderPriceRange' => $rangePrice
            ];
        }

        $categories = ArrayHelper::index(TrPosHotels::getActualCategoriesCash(), 'id_external');
        $locations = [];
        return $this->render(
            'index',
            compact(
                'items',
                'pagination',
                'categories',
                'locations',
                'priceAll',
                'rangePrice',
                'Search',
                'itemCount',
                'RestaurantAmenity'
            )
        );
    }

    /**
     * @return string|Response
     * @throws Exception
     */
    public function actionPopupCompare()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
        $Compare = new Compare();
        $ids = $Compare->getIDsByType(TrPosHotels::TYPE);

        $items = TrPosHotels::find()->andWhere(['id_external' => $ids])->with(['preview', 'theatre'])->all();

        $priceAll = [];
        $Search = null;

        return $this->renderAjax(
            '@app/views/components/popup-compare-data',
            compact('items', 'Search', 'priceAll')
        );
    }
}

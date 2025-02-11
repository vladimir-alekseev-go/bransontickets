<?php

namespace common\controllers;

use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/** @deprecated  */
trait LodgingControllerTrait
{
    /**
     * @return mixed
     */
    public function indexAllHotels()
    {
        $Search = new $this->searchClass;
        $Search->model = new TrPosPlHotels();
        $Search->load(Yii::$app->getRequest()->get());

        $rangePrice = $Search->getSliderPriceRange();

        if (Yii::$app->request->isAjax) {
            $items = TrPosPlHotels::withPriceLine($Search);

            $query = TrPosHotels::getByFilterAll($Search);
            $query->with(['preview', 'theatre']);
            $itemsPosHotel = $query->all();
            $items = array_merge($itemsPosHotel, $items);
            TrPosHotels::clearDuplicate($items);

            $this->layout = false;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'listHtml'  => $this->render('list-pl-hotels', compact('Search', 'items')),
                'itemCount' => count($items),
            ];
        }

        $pagination = new Pagination(['totalCount' => 1000, 'pageSize' => 1]);

        $categories = ArrayHelper::index(TrPosHotels::getActualCategoriesCash(), 'id_external');

        Yii::$app->view->params['view']['search'] = $Search;

        return $this->render(
            'index-pl-hotels',
            compact(
                'Search',
                'rangePrice',
                'pagination',
                'categories',
            )
        );
    }
}

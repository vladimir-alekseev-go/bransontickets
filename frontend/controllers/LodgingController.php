<?php
namespace frontend\controllers;

use common\controllers\LodgingControllerTrait;
use common\models\Compare;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use Exception;
use frontend\models\SearchPlHotel;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class LodgingController extends Controller
{
    use LodgingControllerTrait;

    /**
     * @var string
     */
    protected $searchClass = SearchPlHotel::class;

    /**
     * @return mixed
     */
    public function actionIndex()
    {
    	return $this->indexAllHotels();
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
        $model = new TrPosPlHotels;
        $ids = $Compare->getIDsByType(TrPosHotels::TYPE);
        $PLIds = $Compare->getIDsByType(TrPosPlHotels::TYPE);

        if (empty($PLIds) && empty($ids)) {
            return $this->redirect(['index']);
        }

        $Search = new $this->searchClass(['model' => $model]);
        $Search->load([]);

        $items = [];
        if (!empty($PLIds)) {
            $Search->setAttributes(['externalIds' => $PLIds]);
            $items = TrPosPlHotels::withPriceLine($Search);
        }
        if (!empty($ids)) {
            $Search->setAttributes(['externalIds' => $ids]);
            $query = TrPosHotels::getByFilterAll($Search);
            $query->with(['preview', 'wishUser', 'theatre']);
            $itemsPosHotel = $query->all();
            $items = array_merge($itemsPosHotel, $items);
        }

        TrPosHotels::clearDuplicate($items);

        $priceAll = [];

        return $this->renderAjax(
            '@app/views/components/popup-compare-data',
            compact('model', 'items', 'Search', 'priceAll')
        );
    }
}

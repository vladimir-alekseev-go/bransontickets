<?php

namespace console\controllers;

use common\models\TrAdmissions;
use common\models\TrAttractions;
use common\models\TrAttractionsPrices;
use common\models\TrBasket;
use common\models\TrCategories;
use common\models\TrPosHotels;
use common\models\TrPrices;
use common\models\TrShows;
use common\tripium\TripiumUpdater;
use yii\console\Controller;

class TripiumController extends Controller
{
    use CleanControllerTrait;

    public function actionUpdate()
    {
        $TripiumUpdater = new TripiumUpdater(
            [
                'mode' => TripiumUpdater::SHOW_ERROR_DETAIL,
                'models' => [
                    ['class' => TrCategories::class],

                    ['class' => TrShows::class],
                    [
                        'class' => TrPrices::class,
                        'arg' => [
                            "start" => date("m/d/Y"),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 90)
                        ]
                    ],
                    ['class' => TrShows::class],

                    ['class' => TrAttractions::class],
                    ['class' => TrAdmissions::class],
                    [
                        'class' => TrAttractionsPrices::class,
                        'arg' => [
                            "start" => date("m/d/Y"),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 90)
                        ]
                    ],
                    ['class' => TrAttractions::class],

                    ['class' => TrPosHotels::class],
                ]
            ]
        );

        $TripiumUpdater->run();

        TrBasket::removeOld();
    }

    public function actionUpdateOnDay()
    {
        //ini_set("memory_limit","512M");
        //ini_set("max_execution_time", "60");

        $TripiumUpdater = new TripiumUpdater(
            [
                'models' => [
//                    ['class' => TrShows::class],
//                    [
//                        'class' => TrPrices::class,
//                        'arg' => ["start" => date("m/d/Y"), "end" => date("m/d/Y", time() + 3600 * 24 * 540)]
//                    ],
//                    ['class' => TrAttractions::class],
//                    ['class' => TrAdmissions::class],
//                    [
//                        'class' => TrAttractionsPrices::class,
//                        'arg' => [
//                            "start" => date("m/d/Y"),
//                            "end" => date("m/d/Y", time() + 3600 * 24 * 360 * 2)
//                        ]
//                    ],
//                    ['class' => TrAttractions::class],
                    [
                        'class' => TrPosHotels::class,
                        'params' => ['updateForce' => true, 'updateImages' => true, 'updateVideo' => true]
                    ],
                    ['class' => TrPosHotels::class],
    	        ]
            ]
        );
        $TripiumUpdater->run();

        $this->markActualImages();
        $this->unMarkDeleteImages();
    }

    public function actionUpdateAfternoon()
    {
        $TripiumUpdater = new TripiumUpdater(
            [
                'models' => [
                    ['class' => TrCategories::class],
                    ['class' => TrShows::class, 'params' => ['updateForce' => true]],
                    ['class' => TrAttractions::class, 'params' => ['updateForce' => true]],
                    ['class' => TrPosHotels::class, 'params' => ['updateForce' => true]],
                ]
            ]
        );

        $TripiumUpdater->run();
    }

    public function actionBasketClean()
    {
        TrBasket::removeOld();
    }
}

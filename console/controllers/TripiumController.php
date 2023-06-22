<?php

namespace console\controllers;

/*use common\models\LocationServices;*/
/*use common\models\priceLine\NewPriceLineHotels;
use common\models\TrAdmissions;
use common\models\TrAttractions;
use common\models\TrAttractionsPrices;
use common\models\TrBasket;*/
use common\models\TrCategories;
/*use common\models\TrLocations;*/
/*use common\models\TrLunchs;
use common\models\TrLunchsCertificates;
use common\models\TrLunchsPrices;
use common\models\TrPosHotels;
use common\models\TrPosHotelsPriceExtra;
use common\models\TrPosHotelsPriceRoom;
use common\models\TrPosPlHotels;
use common\models\TrPosRoomTypes;*/
use common\models\TrPrices;
use common\models\TrShows;
/*use common\models\VacationPackage;*/
use common\tripium\TripiumUpdater;
use DateInterval;
use DateTime;
use yii\console\Controller;
use yii\helpers\Json;

class TripiumController extends Controller
{
    use CleanControllerTrait;

    /*public function actionPriceLine(array $ids = [], $updateForce = false, $date = null)
    {
        $shouldUpdateExternalId = !empty($ids) ? $ids : null;
        $updateForce = (bool)$updateForce;

        $checkIn = (new DateTime())->add(new DateInterval('P1D'));
        $checkOut = (new DateTime())->add(new DateInterval('P2D'));

        if ($date) {
            $checkIn = new DateTime($date);
            $checkOut = (new DateTime($checkIn->format('m/d/Y')))->add(new DateInterval('P1D'));
        }

        $TripiumUpdater = new TripiumUpdater(
            [
                'mode' => TripiumUpdater::SHOW_ERROR_DETAIL,
                'models' => [
                    [
                        'class' => TrPosPlHotels::class,
                        'arg' => [
                            'setStatus' => 1,
                            'check_in' => $checkIn,
                            'check_out' => $checkOut,
                            'rooms' => 1,
                            'adults' => 1,
                            'children' => 0,
                            'sort_by' => 'gs'
                        ],
                        'params' => [
                            'updatePlHotelDetail' => true,
                            'updateForce' => !empty($shouldUpdateExternalId) || $updateForce,
                            'updateOnlyIdExternal' => $shouldUpdateExternalId,
                        ]
                    ],
                ]
            ]
        );
        $TripiumUpdater->run();
    }*/

//    public function actionUpdateTest()
//    {
////        $t = new \common\tripium\Tripium;
////        var_dump($t->getContent('hotel', 85588843));
////        exit();
//    	$TripiumUpdater = new TripiumUpdater([
//    	    'mode' => TripiumUpdater::SHOW_ERROR_DETAIL,
//    		'models' => [
////                ['class' => 'common\models\TrPosHotels', 'params' => ['updateForce' => true, 'updateOnlyIdExternal' => [85588843], 'updateForceImages' => false]],
//////                ['class' => 'common\models\TrPosHotels', 'params' => ['updateForce' => true, 'updateOnlyIdExternal' => [85588843], 'updateForceImages' => false]],
////                ['class' => 'common\models\TrPosHotels', 'params' => ['updateForce' => true, 'updateOnlyIdExternal' => [85588843], 'updateForceImages' => false, 'updateImages' => 'all']],
//                ['class'=>'common\models\TrCategories'],
//                ['class'=>'common\models\TrLocations'],
//                ['class'=>'common\models\LocationServices'],
//                ['class' => 'common\models\TrPosHotels'],
//                ['class' => 'common\models\TrPosHotels', 'params' => ['updateForce' => true, 'updateImages' => true]],
//                ['class'=>'common\models\TrPosRoomTypes'],
//    		    ['class'=>'common\models\TrPosHotelsPriceExtra',
//                    'arg' => [
//                        "start"=>date("m/d/Y"),
//                        "end"=>date("m/d/Y",time()+3600*24*60)
//                    ]
//                ],
//    		    ['class'=>'common\models\TrPosHotelsPriceRoom',
//                    'arg' => [
//                        "start"=>date("m/d/Y"),
//                        "end"=>date("m/d/Y",time()+3600*24*60)
//                    ]
//                ]
//    	    ]
//    	]);
//
//        $TripiumUpdater->run();
//    }

    /*public function actionUpdatePosHotels()
    {
        $TripiumUpdater = new TripiumUpdater(
            [
                'mode' => TripiumUpdater::SHOW_ERROR_DETAIL,
                'models' => [
                    [
                        'class' => TrPosHotels::class,
                        'params' => ['updateForce' => true, 'updateImages' => true, 'updateVideo' => true]
                    ],
                    ['class' => TrPosRoomTypes::class],
                    [
                        'class' => TrPosHotelsPriceExtra::class,
                        'arg' => [
                            "start" => date("m/d/Y"),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 60)
                        ]
                    ],
                    [
                        'class' => TrPosHotelsPriceRoom::class,
                        'arg' => [
                            "start" => date("m/d/Y"),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 60)
                        ]
                    ]
                ]
            ]
        );

        $TripiumUpdater->run();
    }*/

    public function actionUpdate()
    {
        $TripiumUpdater = new TripiumUpdater(
            [
                'mode' => TripiumUpdater::SHOW_ERROR_DETAIL,
                'models' => [
                    ['class' => TrCategories::class],
                    /*['class' => TrLocations::class],*/
                    /*['class' => LocationServices::class],*/

                    ['class' => TrShows::class],
                    [
                        'class' => TrPrices::class,
                        'arg' => [
                            "start" => date("m/d/Y"),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 90)
                        ]
                    ],
                    ['class' => TrShows::class],

                    /*['class' => TrAttractions::class],
                    ['class' => TrAdmissions::class],
                    [
                        'class' => TrAttractionsPrices::class,
                        'arg' => [
                            "start" => date("m/d/Y"),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 90)
                        ]
                    ],
                    ['class' => TrAttractions::class],

                    ['class' => TrLunchs::class],
                    ['class' => TrLunchsCertificates::class],
                    [
                        'class' => TrLunchsPrices::class,
                        'arg' => [
                            "start" => date("m/d/Y"),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 90)
                        ]
                    ],
                    ['class' => TrLunchs::class],

                    ['class' => VacationPackage::class],

                    ['class' => TrPosHotels::class],
                    ['class' => TrPosRoomTypes::class],
                    [
                        'class' => TrPosHotelsPriceExtra::class,
                        'arg' => [
                            "start" => date("m/d/Y"),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 60)
                        ]
                    ],
                    [
                        'class' => TrPosHotelsPriceRoom::class,
                        'arg' => [
                            "start" => date("m/d/Y"),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 60)
                        ]
                    ]*/
                ]
            ]
        );

        $TripiumUpdater->run();

        /*TrBasket::removeOld();*/
    }

    public function actionUpdateOnDay()
    {
        //ini_set("memory_limit","512M");
        //ini_set("max_execution_time", "60");

        $TripiumUpdater = new TripiumUpdater(
            [
                'models' => [
                    ['class' => TrShows::class],
                    [
                        'class' => TrPrices::class,
                        'arg' => ["start" => date("m/d/Y"), "end" => date("m/d/Y", time() + 3600 * 24 * 540)]
                    ],
                    /*['class' => TrAttractions::class],
                    ['class' => TrAdmissions::class],
                    [
                        'class' => TrAttractionsPrices::class,
                        'arg' => [
                            "start" => date("m/d/Y"),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 360 * 2)
                        ]
                    ],
                    ['class' => TrAttractions::class],

                    ['class' => TrLunchs::class],
                    ['class' => TrLunchsCertificates::class],
                    [
                        'class' => TrLunchsPrices::class,
                        'arg' => [
                            "start" => date("m/d/Y"),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 360 * 2)
                        ]
                    ],
                    ['class' => TrLunchs::class],
                    [
                        'class' => TrPosPlHotels::class,
                        'arg' => ['setStatus' => 1],
                        'params' => ['updatePlHotelDetail' => true]
                    ],
                    [
                        'class' => TrPosPlHotels::class,
                        'arg' => [
                            'setStatus' => 1,
                            'check_in' => (new DateTime())->add(new DateInterval('P2D')),
                            'check_out' => (new DateTime())->add(new DateInterval('P3D')),
                            'rooms' => 1,
                            'adults' => 1,
                            'children' => 0,
                            'sort_by' => 'gs'
                        ],
                        'params' => [
                            'updatePlHotelDetail' => true
                        ]
                    ],
                    [
                        'class' => TrPosPlHotels::class,
                        'arg' => [
                            'setStatus' => 1,
                            'check_in' => (new DateTime())->add(new DateInterval('P7D')),
                            'check_out' => (new DateTime())->add(new DateInterval('P8D')),
                            'rooms' => 1,
                            'adults' => 1,
                            'children' => 0,
                            'sort_by' => 'gs'
                        ],
                        'params' => [
                            'updatePlHotelDetail' => true
                        ]
                    ],
                    [
                        'class' => TrPosPlHotels::class,
                        'arg' => [
                            'setStatus' => 1,
                            'check_in' => (new DateTime())->add(new DateInterval('P14D')),
                            'check_out' => (new DateTime())->add(new DateInterval('P15D')),
                            'rooms' => 1,
                            'adults' => 1,
                            'children' => 0,
                            'sort_by' => 'gs'
                        ],
                        'params' => [
                            'updatePlHotelDetail' => true
                        ]
                    ],
                    [
                        'class' => TrPosHotels::class,
                        'params' => ['updateForce' => true, 'updateImages' => true, 'updateVideo' => true]
                    ],
                    ['class' => TrPosHotels::class],
                    ['class' => TrPosRoomTypes::class],
                    [
                        'class' => TrPosHotelsPriceExtra::class,
                        'arg' => [
                            "start" => date("m/d/Y", time() + 3600 * 24 * 60),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 180)
                        ]
                    ],
                    [
                        'class' => TrPosHotelsPriceRoom::class,
                        'arg' => [
                            "start" => date("m/d/Y", time() + 3600 * 24 * 60),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 180)
                        ]
                    ],
                    [
                        'class' => TrPosHotelsPriceExtra::class,
                        'arg' => [
                            "start" => date("m/d/Y", time() + 3600 * 24 * 180),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 300)
                        ]
                    ],
                    [
                        'class' => TrPosHotelsPriceRoom::class,
                        'arg' => [
                            "start" => date("m/d/Y", time() + 3600 * 24 * 180),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 300)
                        ]
                    ],
                    [
                        'class' => TrPosHotelsPriceExtra::class,
                        'arg' => [
                            "start" => date("m/d/Y", time() + 3600 * 24 * 300),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 420)
                        ]
                    ],
                    [
                        'class' => TrPosHotelsPriceRoom::class,
                        'arg' => [
                            "start" => date("m/d/Y", time() + 3600 * 24 * 300),
                            "end" => date("m/d/Y", time() + 3600 * 24 * 420)
                        ]
                    ],
//    	        ['class'=>'common\models\TrHotels', 'params' =>['fullUpdate'=>false], 'arg' => ['arrivalDate'=>date("m/d/Y",time()+3600*24*25), 'departureDate'=>date("m/d/Y",time()+3600*24*50)]],
//    	        ['class'=>'common\models\TrHotels', 'params' =>['fullUpdate'=>false], 'arg' => ['arrivalDate'=>date("m/d/Y",time()+3600*24*50), 'departureDate'=>date("m/d/Y",time()+3600*24*75)]],
//    	        ['class'=>'common\models\TrHotels', 'params' =>['fullUpdate'=>false], 'arg' => ['arrivalDate'=>date("m/d/Y",time()+3600*24*75), 'departureDate'=>date("m/d/Y",time()+3600*24*100)]],*/
                ]
            ]
        );
        $TripiumUpdater->run();

        $this->markActualImages();
        $this->unMarkDeleteImages();
    }

//    public function actionUpdateAllImages()
//    {
//        $TripiumUpdater = new TripiumUpdater([
//            'models' => [
//                ['class' => TrShows::class, 'params' => ['updateForce' => true, 'updateForceImages'=>true]],
//            ]
//        ]);
//
//        $TripiumUpdater->run();
//    }

    public function actionUpdateAfternoon()
    {
        $TripiumUpdater = new TripiumUpdater(
            [
                'models' => [
                    ['class' => TrCategories::class],
                    /*['class' => TrLocations::class],*/
                    /*['class' => LocationServices::class],*/
                    ['class' => TrShows::class, 'params' => ['updateForce' => true]],
                    /*['class' => TrAttractions::class, 'params' => ['updateForce' => true]],
                    ['class' => TrLunchs::class, 'params' => ['updateForce' => true]],
                    ['class' => TrPosHotels::class, 'params' => ['updateForce' => true]],*/
                ]
            ]
        );

        $TripiumUpdater->run();
    }

    /*public function actionBasketClean()
    {
        TrBasket::removeOld();
    }*/

//    public function actionUpdateImages()
//    {
//        $TripiumUpdater = new TripiumUpdater([
//            'models' => [
//                ['class'=> TrShows::class, 'params' =>['__updateForce'=>true, 'updateForceImages'=>true]],
//                ['class'=> TrAttractions::class, 'params' =>['__updateForce'=>true, 'updateForceImages'=>true]],
//                ['class'=> TrLunchs::class, 'params' =>['__updateForce'=>true, 'updateForceImages'=>true]],
//            ]
//        ]);
//
//        $TripiumUpdater->run();
//    }

//    public function actionUpdateTheatersLocations()
//    {
//        $items = TrTheaters::find()->where(['location_lat'=>0])->all();
//        foreach ($items as $item) {
//            $item->updateLocation(1);
//            echo "<pre>"; var_dump(TrTheaters::find()->where(['location_lat'=>0])->count()); echo "</pre>";
//            sleep(1);
//        }
//    }

    /**
     * Update new PL hotels. Runs every minutes.
     */
    /*public function actionPriceLineAddNewItems(): void
    {
        $newPriceLineHotels = NewPriceLineHotels::find()
            ->where(['status' => NewPriceLineHotels::STATUS_NEW])
            ->all();

        foreach ($newPriceLineHotels as $item) {
            $TripiumUpdater = new TripiumUpdater(
                [
                    'mode' => TripiumUpdater::MODE_HIDE_MESSAGE,
                    'models' => [
                        [
                            'class' => TrPosPlHotels::class,
                            'arg' => Json::decode($item->getAttribute('query')),
                            'params' => [
                                'updatePlHotelDetail' => true,
                                'updateOnlyIdExternal' => [$item->getAttribute('external_id')]
                            ]
                        ],
                    ]
                ]
            );
            $TripiumUpdater->run();

            $item->setAttribute('status', NewPriceLineHotels::STATUS_UPLOADED);
            $item->save();
        }
    }*/
}

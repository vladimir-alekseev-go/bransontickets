<?php

namespace console\controllers;

use common\models\logs\CronTask;
/*use common\models\TrAdmissions;
use common\models\TrAttractions;
use common\models\TrAttractionsPrices;
use common\models\TrLunchs;
use common\models\TrLunchsCertificates;
use common\models\TrLunchsPrices;
use common\models\TrPosHotels;
use common\models\TrPosHotelsPriceExtra;
use common\models\TrPosHotelsPriceRoom;
use common\models\TrPosRoomTypes;*/
use common\models\TrPrices;
use common\models\TrShows;
use common\tripium\TripiumUpdater;

trait CronControllerTrait
{
    /**
     * Processing cron tasks. Runs every minutes.
     */
    public function actionCronProgress(): void
    {
        /**
         * @var CronTask $task
         */
        $models = [];

        $task = CronTask::find()->where(['status' => CronTask::STATUS_CREATED])->orderBy('id asc')->one();
        $task->setAttribute('status', CronTask::STATUS_STARTED);
        $task->save();

        if ($task && $task->type === CronTask::TYPE_MARKUP && $task->getDateFrom() && $task->getDateTo()) {
            if (!empty($task->getVendorsId(TrShows::TYPE_ID))) {
                $models = array_merge(
                    $models,
                    [
                        [
                            'class' => TrShows::class,
                            'params' => [
                                'updateOnlyIdExternal' => $task->getVendorsId(TrShows::TYPE_ID)
                            ]
                        ],
                        [
                            'class' => TrPrices::class,
                            'params' => [
                                'updateStart' => $task->getDateFrom(),
                                'updateEnd' => $task->getDateTo(),
                                'updateOnlyIdExternal' => $task->getVendorsId(TrShows::TYPE_ID)
                            ]
                        ],
                    ]
                );
            }

            /*if (!empty($task->getVendorsId(TrAttractions::TYPE_ID))) {
                $models = array_merge(
                    $models,
                    [
                        [
                            'class' => TrAttractions::class,
                            'params' => [
                                'updateOnlyIdExternal' => $task->getVendorsId(TrAttractions::TYPE_ID)
                            ]
                        ],
                        ['class' => TrAdmissions::class],
                        [
                            'class' => TrAttractionsPrices::class,
                            'params' => [
                                'updateStart' => $task->getDateFrom(),
                                'updateEnd' => $task->getDateTo(),
                                'updateOnlyIdExternal' => $task->getVendorsId(TrAttractions::TYPE_ID)
                            ]
                        ],
                    ]
                );
            }

            if (!empty($task->getVendorsId(TrLunchs::TYPE_ID))) {
                $models = array_merge(
                    $models,
                    [
                        [
                            'class' => TrLunchs::class,
                            'params' => [
                                'updateOnlyIdExternal' => $task->getVendorsId(TrLunchs::TYPE_ID)
                            ]
                        ],
                        ['class' => TrLunchsCertificates::class],
                        [
                            'class' => TrLunchsPrices::class,
                            'params' => [
                                'updateStart' => $task->getDateFrom(),
                                'updateEnd' => $task->getDateTo(),
                                'updateOnlyIdExternal' => $task->getVendorsId(TrLunchs::TYPE_ID)
                            ]
                        ],
                    ]
                );
            }

            if (!empty($task->getVendorsId(TrPosHotels::TYPE_ID))) {
                $models = array_merge(
                    $models,
                    [
                        ['class' => TrPosHotels::class],
                        ['class' => TrPosRoomTypes::class],
                        [
                            'class' => TrPosHotelsPriceExtra::class,
                            'params' => [
                                'updateStart' => $task->getDateFrom(),
                                'updateEnd' => $task->getDateTo(),
                                'updateOnlyIdExternal' => $task->getVendorsId(TrPosHotels::TYPE_ID)
                            ]
                        ],
                        [
                            'class' => TrPosHotelsPriceRoom::class,
                            'params' => [
                                'updateStart' => $task->getDateFrom(),
                                'updateEnd' => $task->getDateTo(),
                                'updateOnlyIdExternal' => $task->getVendorsId(TrPosHotels::TYPE_ID)
                            ]
                        ],
                    ]
                );
            }*/
        }

        $TripiumUpdater = new TripiumUpdater(['models' => $models]);
        $TripiumUpdater->run();

        $task->setAttribute('status', CronTask::STATUS_FINISHED);
        $task->save();
    }
}
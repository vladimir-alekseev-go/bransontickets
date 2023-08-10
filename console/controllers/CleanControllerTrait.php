<?php

namespace console\controllers;

use common\models\AttractionsPhotoJoin;
use common\models\ContentFiles;
/*use common\models\HotelsPhotoJoin;
use common\models\LunchsPhotoJoin;
use common\models\RestaurantPhotoJoin;*/
use common\models\ShowsPhotoJoin;
/*use common\models\TrPosHotelsPhotoJoin;
use common\models\TrPosPlHotelsPhotoJoin;*/
use Yii;

trait CleanControllerTrait
{
    /**
     * Mark actual images
     */
    protected function markActualImages(): void
    {
        $ar = [
            ShowsPhotoJoin::class,
            AttractionsPhotoJoin::class,
            /*LunchsPhotoJoin::class,
            RestaurantPhotoJoin::class,
            HotelsPhotoJoin::class,
            TrPosPlHotelsPhotoJoin::class,
            TrPosHotelsPhotoJoin::class,*/
        ];
        $query = Yii::$app->db->createCommand(
            "UPDATE " . ContentFiles::tableName() . " SET old = null"
        );
        echo '<pre>';
        var_dump($query->execute());
        echo '</pre>';
        foreach ($ar as $class) {
            $query = Yii::$app->db->createCommand(
                "UPDATE " . ContentFiles::tableName() . " cf " .
                "INNER JOIN " . $class::tableName() . " pj ON pj.photo_id = cf.id " .
                "SET cf.old = 0 " .
                "WHERE dir = 'items-photos' and cf.id > 0"
            );
            echo '<pre>';
            var_dump($query->execute());
            echo '</pre>';
            $query = Yii::$app->db->createCommand(
                "UPDATE " . ContentFiles::tableName() . " cf " .
                "INNER JOIN " . $class::tableName() . " pj ON pj.preview_id = cf.id " .
                "SET cf.old = 0 " .
                "WHERE dir = 'items-photos-preview' and cf.id > 0"
            );
            echo '<pre>';
            var_dump($query->execute());
            echo '</pre>';
        }
        $query = Yii::$app->db->createCommand(
            "UPDATE " . ContentFiles::tableName() . " cf " .
            "SET cf.old = 1, cf.path_old = cf.path " .
            "WHERE dir = 'items-photos-preview' and cf.id > 0 and cf.old is null"
        );
        echo '<pre>';
        var_dump($query->execute());
        echo '</pre>';
        $query = Yii::$app->db->createCommand(
            "UPDATE " . ContentFiles::tableName() . " cf " .
            "SET cf.old = 1, cf.path_old = cf.path " .
            "WHERE dir = 'items-photos' and cf.id > 0 and cf.old is null"
        );
        echo '<pre>';
        var_dump($query->execute());
        echo '</pre>';

        $query = Yii::$app->db->createCommand(
            "UPDATE " . ContentFiles::tableName() . " cf " .
            "SET cf.path = '' " .
            "WHERE dir = 'items-photos-preview' and cf.id > 0 and cf.old = 1"
        );
        echo '<pre>';
        var_dump($query->execute());
        echo '</pre>';
        $query = Yii::$app->db->createCommand(
            "UPDATE " . ContentFiles::tableName() . " cf " .
            "SET cf.path = '' " .
            "WHERE dir = 'items-photos' and cf.id > 0 and cf.old = 1"
        );
        echo '<pre>';
        var_dump($query->execute());
        echo '</pre>';
    }

    /**
     * Unmark images and delete
     */
    protected function unMarkDeleteImages(): void
    {
        $query = Yii::$app->db->createCommand(
            "UPDATE " . ContentFiles::tableName() . " cf " .
            "SET cf.path = cf.path_old " .
            "WHERE dir = 'items-photos-preview' and cf.id > 0 and cf.old = 1"
        );
        echo '<pre>';
        var_dump($query->execute());
        echo '</pre>';
        $query = Yii::$app->db->createCommand(
            "UPDATE " . ContentFiles::tableName() . " cf " .
            "SET cf.path = cf.path_old " .
            "WHERE dir = 'items-photos' and cf.id > 0 and cf.old = 1"
        );
        echo '<pre>';
        var_dump($query->execute());
        echo '</pre>';

        $part = 100000;

        $count = ContentFiles::find()->where(['old' => 1, 'dir' => 'items-photos'])->count();

        $k = ceil($count / $part);
        var_dump('k:' . $k);

        for ($i = 0; $i <= $k; $i++) {
            $ContentFiles = ContentFiles::find()->where(['old' => 1, 'dir' => 'items-photos'])->limit($part)->all();
            echo '<pre>';
            var_dump(count($ContentFiles));
            echo '</pre>';
            foreach ($ContentFiles as $ContentFile) {
                $ContentFile->delete();
                echo '<pre>';
                var_dump($ContentFile->id);
                echo '</pre>';
            }
        }

        for ($i = 0; $i <= $k; $i++) {
            $ContentFiles = ContentFiles::find()->where(['old' => 1, 'dir' => 'items-photos-preview'])->limit(
                $part
            )->all();
            echo '<pre>';
            var_dump(count($ContentFiles));
            echo '</pre>';
            foreach ($ContentFiles as $ContentFile) {
                $ContentFile->delete();
                echo '<pre>';
                var_dump($ContentFile->id);
                echo '</pre>';
            }
        }
    }

}
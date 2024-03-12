<?php

use common\helpers\General;
use common\models\TrAttractions;
use common\models\TrShows;

/**
 * @var TrShows|TrAttractions $model
 */

$this->context->layout = 'item-detail';
Yii::$app->view->params['model'] = $model;

$this->title = $model->name;

$videos = !empty($model->videos) ? explode(";", $model->videos) : [];
$videos = General::handleVideoLink($videos);
$images = $model->relatedPhotos;
?>
<?= $this->render('@app/views/components/item/menu-content', compact('model', 'ScheduleSlider', 'VPLWidget')) ?>
<?= $this->render('@app/views/components/item/description', compact('model')) ?>
<?= $this->render('@app/views/components/item/gallery', compact('model', 'videos', 'images')) ?>
<?= $this->render('@app/views/components/item/also', compact('showsRecommended')) ?>


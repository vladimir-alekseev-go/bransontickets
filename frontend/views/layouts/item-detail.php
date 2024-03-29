<?php

use common\models\TrAttractions;
use common\models\TrShows;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * @var TrShows|TrAttractions|TrPosHotels|TrPosPlHotels $model
 * @var TrShows[]|TrAttractions[]                       $similar
 * @var string                                          $content
 */

$this->beginContent('@app/views/layouts/main.php');

$model = Yii::$app->view->params['model'];

$theatreName = isset($model->theatre) ? $model->theatre->name : ($model->theatre_name ?? '');
$theatreAddress = isset($model->theatre) ? $model->theatre->address1 : ($model->address ?? '');
$theatreAddress .= isset($model->theatre)
    ? ', ' . $model->theatre->city . ', ' . $model->theatre->state . ' ' . $model->theatre->zip_code : '';

$theatreArrayLine = [];
if (!empty($theatreName)) {
    $theatreArrayLine[] = $theatreName;
}
if (!empty($theatreAddress)) {
    $theatreArrayLine[] = $theatreAddress;
}
?>

<div class="show-detail">
    <div class="fixed">
        <div class="main-info">
            <div class="image-detail">
                <?php if ($model->preview_id) { ?>
                    <img class="preview" src="<?= $model->preview->url ?>" alt="<?= $model->name ?>" itemprop="image"/>
                <?php } else { ?>
                    <img class="preview" src="/img/bransontickets-noimage.png" alt=""/>
                <?php } ?>
            </div>
            <div class="info">
                <h1 itemprop="name"><?= $model->name ?></h1>
                <div class="theatre-info">
                    <div class="item">
                        <img src="/img/map-marker.svg" alt="map marker icon">
                        <span class="theatre">
                            <?= implode(', ', $theatreArrayLine) ?>
                        </span>
                    </div>
                    <div class="item">
                        <img src="/img/phone.svg" class="phone-img" alt="phone icon"><span class="phone"><?= $model->phone ?></span>
                    </div>
                    <?php if (isset($model->show_length) || (isset($model->intermissions) && $model->intermissions !==
                        'null')) { ?>
                        <div class="item">
                            <img src="/img/time.svg" alt="time icon">
                                <span class="time">
                                    <?php if ($model->show_length) { ?>
                                        <?= $model->show_length ?> min.
                                    <?php } ?>
                                    <?php $intermissions = Json::decode($model->intermissions); ?>
                                    <b><?php if (!empty($intermissions['count'])) { ?>
                                        (<?= $intermissions['count'] ?> Intermission for <?= $intermissions['length'] ?>min)
                                    <?php } ?></b>
                                </span>
                        </div>
                    <?php } ?>
                </div>
                <?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
                    <?php if (!empty($model->check_in) || !empty($model->check_out)) { ?>
                        <div class="check-time">
                            <?php if (!empty($model->check_in)) { ?>
                                <div class="item">
                                    <img src="/img/time.svg" alt="time icon"> Check In: <?= $model->getCheckIn() ?>
                                </div>
                            <?php } ?> 
                            <span> - </span>
                            <?php if (!empty($model->check_out)) { ?>
                                <div class="item">
                                    <img src="/img/time.svg" alt="time icon"> Check Out: <?= $model->getCheckOut() ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } elseif (!empty($model->categories)) { ?>
                    <div class="categories">
                        <?php foreach ($model->categories as $category) {
                            echo Html::a(
                                $category->name,
                                [$this->context->id . '/index', 's[c]' => [$category->id_external]],
                                ['class' => 'tag text-uppercase']
                            );
                        } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <?= $content ?>
</div>

<?php $this->endContent();

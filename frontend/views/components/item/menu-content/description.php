<?php

use common\models\TrAttractions;
use common\models\TrShows;

/**
 * @var TrShows|TrAttractions $model
 */

?>

<div id="description" role="tabpanel" aria-labelledby="description-tab" class="tab-pane">
    <div class="fixed">
        <div class="description-panel">
            <div class="row">
                <div class="col-sm-6">
                    <div class="title">Description</div>
                    <div class="js-description-short description-short description"><?= $model->getDescriptionShort(120) ?>
                        <div class="view-full-description">
                            <a href="#" onclick="$('.js-description-short, .js-description-full').toggle('slow');return false;">View full Description <i class="fa fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="js-description-full description-full description"><?= $model->description ?>
                        <div class="hide-full-description">
                            <a href="#" onclick="$('.js-description-short, .js-description-full').toggle('slow');return false;">Hide full Description <i class="fa fa-angle-up"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <?php if (!empty($model->directions)) { ?>
                        <div class="title">Directions</div>
                        <div class="description"><?= $model->directions ?></div>
                    <?php } ?>
                    <?php if (!empty($model->amenities)) { ?>
                        <div class="title">Amenities</div>
                        <div class="description"><?= $model->amenities ?></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

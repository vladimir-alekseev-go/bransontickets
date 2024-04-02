<?php

use common\models\TrAttractions;
use common\models\TrShows;

/**
 * @var TrShows|TrAttractions $model
 */

?>
<div class="fixed">
    <h2 class="text-center text-uppercase mb-4 fw-bold">About event</h2>
<div class="shadow-block margin-block white-block">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation">
            <a href="#description" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" role="tab"
               aria-controls="description" aria-selected="true" class="active">Description</a>
        </li>
        <?php if (!empty($model->voucher_procedure)) { ?>
            <li role="presentation">
                <a href="#voucher-exchange" id="voucher-exchange-tab" data-bs-toggle="tab" data-bs-target="#voucher-exchange"
                   role="tab" aria-controls="voucher-exchange" aria-selected="false">Voucher Exchange</a>
            </li>
        <?php } ?>
        <?php if (!empty($model->getCancellationPolicyText())) { ?>
            <li role="presentation">
                <a href="#cancellation-policy" id="cancellation-policy-tab" data-bs-toggle="tab"
                   data-bs-target="#cancellation-policy" role="tab"
                   aria-controls="cancellation-policy" aria-selected="false">Cancellation policy</a>
            </li>
        <?php } ?>
    </ul>
    <div class="tab-content">
        <?php if (!empty($model->description)) { ?>
            <div id="description" role="tabpanel" aria-labelledby="description-tab" class="tab-pane active">
                <div class="description-panel">
                    <?php if (!empty($model->directions)) { ?>
                        <div class="title">Directions</div>
                        <div class="description"><?= $model->directions ?></div>
                    <?php } ?>

                    <?php if (!empty($model->amenities)) { ?>
                        <div class="title">Amenities</div>
                        <div class="description"><?= $model->amenities ?></div>
                    <?php } ?>
                    <?php if (!empty($model->description)) { ?>
                        <div class="title">Description</div>
                    <?php } ?>
                    <div class="js-description-short description-short description">
                        <?= $model->getDescriptionShort(520, ['br']) ?>
                        <?php if (strlen($model->getDescriptionShort(520)) > 500) { ?>
                            <div class="view-full-description text-center pt-2">
                                <a onclick="$('.js-description-short, .js-description-full').toggle('slow');return false;">
                                    View full Description <span class="icon br-t-points"></span>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="js-description-full description-full description"><?= $model->description ?>
                        <div class="hide-full-description text-center pt-2">
                            <a onclick="$('.js-description-short, .js-description-full').toggle('slow');return false;">
                                Hide full Description <span class="icon br-t-points"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if (!empty($model->voucher_procedure)) { ?>
            <div id="voucher-exchange" role="tabpanel" aria-labelledby="voucher-exchange-tab" class="tab-pane">
                <div class="description-panel">
                    <div class="description"><?= $model->voucher_procedure ?></div>
                </div>
            </div>
        <?php } ?>
        <?php if (!empty($model->getCancellationPolicyText())) { ?>
            <div id="cancellation-policy" role="tabpanel" aria-labelledby="cancellation-policy-tab" class="tab-pane">
                <div class="description-panel">
                    <div class="description"><?= $model->getCancellationPolicyText() ?></div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</div>

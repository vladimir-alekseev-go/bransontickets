<?php

$Search = $this->context->search;
$rangePrice = $this->context->rangePrice;
$categories = $this->context->categories;

?>

<div class="fixed">
    <div class="row">
        <div class="col-lg-3">
            <?= $this->render('@app/views/components/filter', compact('Search', 'categories', 'rangePrice')) ?>
        </div>
        <div class="col-lg-9">
            <div id="show-list" class="vacation-packages-list">
                <?= $this->render('items') ?>
            </div>
        </div>
    </div>
</div>
<?= $this->render('@common/views/pagination-btn', ['pagination' => $this->context->pagination]) ?>

<?php $this->context->assetRegister(); ?>

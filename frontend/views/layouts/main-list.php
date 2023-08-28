<?php

/**
 * @var string $content
 */

$this->beginContent('@app/views/layouts/main.php');

?>

<div class="filter-up">
    <div class="fixed">
        <div class="row">
            <div class="col-lg-3 order-1 pt-lg-3 mb-3 text-center text-sm-start">
                <h1><b><?= $this->title ?></b></h1>
            </div>
            <div class="col-lg-9 order-2 text-center text-sm-end pt-lg-4 mb-3">
                <div class="row">
                    <div class="col-md-8 col-sm-6">
                        <?= $this->render('@app/views/components/filter-by-name') ?>
                    </div>
                    <div class="col-md-4 col-sm-6"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $content; ?>
<?php $this->endContent();

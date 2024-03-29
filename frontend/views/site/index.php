<?php
/** @var array $showsRecommended */
/** @var array $showsFeatured */

use yii\helpers\Url;

?>
<?= $this->render('main-page/main-banner') ?>
<?= $this->render('main-page/main-info-block') ?>
<div class="slider-name mb-3">Featured</div>
<?= $this->render(
    'main-page/featured',
    [
        'showsFeatured'   => $showsFeatured,
        'id'              => 'feature-id',
        'showDescription' => true,
    ]
) ?>
<div class="slider-name mb-3">Recommended</div>
<div class="recommended">
    <?= $this->render(
        'main-page/featured',
        [
            'showsFeatured'   => $showsRecommended,
            'id'              => 'recommend-id',
            'showDescription' => true,
        ]
    ) ?>
</div>
<div class="text-center mb-5">
    <a class="btn btn-secondary" href="<?= Url::to(['site/search'])?>">Find more</a>
</div>

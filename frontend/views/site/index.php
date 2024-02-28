<?php
/** @var array $showsRecommended */
?>
<?= $this->render('main-page/main-banner') ?>
<?= $this->render('main-page/main-info-block') ?>
<div class="slider-name mb-3">Featured</div>
<?= $this->render('main-page/featured', ['showsFeatured' => $showsFeatured, 'id' => 'feature-id']) ?>
<div class="slider-name mb-3">Recommended</div>
<div class="recommended">
<?= $this->render('main-page/featured', ['showsFeatured' => $showsRecommended, 'id' => 'recommend-id']) ?>
</div>

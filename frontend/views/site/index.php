<?= $this->render('main-page/main-banner') ?>
<?= $this->render('main-page/main-info-block') ?>
<?= $this->render('main-page/featured', compact('showsFeatured')) ?>
<?= $this->render('@app/views/components/recommended', ['showsRecommended' => $showsRecommended, 'recommended' => true]) ?>

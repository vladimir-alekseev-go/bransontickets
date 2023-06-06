<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $content string */
if (Yii::$app->controller->action->id === 'login') {
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {
    backend\assets\AppAsset::register($this);
    dmstr\web\AdminLteAsset::register($this);

    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="skin-blue">
    <?php $this->beginBody() ?>
    <div class="wrapper">


        <?= $this->render('header.php') ?>

        <div class="wrapper row-offcanvas row-offcanvas-left">
            <?= $this->render('left.php') ?>
            <?= $this->render('content.php', ['content' => $content]) ?>
        </div>
    </div>

    <?php $this->endBody() ?>

    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>

<?php

use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $content string */

AppAsset::register($this);

$this->title = 'Branson Tickets';
$this->beginPage() ?><!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" itemscope itemtype="http://schema.org/WebPage">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="<?= $this->title ?>"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="<?= Yii::$app->request->absoluteUrl ?>"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.css"
          rel="stylesheet"  type='text/css'>
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $this->render('menu-general') ?>
<div class="wrapper-main js-wrapper-main">
    <?= $this->render('header') ?>
    <main>
        <div class="header-height-fixed-block"></div>
        <?= $content ?>
    </main>
    <?= $this->render('footer') ?>
</div>
<?php $this->endBody() ?>
<?= $this->blocks['before-end-body'] ?? '' ?>
</body>
</html>
<?php $this->endPage() ?>

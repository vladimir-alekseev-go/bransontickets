<?php

use common\widgets\yii2CookieConsent\CookieConsent;use frontend\assets\AppAsset;
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
        <?php if (!empty(Yii::$app->params['dotdigital']['addressbookid'])
            && !empty(Yii::$app->params['dotdigital']['name'])) { ?>
            <?= $this->render(
                'dotdigital/code',
                [
                    'name' => Yii::$app->params['dotdigital']['name'],
                    'addressBookId' => Yii::$app->params['dotdigital']['addressbookid'],
                ]
            ) ?>
        <?php } ?>
        <?= CookieConsent::widget([
          'name' => 'cookie_consent_status',
          'path' => '/',
          'domain' => '',
          'expiryDays' => 365,
          'message' => '',
          'save' => 'Save',
          'acceptAll' => 'Accept all',
          'controlsOpen' => 'Change',
          'detailsOpen' => 'Cookie Details',
          'learnMore' => 'Cookies Policy',
          'visibleControls' => false,
          'visibleDetails' => false,
          'link' => '/cookies-policy/',
          'consent' => [
              'cookie_necessary' => [
                  'label' => 'Necessary',
                  'checked' => true,
                  'disabled' => true
              ],
              'cookie_statistics' => [
                  'label' => 'Statistics',
                  'checked' => true,
                  'cookies' => [
                      ['name' => '_ga'],
                      ['name' => '_gat', 'domain' => '', 'path' => '/'],
                      ['name' => '_gid', 'domain' => '', 'path' => '/']
                  ]
              ]
          ]
      ]) ?>
    </main>
    <?= $this->render('footer') ?>
</div>
<?php $this->endBody() ?>
<?= $this->blocks['before-end-body'] ?? '' ?>
<script src="https://r1.for-email.com/DM-3255854826-01/ddgtag.js"></script>
</body>
</html>
<?php $this->endPage() ?>

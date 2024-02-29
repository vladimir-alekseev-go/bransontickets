<?php

use yii\helpers\Url;

?>

<div id="menu-general" class="menu-general">
    <div class="text-end">
        <span id="menu-up-control-close" class="menu-up-control-close">
            <span class="icon br-t-x"></span>
        </span>
    </div>
    <ul class="menu-main">
        <li><a href="<?= Url::to(['shows/index']) ?>">Shows</a></li>
        <li><a href="<?= Url::to(['attractions/index']) ?>">Attractions</a></li>
        <li><a href="<?= Url::to(['lodging/index']) ?>">Lodging</a></li>
        <li><a href="<?= Url::to(['packages/index']) ?>">Vacation Packages</a></li>
        <li class="d-inline-block d-lg-none"><a href="<?= Url::to(['site/about']) ?>">About Us</a></li>
        <li class="d-inline-block d-lg-none"><a href="<?= Url::to(['site/search']) ?>">Search</a></li>
        <li class="d-inline-block d-lg-none">
            <a href="tel:417-337-8455">
                <span class="icon br-t-smartphone"></span> 417-337-8455
            </a>
        </li>
    </ul>
    <?php if (Yii::$app->user->isGuest) { ?>
        <div class="text-center mb-3"><a href="<?= Url::to(['site/signup']) ?>" class="btn btn-fifth px-5">Sign up</a></div>
        <div class="text-center"><a href="<?= Url::to(['site/login']) ?>" class="btn btn-fourth px-5">Sign in</a></div>
    <?php } ?>
</div>
<div class="menu-general-fon"></div>
<div class="menu-general-fon-click js-menu-general-fon-click"></div>

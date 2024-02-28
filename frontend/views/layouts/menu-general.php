<?php

use yii\helpers\Url;

?>

<div id="menu-general" class="menu-general">
    <div id="menu-up-control-close" class="menu-up-control-close">
        <i class="fa fa-angle-right"></i>
    </div>
    <ul class="menu-main">
        <li><a href="<?= Url::to(['shows/index']) ?>">Shows <div class="line"></div></a></li>
        <li><a href="<?= Url::to(['attractions/index']) ?>">Attractions <div class="line"></div></a></li>
        <li><a href="<?= Url::to(['lodging/index']) ?>">Lodging <div class="line"></div></a></li>
        <li><a href="<?= Url::to(['packages/index']) ?>">Vacation Packages <div class="line"></div></a></li>
        <li><a href="<?= Url::to(['site/signup']) ?>">Sign up <div class="line"></div></a></li>
        <li><a href="<?= Url::to(['site/login']) ?>">Sign in <div class="line"></div></a></li>
        <li class="d-inline-block d-lg-none"><a href="<?= Url::to(['site/about']) ?>">About Us</a></li>
        <li class="d-inline-block d-lg-none"><a href="<?= Url::to(['site/search']) ?>">Search</a></li>
        <li class="d-inline-block d-lg-none">
            <a href="tel:417-337-8455">
                <img src="/img/phone.svg" alt="phone icon"> 417-337-8455
            </a>
        </li>
    </ul>
</div>

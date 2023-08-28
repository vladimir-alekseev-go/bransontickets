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
        <li><a href="#">Lodging <div class="line"></div></a></li>
    </ul>
    <div class="menu-phone">
        <img src="/img/phone.svg" alt="phone icon">
        <a href="tel:417-337-8455" class="phone">417-337-8455</a>
    </div>
    <div class="menu-footer">
        <div class="item">
            <a href="https://bransonrestaurants.com/" target="_blank"><img src="/img/branson-restaurants-logo.png" alt="branson restaurants logo"></a>
        </div>
        <div class="item">
            <a href="https://ibranson.com/" target="_blank"><img src="/img/ibranson-logo.png" alt="ibranson logo"></a>
        </div>
    </div>
</div>

<?php

use yii\helpers\Url;

?>
<header class="header">
    <div class="fixed">
        <div class="row">
            <div class="col">
                <div class="header-logo">
                    <a id="menu-up-control" class="menu-up-control menu-up-control-is-close">
                        <i class="fa fa-bars"></i>
                        <i class="fa fa-times"></i>
                    </a>
                    <a href="/" class="bransontickets-logo">
                        <img src="/img/bransontickets-logo.png" alt="Branson Tickets">
                    </a>
                    <div class="header-search input-search">
                        <input type="text" id="search" class="form-control" name="Search[name]" value="" aria-required="true" aria-invalid="false" placeholder="Search">
                    </div>
                </div>
            </div>
        
            <div class="col">
                <div class="header-menu-right">
                    <img src="/img/phone.svg" alt="phone icon">
                    <a href="tel:417-337-8455" class="header-phone">417-337-8455</a>
                    <div class="auth-item">
                        <a class="btn sign-up" href="<?= Url::to(['auth/sign-up']) ?>">Sign up</a>
                    </div>
                    <div class="auth-item">
                        <a class=" btn sign-in" href="<?= Url::to(['auth/sign-in']) ?>">Sign in</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="menu-general" class="menu-general menu-general-control-is-close">
            <div class="search-up">
                <form action="#" method="get" data-hs-cf-bound="true">
                    <div class="form-group header-search input-search">
                        <input type="text" class="form-control" name="Search[name]" value="" aria-required="true" aria-invalid="false" placeholder="Search">
                    </div>
                </form>
            </div>
            <ul class="menu-main">
                <li><a href="#">Shows</a></li>
                <li><a href="#">Attractions</a></li>
                <li><a href="#">Lodging</a></li>
                <li><a href="#">Dining</a></li>
                <li><a href="#">Sign up</a></li>
                <li><a href="#">Sign in</a></li>
            </ul>
            <a class="menu-main-phone" href="tel:417-337-8455">
                <img src="/img/phone.svg" alt="phone icon">
                417-337-8455
            </a>
        </div>
    </div>
    <div class="header-menu-bottom-line">
        <div class="fixed">
            <div class="header-menu-bottom">
                <a class="btn header-menu-item" href="#">Shows</a>
                <a class="btn header-menu-item" href="#">Attractions</a>
                <a class="btn header-menu-item" href="#">Lodging</a>
                <a class="btn header-menu-item" href="#">Dining</a>
            </div>
        </div>
    </div>
</header>

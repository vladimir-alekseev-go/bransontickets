<?php

use yii\helpers\Url;

?>
<header class="header">
    <div class="fixed">
        <div class="row">
            <div class="col-4">
                <div class="auth-items">
                    <div class="auth-item">
                        <a class="btn sign-up" href="<?= Url::to(['auth/sign-up']) ?>">Sign up</a>
                    </div>
                    <div class="auth-item">
                        <a class=" btn sign-in" href="<?= Url::to(['auth/sign-in']) ?>">Sign in</a>
                    </div>
                </div>
            </div>
            
            <div class="col-4">
                <div class="header-menu-phone">
                    <img src="img/phone.svg" alt="phone icon">
                    <a href="tel:417-337-8455" class="header-phone">417-337-8455</a>
                </div>
            </div>
                
            <div class="col-4">
                <a id="menu-up-control" class="menu-up-control">
                    <i class="fa fa-bars"></i>
                </a>
            </div>
        </div>
        <div id="menu-general" class="menu-general">
            <div id="menu-up-control-close" class="menu-up-control-close">
                <i class="fa fa-angle-right"></i>
            </div>
            <ul class="menu-main">
                <li><a href="#">Shows <div class="line"></div></a></li>
                <li><a href="#">Attractions <div class="line"></div></a></li>
                <li><a href="#">Lodging <div class="line"></div></a></li>
                <li><a href="#">Dining <div class="line"></div></a></li>
                <li><a href="#" class="d-md-none">Sign up</a></li>
                <li><a href="#" class="d-md-none">Sign in</a></li>
            </ul>
            <div class="menu-footer">
                <a href="https://bransonrestaurants.com/" target="_blank"><img src="img/branson-restaurants-logo.png" alt="branson restaurants logo"></a>
                <a href="https://ibranson.com/" target="_blank"><img src="img/ibranson-logo.png" alt="ibranson logo"></a>
            </div>
        </div>
    </div>
</header>

<?php

use yii\helpers\Url;

?>
<header class="header">
    <div class="fixed">
        <div class="row">
            <div class="col-4 order-1 order-md-0 d-none d-md-block">
                <div class="auth-items">
                    <div class="auth-item">
                        <a class="btn sign-up" href="#">Sign up</a>
                    </div>
                    <div class="auth-item">
                        <a class=" btn sign-in" href="#">Sign in</a>
                    </div>
                </div>
            </div>
            
            <div class="col-10 col-md-4 order-0 order-md-1">
                <div class="header-menu-phone">
                    <img src="img/phone.svg" alt="phone icon">
                    <a href="tel:417-337-8455" class="header-phone">417-337-8455</a>
                </div>
            </div>
                
            <div class="col-2 col-md-4 order-2">
                <a id="menu-up-control" class="menu-up-control">
                    <i class="fa fa-bars"></i>
                </a>
            </div>
        </div>
    </div>
</header>

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
    </div>
</header>

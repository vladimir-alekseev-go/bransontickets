<?php

use yii\helpers\Url;

?>
<header class="header">
    <div class="fixed">
        <div class="row">
            <div class="col-4 order-1 order-md-0 d-none d-md-block">
                <?php if (Yii::$app->request->url === Url::to('/')) { ?>
                    <div class="auth-items">
                        <div class="auth-item">
                            <a class="btn sign-up" href="#">Sign up</a>
                        </div>
                        <div class="auth-item">
                            <a class=" btn sign-in" href="#">Sign in</a>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="header-menu-phone detail">
                        <img src="/img/phone.svg" alt="phone icon">
                        <a href="tel:417-337-8455" class="header-phone">417-337-8455</a>
                    </div>
                <?php } ?>
            </div>
            
            <div class="col-10 col-md-4 order-0 order-md-1">
                <?php if (Yii::$app->request->url === Url::to('/')) { ?>
                    <div class="header-menu-phone">
                        <img src="/img/phone.svg" alt="phone icon">
                        <a href="tel:417-337-8455" class="header-phone">417-337-8455</a>
                    </div>
                <?php } else { ?>
                    <a href="<?= Url::to('/') ?>" class="bransontickets-logo">
                        <img src="/img/bransontickets-logo.png" alt="Branson Tickets">
                    </a>
                <?php } ?>
            </div>
                
            <div class="col-2 col-md-4 order-2">
                <a id="menu-up-control" class="menu-up-control">
                    <i class="fa fa-bars"></i>
                </a>
            </div>
        </div>
    </div>
</header>

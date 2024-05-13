<?php

use common\models\TrBasket;
use common\models\User;
use yii\helpers\Url;

$basket = TrBasket::build();

?>
<header class="header">
    <div class="fixed">
        <div class="header-content justify-content-center align-items-center">
            <a href="<?= Url::to('/') ?>" class="bransontickets-logo">
                <img src="/img/bransontickets-logo.png" alt="Branson Tickets">
            </a>
            <div class="menu-up d-none d-lg-block">
                <a href="<?= Url::to(['site/about']) ?>">About Us</a>
                <a href="<?= Url::to(['site/search']) ?>">Search</a>
            </div>
            <div class="header-message">
                <span class="bg d-none d-sm-inline-block">
                    <span class="d-none d-xxl-inline-block message">
                        Your #1 source for tickets in Branson, Missouri
                    </span>
                    <span class="phone">
                        <span class="icon br-t-smartphone"></span>
                        <a href="tel:417-337-4814">417-337-4814</a>
                    </span>
                </span>
            </div>
            <div>
                <?php if (!Yii::$app->user->isGuest) { ?>
                    <div class="auth-items">
                        <div class="auth-item">
                            <?php
                            /**
                             * @var User $user
                             */
                            $user = Yii::$app->user->identity;
                            ?>
                            <a class="d-block" href="<?= Url::to(['profile/index']) ?>">
                                <span class="fa fa-user"></span>
                            </a>
                        </div>
                        <div class="auth-item">
                            <a href="<?= Url::to(['site/logout']) ?>"><i class="fa fa-sign-out"></i></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div>
                <a id="menu-up-control" class="menu-up-control">
                    <i class="icon br-t-menu"></i>
                </a>
                <?php if ($basket->getTotalCount()) {?>
                    <a href="<?= Url::to(['order/cart']) ?>" class="btn-basket">
                        <i class="fa fa-shopping-cart"></i><span class="count"><?= $basket->getTotalCount() ?></span>
                    </a>
                <?php }?>
            </div>
        </div>
    </div>
</header>

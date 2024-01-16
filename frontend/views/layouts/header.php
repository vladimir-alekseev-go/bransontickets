<?php

use common\models\TrBasket;
use common\models\User;
use yii\helpers\Url;

$basket = TrBasket::build();

?>
<header class="header">
    <div class="fixed">
        <ul class="menu-up">
            <li><a href="/">Home</a></li>
            <li><a href="<?= Url::to(['site/about']) ?>">About Us</a></li>
            <li><a href="<?= Url::to(['site/search']) ?>">Search</a></li>
        </ul>
        <div class="row">
            <div class="col-4 order-1 order-md-0 d-none d-md-flex align-items-center">
                <?php if (Yii::$app->request->url === Url::to('/') || Yii::$app->request->url === Url::to(['profile/index']) ||
                    Yii::$app->request->url === Url::to(['profile/change-password']) || Yii::$app->request->url === Url::to(['profile/edit'])) { ?>
                        <?php if (Yii::$app->user->isGuest) { ?>
                            <div class="blue">
                                Your #1 source for tickets in Branson, Missouri
                            </div>
                        <?php } else { ?>
                        <div class="auth-items">
                            <div class="auth-item">
                                <?php
                                /**
                                 * @var User $user
                                 */
                                $user = Yii::$app->user->identity;
                                ?>
                                <a class="d-block" href="<?= Url::to(['profile/index']) ?>">
                                    <strong><?= trim($user->first_name . ' ' . $user->last_name) ?></strong>
                                </a>
                            </div>
                            <div class="auth-item">
                                <a href="<?= Url::to(['site/logout']) ?>"><i class="fa fa-sign-out"></i></a>
                            </div>
                        </div>
                        <?php } ?>
                <?php } else { ?>
                    <div class="header-menu-phone detail">
                        <img src="/img/phone.svg" alt="phone icon">
                        <a href="tel:417-337-8455" class="header-phone">417-337-8455</a>
                    </div>
                <?php } ?>
            </div>

            <div class="col-8 col-md-4 order-0 order-md-1 d-flex align-items-center">
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

            <div class="col-4 col-md-4 order-2">
                <a id="menu-up-control" class="menu-up-control">
                    <i class="fa fa-bars"></i>
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

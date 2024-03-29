<?php

use common\helpers\General;
use yii\helpers\Url;
use common\models\Users;

/**
 * @var Users $user
 * @var array $wishList
 * @var array $orders
 */

$this->title = trim($user->first_name . ' ' . $user->last_name);

?>

<div class="fixed">
    <h1 class="h2"><strong><?= $this->title ?></strong></h1>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <span class="d-inline-block me-4">
                <i class="fa fa-envelope"></i> <?= $user->email ?>
            </span>
            <span class="d-inline-block me-4">
                <i class="icon br-t-smartphone"></i> <?= General::formatPhoneNumber($user->phone) ?>
            </span>
            <span class="d-inline-block me-4">
                <i class="fa fa-map-marker"></i> <?= $user->getAddress() ?>
            </span>
        </div>
        <div class="col-lg-4 mb-3 text-lg-end">
            <a href="<?= Url::to(['profile/change-password']) ?>" class="text-white me-3 d-inline-block">
                <i class="fa fa-unlock"></i> <strong>Change password</strong>
            </a>
            <a href="<?= Url::to(['profile/edit']) ?>" class="text-white">
                <i class="fa fa-edit"></i> <strong>Manage profile</strong>
            </a>
        </div>
    </div>

    <?php if (!empty($orders)) { ?>
        <h4 class="mb-2"><strong>Orders</strong></h4>
        <?= $this->render('orders-list', ['orders' => $orders]) ?>
    <?php } ?>
</div>

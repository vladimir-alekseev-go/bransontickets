<?php

use common\helpers\General;
use yii\helpers\Url;
use common\models\Users;

/**
 * @var Users $model
 * @var array $wishList
 */

$this->title = trim($model->first_name . ' ' . $model->last_name);

?>

<div class="fixed">
    <h1 class="h2"><strong><?= $this->title ?></strong></h1>
    <div class="row">
        <div class="col-lg-8 mb-3">
            <span class="d-inline-block me-4">
                <i class="fa fa-envelope"></i> <?= $model->email ?>
            </span>
            <span class="d-inline-block me-4">
                <i class="fa fa-phone"></i> <?= General::formatPhoneNumber($model->phone) ?>
            </span>
            <span class="d-inline-block me-4">
                <i class="fa fa-map-marker"></i> <?= $model->getAddress() ?>
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
</div>

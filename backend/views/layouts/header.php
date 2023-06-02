<?php

use webvimark\modules\UserManagement\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<header class="main-header">

    <?= Html::a(Yii::$app->name, Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle">
                        <span class="hidden-xs"><?= User::getCurrentUser($fromSingleton = true)->username ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>

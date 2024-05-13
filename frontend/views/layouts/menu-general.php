<?php

use yii\helpers\Url;

?>

<div id="menu-general" class="menu-general">
    <div class="text-end">
        <span id="menu-up-control-close" class="menu-up-control-close">
            <span class="icon br-t-x"></span>
        </span>
    </div>
    <div class="search-menu mb-3">
        <form action="<?= Url::to(['site/search']) ?>" method="get">
            <button type="submit" class="btn btn-link"><span class="icon br-t-search"></span></button>
            <div class="form-group input-search">
                <input type="text" class="form-control" name="q" value="" placeholder="Start typing to search">
            </div>
        </form>
    </div>
    <ul class="menu-main">
        <li><a href="<?= Url::to(['shows/index']) ?>">Shows</a></li>
        <li><a href="<?= Url::to(['attractions/index']) ?>">Attractions</a></li>
        <li><a href="<?= Url::to(['lodging/index']) ?>">Lodging</a></li>
        <li><a href="<?= Url::to(['packages/index']) ?>">Vacation Packages</a></li>
        <li class="d-inline-block d-lg-none"><a href="<?= Url::to(['site/about']) ?>">About Us</a></li>
        <li class="d-inline-block d-lg-none"><a href="<?= Url::to(['site/search']) ?>">Search</a></li>
    </ul>
    <?php if (Yii::$app->user->isGuest) { ?>
        <div class="text-center mb-3">
            <a href="<?= Url::to(['site/signup']) ?>" class="btn btn-primary px-5 w-100">Sign up</a>
        </div>
        <div class="text-center mb-3">
            <a href="<?= Url::to(['site/login']) ?>" class="btn btn-secondary px-5 w-100">Sign in</a>
        </div>
    <?php } ?>
    <div class="phone">
        <a href="tel:417-337-4814" class="text-center btn btn-secondary d-block">
            <span class="icon br-t-smartphone"></span> 417-337-4814
        </a>
    </div>
</div>
<div class="menu-general-fon"></div>
<div class="menu-general-fon-click js-menu-general-fon-click"></div>

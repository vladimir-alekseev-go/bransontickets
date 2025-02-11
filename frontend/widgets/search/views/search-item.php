<?php

use common\models\TrAttractions;
use common\models\TrPosHotels;
use common\models\TrShows;

/**
 * @var TrShows|TrAttractions|TrPosHotels $item
 */
?>

<div class="it">
    <?php if ($item->preview) { ?>
        <a class="img" href="<?= $item->getUrl() ?>"><img src="<?= $item->preview->url ?>" alt=""/></a>
    <?php } else { ?>
        <a class="img img-empty" href="<?= $item->getUrl() ?>"><img src="/img/bransontickets-noimage.png" alt=""/></a>
    <?php } ?>
    <div class="data">
        <a href="<?= $item->getUrl() ?>" class="title"><?= $item->name ?></a>
        <div class="place"><?= $item->theatre->name ?? '' ?></div>
        <div class="description"><?= $item->description ?></div>
        <a href="<?= $item->getUrl() ?>" class="detail">
            View detailed information <i class="fa fa-arrow-right"></i>
        </a>
    </div>
</div>

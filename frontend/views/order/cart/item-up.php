<?php

use common\models\Package;
use common\models\TrAttractions;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use common\models\TrShows;

/**
 * @var Package                                         $package
 * @var TrShows|TrAttractions|TrPosPlHotels|TrPosHotels $item
 */

?>
<a class="img" href="<?= $item->getUrl() ?>">
    <?php if (!empty($item->preview_id)) { ?>
        <img src="<?= $item->preview->url ?>" alt=""/>
    <?php } else { ?>
        <img src="/img/bransontickets-noimage.png" alt=""/>
    <?php } ?>
</a>
<a href="<?= $item->getUrl() ?>">
    <div class="name"><?= $item->name ?></div>
</a>

<?php if (in_array($item::TYPE, [TrPosHotels::TYPE, TrPosPlHotels::TYPE], true)) { ?>
    <div class="d-block d-lg-none ps-2 overflow-hidden">
        <i class="fa fa-map-marker"></i> <small>Location</small>
        <div class="ms-3"><small><?= $item->theatre->getSearchAddress() ?></small></div>
    </div>
    <div class="clear-both d-block d-lg-none"></div>
    <div class="row mb-3">
        <div class="col-5 d-none d-lg-block">
            <i class="fa fa-map-marker"></i> <small>Location</small>
            <div class="ms-3"><small><?= $item->theatre->getSearchAddress() ?></small></div>
        </div>
        <div class="col-12 col-lg-7">
            <div class="check-in-out">
                <div class="float-start me-1">
                    <i class="fa fa-calendar"></i>
                </div>
                <div class="float-start">
                    <div><small>Check in</small></div>
                    <div><small><?= $package->getStartDataTime()->format('D, M d, Y') ?></small></div>
                    <div><small><?= $package->getItem()->getCheckIn() ?></small></div>
                </div>
                <div class="float-start me-1 ms-1">
                    <br>
                    -
                </div>
                <div class="float-start">
                    <div><small>Check out</small></div>
                    <div><small><?= $package->getEndDataTime()->format('D, M d, Y') ?></small></div>
                    <div><small><?= $package->getItem()->getCheckOut() ?></small></div>
                </div>
                <div class="float-start ms-2">
                    <br><?php $interval = $package->getStartDataTime()->diff($package->getEndDataTime()); ?>
                    <small><strong>(<?= (int)$interval->format('%a') ?> Night stay)</strong></small>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="row mb-3">
        <?php if (!empty($item->theatre)) { ?>
            <div class="col-5">
                <i class="fa fa-map-marker"></i> <small>Location</small>
                <div class="ms-3"><small><?= $item->theatre->name ?></small></div>
            </div>
        <?php } ?>
        <?php if ($item::TYPE === TrShows::TYPE) { ?>
            <div class="col-7">
                <i class="fa fa-calendar"></i> <small>Date and Time</small>
                <div class="ms-3"><small><?= $package->getStartDataTime()->format('m/d/Y h:i A') ?></small>
                </div>
            </div>
        <?php } elseif (in_array($item::TYPE, [TrAttractions::TYPE], true)) { ?>
            <div class="col-7">
                <i class="fa fa-calendar"></i> <small>Avail dates</small>
                <div class="ms-3"><small>
                        <?php if ($package->isAnyTime) { ?>
                            <?= $package->getStartDataTime()->format(
                                'm/d/Y'
                            ) ?> <?= $package::ANY_TIME ?> - <?= $package->getEndDataTime()->format(
                                'm/d/Y'
                            ) ?> <?= $package::ANY_TIME ?>
                        <?php } else { ?>
                            <?= $package->getStartDataTime()->format('l, M d, h:i A') ?>
                        <?php } ?>
                    </small></div>
            </div>
        <?php } ?>
    </div>
<?php } ?>

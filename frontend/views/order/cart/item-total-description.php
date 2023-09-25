<?php

use common\models\Package;
use common\models\TrPosPlHotels;

/**
 * @var Package $package
 */

?>
<?php if ($package->getItem()::TYPE === TrPosPlHotels::TYPE) { ?>
    <?php /*if ($package->priceLine->strikeoutPrice) { ?>
        <div class="row row-small-padding">
            <div class="col-9 text-start"><small>
                <?php if ($package->priceLine->promoTerms) { ?>
                    <a href="#" data-toggle="modal"
                       data-target=".js-popup-cart-promo-terms"><?= $package->priceLine->promoTitle ?></a>:
                <?php } else { ?>
                    <?= $package->priceLine->promoTitle ?>
                <?php } ?></small>
            </div>
            <div class="col-3">
                <span class="cost text-nowrap">$ <?= number_format(
                        $package->priceLine->strikeoutPrice,
                        2,
                        '.',
                        ''
                    ) ?></span>
            </div>
        </div>
    <?php }*/ ?>
    <?php if ($package->priceLine !== null) { ?>
        <div class="row row-small-padding">
            <div class="col-9 text-start"><small>The avg. nightly rate:</small></div>
            <div class="col-3">
                <span class="cost text-nowrap">$ <?= number_format(
                        $package->priceLine->price,
                        2,
                        '.',
                        ''
                    ) ?></span>
            </div>
        </div>
    <?php } ?>
    <div class="row row-small-padding">
        <div class="col-9 text-start"><small>Subtotal:</small></div>
        <div class="col-3"><span class="cost text-nowrap">$ <?=
                number_format($package->getTotal(), 2, '.', '')
                ?></span></div>
    </div>
    <div class="row row-small-padding">
        <div class="col-9 text-start"><small>
                <?php if ($package->taxDescription) { ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target=".js-popup-cart-tax-description">Taxes</a>:
                <?php } else { ?>
                    Taxes:
                <?php } ?></small>
        </div>
        <div class="col-3">
            <span class="cost text-nowrap">$ <?=
                number_format($package->getTax(), 2, '.', '')
                ?></span>
        </div>
    </div>
    <div class="row row-small-padding">
        <div class="col-9 text-start"><small>Total:</small></div>
        <div class="col-3">
            <span class="cost text-nowrap">$ <?=
                number_format($package->getFullTotal(), 2, '.', '')
                ?></span>
        </div>
    </div>
    <?php if ($package->priceLine !== null) { ?>
        <div class="row row-small-padding">
            <div class="col-9 text-start"><small>Processing Fee:</small></div>
            <div class="col-3">
            <span class="cost text-nowrap">$ <?= number_format(
                    $package->priceLine->displayProcessingFee,
                    2,
                    '.',
                    ''
                ) ?></span>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>

    <div class="row row-small-padding">
        <div class="col-9 text-start"><small>Subtotal:</small></div>
        <div class="col-3"><span class="cost text-nowrap">$ <?=
                number_format($package->getTotal(), 2, '.', '')
                ?></span></div>
    </div>
    <div class="row row-small-padding">
        <div class="col-9 text-start"><small>Taxes and Convenience Fees:</small></div>
        <div class="col-3">
            <span class="cost text-nowrap">$ <?=
                number_format($package->getTax() + $package->getServiceFee(), 2, '.', '')
                ?></span>
        </div>
    </div>
<?php } ?>
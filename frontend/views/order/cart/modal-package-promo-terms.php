<?php

use common\helpers\Modal;
use common\models\Package;

/**
 * @var Package $package
 */

if (!empty($package->priceLine->promoTerms)) { ?>
    <?php Modal::begin(
        [
            'header' => '<h2>Terms</h2>',
            'size' => 'modal-dialog-centered modal-lg',
            'clientOptions' => ['show' => false],
            'options' => ['class' => 'js-popup-cart-promo-terms'],
        ]
    ); ?>
    <div class="scrollbar-inner">
        <p><?= $package->priceLine->promoTerms ?></p>
    </div>
    <?php Modal::end(); ?>
<?php } ?>
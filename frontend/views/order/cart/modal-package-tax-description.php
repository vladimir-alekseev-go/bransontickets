<?php

use common\helpers\Modal;
use common\models\Package;

/**
 * @var Package $package
 */

if (!empty($package->taxDescription)) { ?>
    <?php Modal::begin(
        [
            'header' => '<h2>Tax Description</h2>',
            'size' => 'modal-dialog-centered modal-lg',
            'clientOptions' => ['show' => false],
            'options' => ['class' => 'js-popup-cart-tax-description'],
        ]
    ); ?>
    <div class="scrollbar-inner">
        <?php foreach ($package->taxDescription as $description) { ?>
            <p><?= $description ?></p>
        <?php } ?>
    </div>
    <?php Modal::end(); ?>
<?php } ?>
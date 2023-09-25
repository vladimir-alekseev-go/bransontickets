<?php

use common\helpers\Modal;
use common\models\Package;

/**
 * @var Package $package
 */

if (!empty($package->policy)) { ?>
    <?php Modal::begin(
        [
            'header' => '<h2>Policy</h2>',
            'size' => 'modal-dialog-centered modal-lg',
            'clientOptions' => ['show' => false],
            'options' => ['class' => 'js-popup-cart-policy'],
        ]
    ); ?>
    <div class="scrollbar-inner">
        <?= $this->render('package-policy', compact('package')) ?>
    </div>
    <?php Modal::end(); ?>
<?php } ?>

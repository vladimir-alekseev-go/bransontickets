<?php

use common\models\Package;

/**
 * @var Package $package
 */

?>

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

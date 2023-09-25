<?php

use common\models\VacationPackageOrder;

/**
 * @var VacationPackageOrder $vacationPackage
 */

?>
<div class="row row-small-padding">
    <div class="col-9 text-start"><small>Package quantity:</small></div>
    <div class="col-3"><span class="cost text-nowrap"><?= $vacationPackage->count ?></span></div>
</div>
<?php if ($vacationPackage->getSave()) { ?>
    <div class="row row-small-padding">
        <div class="col-9 text-start"><small>Save:</small></div>
        <div class="col-3">
                <span class="cost text-nowrap">$ <?= number_format(
                        $vacationPackage->getSave(),
                        2,
                        '.',
                        ''
                    )
                    ?></span>
        </div>
    </div>
<?php } ?>
<div class="row row-small-padding">
    <div class="col-9 text-start"><small>Subtotal:</small></div>
    <div class="col-3">
        <span class="cost text-nowrap">$ <?= number_format($vacationPackage->getTotal(), 2, '.', '') ?>
        </span></div>
</div>
<div class="row row-small-padding">
    <div class="col-9 text-start"><small>Taxes and Convenience Fees:</small></div>
    <div class="col-3">
        <span class="cost text-nowrap">$ <?= number_format($vacationPackage->getTax(), 2, '.', '') ?>
        </span>
    </div>
</div>
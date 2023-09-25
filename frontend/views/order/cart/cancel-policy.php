<?php

use common\models\Package;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use common\models\VacationPackageOrder;

/**
 * @var Package[]|VacationPackageOrder[] $packages
 */

$this->title = 'Policy';

?>
<?php if (!empty($packages)) { ?>
    <ul class="with-out">
        <?php foreach ($packages as $package) { ?>
            <li>
                <h3><?= $package->name ?></h3>
                <?php if ($package instanceof Package && $package->getItem()::TYPE === TrPosHotels::TYPE
                    && !empty($package->getCancellationTexts())) { ?>
                    <?php foreach ($package->getCancellationTexts() as $cancelPolicyText) { ?>
                        <ul>
                            <li><?= $cancelPolicyText ?></li>
                        </ul>
                    <?php } ?>
                <?php } ?>
                <?php if ($package instanceof Package && $package->getItem()::TYPE === TrPosPlHotels::TYPE) { ?>
                    <ul>
                        <li><?= $this->render('package-policy', compact('package')) ?></li>
                    </ul>
                <?php } else { ?>
                    <?php if (is_array($package->getCancellationPolicyText())) { ?>
                        <?php foreach ($package->getCancellationPolicyText() as $text) { ?>
                            <ul>
                                <li><?= $text ?></li>
                            </ul>
                        <?php } ?>
                    <?php } else { ?>
                        <ul>
                            <li><?= $package->getCancellationPolicyText() ?></li>
                        </ul>
                    <?php } ?>
                    <?php if (!empty($package->cancelPolicies)) { ?>
                        <?php foreach ($package->cancelPolicies as $cancelPolicy) { ?>
                            <ul>
                                <li><?= $cancelPolicy['names'] ?><br/><?= strip_tags($cancelPolicy['text']) ?></li>
                            </ul>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                <br/>
            </li>
        <?php } ?>
    </ul>
<?php } ?>

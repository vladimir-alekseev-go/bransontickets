<?php

use common\models\Package;

/**
 * @var Package $package
 */

if ($package->policy) { ?>
    <?php foreach ($package->policy as $policy) { ?>
        <h4 class="mb-0"><?= $policy['title'] ?></h4>
        <p><?php foreach ($policy['paragraph_data'] as $paragraph_data) { ?>
                <?= strip_tags($paragraph_data) ?>
            <?php } ?></p>
    <?php } ?>
<?php } ?>

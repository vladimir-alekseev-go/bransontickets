<?php

/**
 * @var string $name
 * @var float  $cost
 */

if ($cost > 0) { ?>
    <div class="text-end mb-2">
        <small><?= $name ?>:</small>
        <span class="cost">$ <?= number_format($cost, 2, '.', '') ?></span>
    </div>
<?php } ?>
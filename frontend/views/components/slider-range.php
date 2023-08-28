<?php
/**
 * @var array $rangePrice
 */

?>
<div id="slider-range" data-value-from="<?= floor($rangePrice["value_from"]) ?>"
     data-value-to="<?= ceil($rangePrice["value_to"]) ?>" data-min="<?= $rangePrice["min"] ?>"
     data-max="<?= ceil($rangePrice["max"] / 30) * 30 ?>"></div>
<div id="slider-price-range-grid" class="slider-range-grid">
    <div class="slider-mark-left">$ 0</div>
    <div>$ <?= ceil($rangePrice["max"] / 30) * 10 ?></div>
    <div>$ <?= ceil($rangePrice["max"] / 30) * 20 ?></div>
    <div class="slider-mark-right">$ <?= ceil($rangePrice["max"] / 30) * 30 ?></div>
</div>

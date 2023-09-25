<?php
/**
 * @var array $result
 * @var array $cards
 */
?>
<div class="row mb-3 mt-3">
    <div class="col-sm-5">
        <h3>TOTAL AMOUNT TO REFUND</h3>
    </div>
    <div class="col-sm-7 text-start text-sm-end">
        <div class="cost fs-4 mb-3">$ <?= number_format(abs($result['modifyAmount']), 2, '.', '')?></div>
        <div class="refund">
            Refund to: <b class="fs-4"><?php if ($cards) { foreach($cards as $card) { echo $card."<br/>"; }}?></b>
        </div>
    </div>
</div>

<p class="note">Please note:<br>You should receive your refund within 3-15 working days. If there is a delay, please check with your issuing bank or contact us for help.</p>

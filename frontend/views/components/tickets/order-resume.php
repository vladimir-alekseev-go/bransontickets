<?php

use common\models\OrderForm;
use common\models\TrLunchs;

/**
 * @var OrderForm $OrderForm
 */

?>
<div class="resume-order">
    <div class="total">
        <div class="row">
            <div class="col-6 col-sm-12 text-start text-sm-end">
                <div class="summ">
                    <span id="total-count">0</span> <?= $OrderForm->model instanceof TrLunchs ? 'Vouchers' : 'Tickets' ?> Total
                </div>
            </div>
            <div class="col-6 col-sm-12 text-end">
                <span class="cost" id="total-price">$ 0.00</span>
            </div>
        </div>
    </div>
</div>
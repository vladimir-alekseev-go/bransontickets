<?php

use common\models\OrderModifyForm;
use common\models\TrLunchs;
use common\models\TrPrices;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;

/**
 * @var OrderModifyForm $OrderForm
 */

?>

<?php if ($OrderForm->model->call_us_to_book) {?>
    <?php echo Alert::widget([
        'options' => ['class' => 'alert-warning'],
        'closeButton' => false,
        'body' => $OrderForm->messageCallUsToBookModification,
    ]);?>
<?php } else if (!$OrderForm->isCutOff()) {?>

<?php if ($OrderForm->model::TYPE === TrLunchs::TYPE) {?>
<p class="range-available">Valid dates are:	<?= $OrderForm->date->format("m/d/y D")?></p>
<?php }?>

<p>
	<b>Tickets on <?= $OrderForm->date->format("l, M d, h:i A")?></b><br/>
</p>

<?php if ($OrderForm->family_pass_n_pack) {?>
<div class="alert alert-success">
	<b>Pertaining to Discounted Promotions</b>
You may select only <b>ONE</b> promotion per order.
Your options are: <b>ONE</b> Family 4 Pack or <b>ONE</b> Family 8 Pack
If more than one promotion is selected this will result in individual Adult and/or Child pricing being applied.
</div>
<?php }?>


<div class="add-order">

<?php $form = ActiveForm::begin(['id' => 'list-tickets', 'method'=>'post']); ?>

	<?= $form->errorSummary($OrderForm,['header'=>'<div class="alert alert-danger alert-dismissible">', 'footer'=>'</div>']) ?>

    <?= $form->field($OrderForm, 'hashData', ['template'=>'{input}'])->hiddenInput() ?>
	<?= $form->field($OrderForm, 'date_format', ['template'=>'{input}'])->hiddenInput()?>
	<?= $form->field($OrderForm, 'package_date_format', ['template'=>'{input}'])->hiddenInput()?>
	<?= $form->field($OrderForm, 'coupon_code', ['template'=>'{input}'])->hiddenInput() ?>

	<div itemprop="offers" itemscope="" itemtype="http://schema.org/AggregateOffer">

        <div class="order-container" role="table" aria-label="Destinations">
            <div class="flex-table row order-container-header" role="rowgroup">
                <div class="flex-row first" role="columnheader">
                    <div class="row">
                        <div class="col-12 col-lg-7">
                            <span class="d-none d-md-inline-block ps-3">Ticket type</span>
                        </div>
                        <div class="col-12 col-lg-5">
                            <span class="d-none d-lg-inline-block ps-3">Valid date</span>
                        </div>
                    </div>
                </div>
                <div class="flex-row second" role="columnheader">Ticket Price</div>
                <div class="flex-row third" role="columnheader">Quantity</div>
                <div class="flex-row forth" role="columnheader">Subtotal</div>
            </div>
            <?= $this->render('@frontend/views/components/tickets/order-fields', compact('OrderForm', 'form')) ?>
        </div>

    	<?php if ($OrderForm->recount) {?>
    	<div class="alert alert-success hide alert-recount" id="alert-recount-fp-4">
    		Your choice of <b><span class="adult-old"></span> ADULT</b> + <b><span class="child-old"></span> CHILD</b> ticket types was automatically recalculated to <b><span class="fp-new-4"></span> FAMILY PASS 4 PACK</b> and <b><span class="adult-new"></span> ADULT</b> and <b><span class="child-new"></span> CHILD</b>
    	</div>
    	<div class="alert alert-success hide alert-recount" id="alert-recount-fp-8">
    		Your choice of <b><span class="adult-old"></span> ADULT</b> + <b><span class="child-old"></span> CHILD</b> <span class="fp-4-block"> + <b>1 FAMILY PASS 4 PACK</b></span> ticket types was automatically recalculated to <b><span class="fp-new-8"></span> FAMILY PASS 8 PACK</b> and <b><span class="adult-new"></span> ADULT</b> and <b><span class="child-new"></span> CHILD</b>
    	</div>
    	<div class="alert alert-success hide alert-recount" id="alert-recount">
    		Yay! We have found a combination of tickets which makes your deal even better and total price of all tickets lower.
    	</div>
    	<?php }?>
	</div>
    <?php ActiveForm::end(); ?>
</div>

<?php } else {?>
	<?php echo Alert::widget([
        'options' => ['class' => 'alert-warning'],
        'closeButton' => false,
        'body' => $OrderForm->messageCutOff,
    ]);?>
    <?php $this->registerJs('order.lockPanel();');?>
<?php }?>

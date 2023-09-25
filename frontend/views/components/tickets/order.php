<?php

use common\models\OrderForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;

/**
 * @var OrderForm $OrderForm
 */

$ignore_call_us_to_book = $ignore_call_us_to_book ?? false;
?>

<?php if (count($OrderForm->prices) > 0) { ?>

    <?php if ($OrderForm->date->format('H:i') === '00:00') { ?>
        <h4>
            <strong>Tickets on <?= $OrderForm->date->format("l, M d") ?>, Any Time,
                <?= $OrderForm->prices[0]->allotment->name ?? '' ?>
            </strong>
        </h4>
    <?php } else { ?>
        <h4>
            <strong>
                Tickets on <?= $OrderForm->date->format("l, M d, h:i A") ?>,
                <?= $OrderForm->prices[0]->allotment->name ?? '' ?>
            </strong>
        </h4>
    <?php } ?>

    <?php if ($OrderForm->family_pass_n_pack) { ?>
        <?php Alert::begin(
            [
                'options' => ['class' => 'alert-success show'],
                'closeButton' => false,
            ]
        ); ?>
        <b>Pertaining to Discounted Promotions</b>
        You may select only <b>ONE</b> promotion per order.
        Your options are: <b>ONE</b> Family 4 Pack or <b>ONE</b> Family 8 Pack
        If more than one promotion is selected this will result in individual Adult and/or Child pricing being applied.
        <?php Alert::end(); ?>
    <?php } ?>
<?php } ?>

<div class="add-order">

<?php if (!$ignore_call_us_to_book && $OrderForm->model->call_us_to_book) {?>
    <?php echo Alert::widget(
        [
            'options' => ['class' => 'alert-warning show'],
            'closeButton' => false,
            'body' => $OrderForm->messageCallUsToBook,
        ]
    ); ?>
<?php } else if ($OrderForm->prices) {?>

<?php $form = ActiveForm::begin(['id' => 'list-tickets', 'method'=>'post','enableClientValidation' => true]); ?>

	<div class="fields-block" itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer">
    	<?php $prices = [];?>

    	<?php if (!$OrderForm->isCutOff()) {?>

            <input type="hidden" name="available_total" value="100"/>
            <div class="order-container" role="table" aria-label="Destinations">
                <div class="flex-table row order-container-header" role="rowgroup">
                    <div class="flex-row first" role="columnheader">
                        <div class="row">
                            <div class="col-12 col-lg-7">
                                <span class="d-none d-md-inline-block ps-3 ticket-type-title">
                                    Ticket type
                                </span>
                            </div>
                            <div class="col-12 col-lg-5">
                                <span class="d-none d-lg-inline-block ps-3 valid-date-title">Valid date</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-row second" role="columnheader">
                        Ticket Price
                    </div>
                    <div class="flex-row third" role="columnheader">Quantity</div>
                    <div class="flex-row forth" role="columnheader">Subtotal</div>
                </div>
                <?= $this->render('order-fields', compact('OrderForm', 'form')) ?>
            </div>

            <meta itemprop="priceCurrency" content="USD">
            <link itemprop="url" href="<?= Url::to([$this->context->id.'/tickets','code'=>$OrderForm->model->code, 'date'=>$OrderForm->date->format('Y-m-d_H:i:s')], true)?>">

            <div class="stick-order-bottom-alert">
            <?php echo Alert::widget(
                [
                    'id' => 'only-one-ticket-in-one-time',
                    'options' => ['class' => 'alert-warning hide show stick-order-bottom-alert shadow-block alert-warning-border'],
                    'closeButton' => false,
                    'body' => "Sorry, you can't buy more than one voucher in one order. 
                    Please, finish this order and create the new one to buy more vouchers.",
                ]
            ); ?>

    	<?php if ($OrderForm->recount) {?>
        	<?php Alert::begin([
                'id' => 'alert-recount-fp-4',
                'options' => ['class' => 'alert-success hide alert-recount show stick-order-bottom-alert shadow-block alert-success-border'],
                'closeButton' => false,
            ]);?>
            Your choice of <b><span class="adult-old"></span> ADULT</b> + <b><span class="child-old"></span> CHILD</b> ticket types was automatically recalculated to <b><span class="fp-new-4"></span> FAMILY PASS 4 PACK</b> and <b><span class="adult-new"></span> ADULT</b> and <b><span class="child-new"></span> CHILD</b>
            <?php Alert::end();?>

            <?php Alert::begin([
                'id' => 'alert-recount-fp-8',
                'options' => ['class' => 'alert-success hide alert-recount show stick-order-bottom-alert shadow-block alert-success-border'],
                'closeButton' => false,
            ]);?>
            Your choice of <b><span class="adult-old"></span> ADULT</b> + <b><span class="child-old"></span> CHILD</b> <span class="fp-4-block"> + <b>1 FAMILY PASS 4 PACK</b></span> ticket types was automatically recalculated to <b><span class="fp-new-8"></span> FAMILY PASS 8 PACK</b> and <b><span class="adult-new"></span> ADULT</b> and <b><span class="child-new"></span> CHILD</b>
            <?php Alert::end();?>

            <?php Alert::begin([
                'id' => 'alert-recount',
                'options' => ['class' => 'alert-success hide alert-recount show shadow-block alert-success-border'],
                'closeButton' => false,
            ]);?>
            Yay! We have found a combination of tickets which makes your deal even better and total price of all tickets lower.
            <?php Alert::end();?>

    	<?php }?>
            </div>
            <div class="rows order-form-footer">
                <?= $this->render('@app/views/components/tickets/order-footer', compact('OrderForm', 'form')) ?>
    	    </div>
		<?php } else {?>
            <?php echo Alert::widget([
                'options' => ['class' => 'alert-warning show'],
                'closeButton' => false,
                'body' => $OrderForm->messageCutOff,
            ]);?>
        <?php }?>
	</div>
 <?php ActiveForm::end(); ?>

<?php }?>

</div>
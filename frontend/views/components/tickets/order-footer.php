<?php

use common\models\OrderForm;
use yii\bootstrap\ActiveForm;

/**
 * @var ActiveForm $form
 * @var OrderForm  $OrderForm
 */

?>

<?= $form->field($OrderForm, 'comments')->textArea(['rows' => '5'])->label(null) ?>

<?= $form->errorSummary(
    $OrderForm,
    ['header' => '<div class="alert alert-danger alert-dismissible">', 'footer' => '</div>']
) ?>

<div class="row">
    <div class="col-sm-5 col-lg-5 mb-3 special-requests-are-not-guaranteed">
        <div>Special requests are not guaranteed</div>
    </div>
    <div class="col-sm-4 col-lg-5 text-end">
        <?= $this->render('order-resume', compact('OrderForm')) ?>
    </div>
    <div class="col-sm-3 col-lg-2 text-end">
        <div class="text-right" itemprop="potentialAction" itemscope itemtype="https://schema.org/BuyAction">
            <button class="btn buy-btn btn-loading-need w-100" itemprop="target" content="<?=
            Yii::$app->request->url ?>">Add to cart
            </button>
        </div>
    </div>
</div>
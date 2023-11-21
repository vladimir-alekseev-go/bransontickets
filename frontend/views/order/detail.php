<?php

use common\helpers\Modal;
use common\models\Package;
use common\models\TrOrders;
use yii\helpers\Url;
use yii\web\JqueryAsset;

/**
 * @var Package[] $itemsByCategory
 * @var TrOrders  $Order
 */

$this->title = "Order detail";

$errors = Yii::$app->session->getFlash('errors');
$warnings = Yii::$app->session->getFlash('warnings');
$messages = Yii::$app->session->getFlash('messages');
$success = Yii::$app->session->getFlash('success');

foreach ($Order->getPackages() as $package) {
    $itemsByCategory[$package->category][] = $package;
}

?>
<div class="fixed">
    <?php if (!Yii::$app->user->isGuest) { ?>
        <a href="<?= Url::to(['profile/index']) ?>" class="back-page d-inline-block mb-2 text-white">
            <strong><span class="icon ib-arrow-left"></span>Back to profile / Orders list</strong>
        </a>
    <?php } ?>
    <h1 class="h2"><strong><?= $Order->order_number ?> - Order detail</strong></h1>

    <div class="row">
        <div class="col-lg-9">

            <?php if (!empty($errors)) { ?>
                <div class="alert alert-danger"><?= $errors[0] ?></div>
            <?php } ?>
            <?php if (!empty($warnings)) { ?>
                <div class="alert alert-warning"><?= $warnings[0] ?></div>
            <?php } ?>
            <?php if (!empty($messages)) { ?>
                <div class="alert alert-success"><?= $messages[0] ?></div>
            <?php } ?>
            <?php if (!empty($success)) { ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php } ?>

            <div class="margin-block-small">
                <?php if ($Order->getUniqueVacationPackages()) { ?>
                    <div class="order-detail-list">
                        <?php foreach ($Order->getUniqueVacationPackages() as $uniqueHash => $vacationPackage) { ?>
                            <?= $this->render(
                                'cart/vp-package-item',
                                compact('vacationPackage', 'uniqueHash', 'Order')
                            ) ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (!empty($itemsByCategory)) { ?>
                    <div class="order-detail-list">
                        <?php foreach ($itemsByCategory as $category => $packages) { ?>
                            <?php foreach ($packages as $package) { ?>
                                <?= $this->render('cart/modal-package-tax-description', compact('package')) ?>
                                <?= $this->render(
                                    'cart/item',
                                    [
                                        'package'       => $package,
                                        'isOrderDetail' => true,
                                    ]
                                ) ?>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="col-lg-3">
            <?= $this->render('detail/order-summary', ['order' => $Order]) ?>
        </div>
    </div>
</div>

<?php Modal::begin(
    [
        'header'        => false,
        'id'            => 'popup-cancel',
        'size'          => 'modal-dialog-centered modal-lg modal-cancel',
        'clientOptions' => ['show' => false]
    ]
);
?>
<div class="scrollbar-inner">
    <div id="js-data-container-cancel" class="container-cancel"></div>
</div>
<?php Modal::end(); ?>

<?php Modal::begin(
    [
        'header'        => false,
        'id'            => 'popup-modification',
        'size'          => 'modal-dialog-centered modal-lg modal-modification',
        'clientOptions' => ['show' => false]
    ]
);
?>

<div id="js-data-container-modification" class="container-cancel"></div>

<?php Modal::end(); ?>

<?php $this->registerJsFile('/js/jquery.payform.js', ['depends' => [JqueryAsset::class]]); ?>
<?php $this->registerJsFile('/js/payment.js', ['depends' => [JqueryAsset::class]]); ?>
<?php $this->registerJsFile('/js/order.js', ['depends' => [JqueryAsset::class]]); ?>
<?php $this->registerJs('order.init()'); ?>
<?php $this->registerJs('order.initModify()'); ?>

<?php //$this->registerJs(
//    '$(".js-popup-cancellation-policy").click(function(){
//    $("#modalContentCancellationPolicy").html(\'<div class="load-progress"></div><br/><br/>\');
//    $("#cancellationPolicy").modal("show").find("#modalContentCancellationPolicy").load($(this).data("url"));
//    return false;
//})'
//); ?>

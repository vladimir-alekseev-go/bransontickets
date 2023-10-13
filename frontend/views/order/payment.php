<?php

use common\models\PaymentForm;
use common\models\PaymentFormAddCard;
use common\models\User;
use yii\bootstrap\BootstrapPluginAsset;
use yii\web\JqueryAsset;

BootstrapPluginAsset::register($this);

/**
 * @var array              $cards
 * @var PaymentForm        $model
 * @var User               $user
 * @var PaymentFormAddCard $modelAddCard
 */

$this->title = "Checkout";

$errors = Yii::$app->session->getFlash('errors');
$success = Yii::$app->session->getFlash('success');
$warnings = Yii::$app->session->getFlash('warnings');
$messages = Yii::$app->session->getFlash('messages');
$post = Yii::$app->request->post();
?>
<div class="fixed">
    <div class="row">
        <div class="col-md-8 order-2 order-md-1">
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
            <?php } else { ?>
                <div class="white-block shadow-block margin-block-small">
                    <?php if ($cards) { ?>
                        <div class="order-card">
                            <div class="menu-content menu-content-control mb-4">

                                <ul class="nav" id="myTab" role="tablist">
                                    <li role="presentation">
                                        <a href="#"
                                           class="<?php if (empty($post["PaymentFormAddCard"])) { ?>active<?php } ?>"
                                           id="usecard-tab" data-bs-toggle="tab" data-bs-target="#usecard"
                                           type="button" role="tab" aria-controls="usecard"
                                           aria-selected="true">Use existing card</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#" id="addcard-tab" data-bs-toggle="tab" data-bs-target="#addcard"
                                           type="button" role="tab" aria-controls="addcard"
                                           aria-selected="false">Add new card</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade show <?php if (empty($post["PaymentFormAddCard"])) { ?>active<?php } ?>"
                                     id="usecard" role="tabpanel" aria-labelledby="usecard-tab">
                                    <?= $this->render('payment/use-card', compact('model', 'cards')) ?>
                                </div>
                                <div class="tab-pane fade" id="addcard" role="tabpanel" aria-labelledby="addcard-tab">
                                    <?= $this->render('payment/add-card', compact('modelAddCard', 'user')) ?>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <?= $this->render('payment/add-card', compact('modelAddCard', 'user')) ?>
                    <?php } ?>
                </div>
                <?php $this->registerJsFile('/js/jquery.payform.js', ['depends' => [JqueryAsset::class]]); ?>
                <?php $this->registerJsFile('/js/payment.js', ['depends' => [JqueryAsset::class]]); ?>
                <?php $this->registerJs("payment.init()"); ?>
                <?php if ($modelAddCard->same_as_billing) {
                    $this->registerJs("payment.billingHide()");
                } ?>
            <?php } ?>
        </div>
        <div class="col-md-4 order-1 order-md-2">
            <?= $this->render('payment/short-payment-cart') ?>
        </div>
    </div>
</div>


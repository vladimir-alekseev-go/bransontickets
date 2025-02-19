<?php

use common\helpers\Modal;
use common\models\CartForm;
use common\models\form\CartCouponForm;
use common\models\TrAttractions;
use common\models\TrBasket;
use common\models\TrPosHotels;
use common\models\TrShows;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var TrBasket       $Basket
 * @var CartCouponForm $CartCouponForm
 * @var CartForm       $CartForm
 * @var View           $this
 */

$this->title = 'Shopping cart';
$errors = Yii::$app->session->getFlash('errors');
$warnings = Yii::$app->session->getFlash('warnings');
$messages = Yii::$app->session->getFlash('messages');
$remove = Yii::$app->session->getFlash('remove');
?>
<div class="fixed">
    <div class="row">
        <div class="col-12 col-md-8 col-xl-9">
            <?php if (!empty($Basket->getPackages()) || !empty($Basket->getVacationPackages())) { ?>
                <a href="<?= Url::to(['order/cart', 'remove_all' => 1]) ?>" class="remove-all-items float-end mt-md-3"
                   onclick="return confirm('Do you want to remove all items?')">
                    <strong><i class="fa fa-trash"></i></span> Clear cart</strong>
                </a>
            <?php } ?>
            <h1 class="h2"><strong><?= $this->title ?></strong></h1>
        </div>
    </div>

    <?php if (!empty($Basket->getPackages()) || !empty($Basket->getVacationPackages())) { ?>
        <div class="row">
            <div class="col-12 col-md-8 col-xl-9">
                <?php if (!empty($errors)) { ?>
                    <?= Alert::widget(
                        [
                            'options'     => ['class' => 'alert-danger show'],
                            'closeButton' => false,
                            'body'        => $errors[0],
                        ]
                    ) ?>
                <?php } ?>
                <?php if (!empty($warnings)) { ?>
                    <?= Alert::widget(
                        [
                            'options'     => ['class' => 'alert-warning show'],
                            'closeButton' => false,
                            'body'        => $warnings[0],
                        ]
                    ) ?>
                <?php } ?>
                <?php if (!empty($messages)) { ?>
                    <?= Alert::widget(
                        [
                            'options'     => ['class' => 'alert-success show'],
                            'closeButton' => false,
                            'body'        => $messages[0],
                        ]
                    ) ?>
                <?php } ?>
                <?php foreach ($Basket->getUniqueVacationPackages() as $uniqueHash => $vacationPackage) { ?>
                    <?= $this->render('cart/vp-package-item', compact('vacationPackage', 'uniqueHash')) ?>
                <?php } ?>
                <?php foreach ($Basket->getPackages() as $package) {
                    $item = $package->getItem();
                    ?>
                    <?= $this->render('cart/modal-package-policy', compact('package')) ?>
                    <?= $this->render('cart/modal-package-tax-description', compact('package')) ?>
                    <?= $this->render('cart/modal-package-promo-terms', compact('package')) ?>
                    <?= $this->render('cart/item', compact('package')) ?>
                <?php } ?>
            </div>
            <div class="col-12 col-md-4 col-xl-3">
                <?php if ($Basket->showCouponForm()) { ?>
                    <?= $this->render('@app/views/order/cart/coupon', compact('CartCouponForm', 'Basket')) ?>
                <?php } ?>
                <div class="js-real-position real-position">
                    <div class="js-static-position static-position">
                        <?= $this->render('cart/resume', compact('CartForm', 'Basket')) ?>
                        <?php $itemsMenu = [
                            [
                                'label' => TrShows::NAME_PLURAL,
                                'url'   => ['/shows/index'],
                            ],
                            [
                                'label' => TrAttractions::NAME_PLURAL,
                                'url'   => ['/attractions/index'],
                            ],
                            [
                                'label' => TrPosHotels::NAME_PLURAL,
                                'url'   => ['lodging/index'],
                            ],
//                            [
//                                'label' => 'Packages',
//                                'url'   => ['packages/index'],
//                            ],
                        ]; ?>
                        <button type="button" class="shadow-block btn btn-secondary w-100 mb-3"
                                onclick="$('.dropdown-menu').toggle('slow')">
                            I want to add more <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu ms-3 i-want-to-add-more" aria-labelledby="dropdown-menu">
                            <?php foreach ($itemsMenu as $itemMenu) { ?>
                                <?php if (isset($itemMenu['visible']) && $itemMenu['visible'] === false) {
                                    continue;
                                } ?>
                                <li class="px-3"><a href="<?= Url::to($itemMenu['url']) ?>"><?= $itemMenu['label']
                                        ?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php } elseif ($remove) { ?>
        <div class="white-block shadow-block text-center mb-5">
            <div class="cart-cleared">You cleared cart. You could choose other items:</div>
            <div class="cart-item-section">
                <a href="<?= Url::to(['shows/index']) ?>">Shows</a>
                <a href="<?= Url::to(['attractions/index']) ?>">Attractions</a>
                <a href="<?= Url::to(['lodging/index']) ?>">Lodging</a>
                <a href="<?= Url::to(['packages/index']) ?>">Packages</a>
            </div>
        </div>
    <?php } else { ?>
        <div class="white-block shadow-block text-center mb-5">
            <div class="cart-empty">Cart is empty!</div>
        </div>
    <?php } ?>
</div>
<?php Modal::begin(
    [
        'header'        => '<h2 id="modalHeaderTitle" class="modalHeaderTitle"></h2>',
        'headerOptions' => ['id' => 'modalHeader'],
        'id'            => 'modalVacationPackage',
        'size'          => 'modal-dialog-centered modal-lg',
        'clientOptions' => ['show' => false, 'keyboard' => false]
    ]
);
?>
<div class="scrollbar-inner">
    <div id="modalContent"></div>
</div>
<?php Modal::end(); ?>

<?php Modal::begin(
    [
        'header'        => false,
        'id'            => 'cancellationPolicy',
        'size'          => 'modal-dialog-centered modal-lg',
        'clientOptions' => ['show' => false, 'keyboard' => false]
    ]
);
?>
<div class="scrollbar-inner">
    <div id="modalContentCancellationPolicy"></div>
</div>
<?php Modal::end(); ?>

<?php //$this->registerJsFile('/js/cart.js', ['depends' => [JqueryAsset::class]], 'cart-js'); ?>
<?php //$this->registerJs('cart.init()'); ?>
<?php $this->registerJs(
    '$(".js-popup-cancellation-policy").click(function(){
    $("#modalContentCancellationPolicy").html(\'<div class="load-progress"></div><br/><br/>\');
    $("#cancellationPolicy").modal("show").find("#modalContentCancellationPolicy").load($(this).data("url"));
    return false;
})'
); ?>

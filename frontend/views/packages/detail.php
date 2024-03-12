<?php

use common\helpers\General;
use common\helpers\Modal;
use common\models\form\PackageForm;
use common\models\VacationPackage;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JqueryAsset;

/**
 * @var VacationPackage $VacationPackage
 * @var PackageForm     $PackageForm
 */

Yii::$app->view->params['model'] = $VacationPackage;

$items = $VacationPackage->getItems();
$DateValidStart = $VacationPackage->getValidStart() > new DateTime ? $VacationPackage->getValidStart() : new DateTime;
?>
<div class="fixed">
    <div class="mb-1">
        <div class="back">
            <a href="<?= Url::to(['packages/index']) ?>">
                <strong><i class="fa fa-arrow-left"></i> Back to Vacation Packages list</strong>
            </a>
        </div>
    </div>

    <?php if (!empty($items)) { ?>
    <?php $form = ActiveForm::begin(
        ['id' => 'packages-form', 'method' => 'post', 'action' => ['packages/add-to-cart']]
    ); ?>
    <?= $form->field($PackageForm, 'package_id')->hiddenInput()->label(false) ?>
    <?= $form->field($PackageForm, 'package_modify_id')->hiddenInput()->label(false) ?>
    <?= $form->field($PackageForm, 'selectedData')->hiddenInput()->label(false) ?>
    <div class="vacation-packages-buy" id="vacationPackagesBuy"
         data-conditions='<?= Json::encode($VacationPackage->getData()['itemCount']) ?>'
         data-prices='<?= Json::encode($VacationPackage->vacationPackagePrices) ?>'>
        <div class="row margin-block-small">
            <div class="col-12 col-md-8 col-lg-9">
                <h1 class="h2"><strong><?= $VacationPackage->name ?></strong></h1>
                <div class="vp-tickets js-vp-tickets">
                    <div class="mb-3"><?= $VacationPackage->description ?></div>
                    <div class="please-select-items alert alert-info"><i class="fa fa-info-circle"></i>
                        Please select items you wish to visit.
                        <?php if (!empty($VacationPackage->getConditionsAsText())) { ?>
                            Need to choose <?= $VacationPackage->getConditionsAsText() ?>.
                        <?php } ?>
                    </div>
                    <?php foreach ($items as $vpItem) { ?>
                        <?= $this->render(
                            'tickets',
                            [
                                'VacationPackage' => $VacationPackage,
                                'vpItem'          => $vpItem,
                                'form'            => $form,
                                'PackageForm'     => $PackageForm
                            ]
                        ) ?>
                    <?php } ?>
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3 js-column-to-scroll-block">
                <div class="package-detail-info">
                    <div class="save-up-to white-block shadow-block text-center mb-3">
                        <span class="fs-4">Save up to: </span>
                        <span class="cost fs-4">$ <?= $VacationPackage->getSaveUpTo() ?></span>
                    </div>
                    <div class="mb-3">
                        <div><i class="fa fa-calendar"></i> <small>Available Dates:</small></div>
                        <div>
                            <?= $DateValidStart->format('M d, Y') ?> - <?= $VacationPackage->getValidEnd()->format(
                                'M d, Y'
                            ) ?>
                        </div>
                    </div>
                    <?php if (!empty($VacationPackage->getTypes())) { ?>
                        <div class="mb-3">
                            <div><i class="fa fa-list-alt"></i> <small>Category:</small></div>
                            <?php foreach ($VacationPackage->getTypes() as $type) { ?>
                                <span class="tag">
                                    <?= Html::a($type, ['packages/index', 's[c][]' => $type]) ?>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="ms-3"><small>For More Package Info</small></div>
                                <i class="fa fa-phone"></i>
                                <strong>
                                    417-337-8455
                                </strong>
                            </div>
                            <div class="col-4">
                                <div class="text-end"><small>From</small></div>
                                <div class="text-end cost">
                                    $ <?= min(
                                        ArrayHelper::getColumn($VacationPackage->vacationPackagePrices, 'price')
                                    ) ?>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="js-real-position">
                        <div class="js-static-position static-position">

                            <div class="vp-small-order-info white-block shadow-block mb-3">
                                <div class="js-selected-info"></div>
                                <div><small>Package options:</small></div>
                                <?php foreach ($VacationPackage->vacationPackagePrices as $price) { ?>
                                    <div class="row js-price-item" data-count="<?= $price->count ?>">
                                        <div class="col-8">
                                            <small>Select <?= $price->count ?> <?= $VacationPackage->getCountMax(
                                                ) ? 'of ' . $VacationPackage->getCountMax() : '' ?> items</small></div>
                                        <div class="col-4 text-end cost">$ <?= $price->price ?></div>
                                    </div>
                                <?php } ?>
                                <div class="line"></div>
                                <div class="text-center description description-error red" id="error-description"></div>

                                <div class="row">
                                    <div class="col-6">Quantity of Packages</div>
                                    <div class="col-6 with-input-field">
                                        <i class="js-input-factor fa fa-minus in-active" data-factor="-1"></i>
                                        <i class="js-input-factor fa fa-plus" data-factor="1"></i>
                                        <?= $form->field($PackageForm, 'count')->textInput(
                                            [
                                                'type'     => 'number',
                                                'max'      => '9',
                                                'data-max' => '9',
                                                'min'      => '1',
                                                'data-min' => '1',
                                                'class'    => 'text-center'
                                            ]
                                        )
                                            ->label(false) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5">
                                        <div class="total">
                                            <div class="summ">Total :</div>
                                            <span class="cost zero" id="total-price">$ 0.00</span>
                                        </div>
                                    </div>
                                    <div class="col-7">
                                        <button class="btn btn-primary w-100 ps-0 pe-0" id="btn-buy-package">
                                            Add to cart
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

        <?php
        $this->registerJsFile('/js/sly.min.js', ['depends' => [JqueryAsset::class]]);
        $this->registerJsFile('/js/vacation-packages.js', ['depends' => [JqueryAsset::class]]);
        $this->registerJsFile('/js/vacation-packages-event.js', ['depends' => [JqueryAsset::class]]);
        ?>
        <?php $this->registerJs("VacationPackageBuy.init('" . Url::to(['packages/selected-info']) . "');"); ?>
        <?php if ($VacationPackageModify = $PackageForm->getVacationPackageModify()) {
            foreach ($VacationPackageModify->getPackages() as $package) {
                $this->registerJs(
                    'VacationPackageBuy.clickByElement("' . $package->category . '", "' . $package->id . '", "'
                    . $package->getStartDataTime()->format('Y-m-d H:i:s') . '")'
                );
            }
        } ?>
        <?php } ?>

        <div class="row margin-block-small">
            <div class="col-12 col-md-8 col-lg-9 js-column-to-scroll-block-additional">
                <?= $this->render('overview', compact('VacationPackage')) ?>
            </div>
        </div>
    </div>
</div>

<?php Modal::begin(
    [
        'header'        => '',
        'id'            => 'selecting-package-rules',
        'size'          => 'modal-dialog-centered modal-lg selecting-package-rules',
        'clientOptions' => ['show' => true, 'keyboard' => false],
    ]
);
?>
<?php if (!empty($VacationPackage->getConditionsAsText())) { ?>
    Need to choose <?= $VacationPackage->getConditionsAsText() ?>.
<?php } ?>
    Please select items you wish to visit.
<?php Modal::end(); ?>

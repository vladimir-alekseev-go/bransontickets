<?php

use common\helpers\General;
use common\models\form\PackageForm;
use common\models\TrAttractions;
use common\models\TrAttractionsPrices;
use common\models\TrPrices;
use common\models\VacationPackage;
use common\models\VacationPackageAttraction;
use common\models\VacationPackageShow;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * @var VacationPackageShow|VacationPackageAttraction $vpItem
 * @var TrPrices[]|TrAttractionsPrices[]              $data
 * @var PackageForm                                   $PackageForm
 * @var VacationPackage                               $VacationPackage
 * @var ActiveForm                                    $form
 */

$item = $vpItem->itemExternal;
$data = $VacationPackage->getItemPrices($vpItem)->all();

$countTypesByDate = [];
foreach ($data as $d) {
    $k1 = (new DateTime($d->start))->format('Y-m-d');
    $k2 = (new DateTime($d->start))->format('H:i:s');
    $countTypesByDate[$k1][$k2][$d->price_external_id] = $d->name;
}

$data = ArrayHelper::map($data, static function($el){
    return (new DateTime($el->start))->format('H:i:s').'-'.($el->any_time ?? '');
}, static function($el){
    return $el->id_external;
}, static function($el){
    return (new DateTime($el->start))->format('Y-m-d');
});

$endRangeDate = clone $VacationPackage->getValidEnd();
$endRangeDate->add(new DateInterval('P1D'));
$range = new DatePeriod(
    $VacationPackage->getValidStart() > new DateTime ? $VacationPackage->getValidStart() : new DateTime,
    new DateInterval('P1D'),
    $endRangeDate
);

$arTypes = [];
foreach ($VacationPackage->getData()['ticketTypes'] as $ticketType) {
    if ((int)$ticketType['vendorId'] === (int)$item->id_external) {
        $arTypes[] = array_merge($ticketType, [
            'count' => !empty($VacationPackage->getData()['ticketTypeQty'][$ticketType['id']]) ? $VacationPackage->getData()['ticketTypeQty'][$ticketType['id']] : 0,
        ]);
    }
}

?>
<div class="calendar-slider-block white-block shadow-block mb-4" id="ticket-<?= $item->id?>">
	<div class="head item-<?= $item::TYPE ?>">

        <div class="row">
            <div class="col-sm-6 col-lg-8">
                <div class="item-img"><a href="<?= $item->getUrl() ?>">
                        <?php if (!empty($item->preview_id)) { ?>
                            <img class="preview" width="260" src="<?= $item->preview->url ?>" alt="<?= $item->name ?>"
                                 itemprop="image"/>
                        <?php } else { ?>
                            <div class="img img-empty">
                                <img class="preview" width="260" src="/img/bransontickets-noimage.png" alt=""/>
                            </div>
                        <?php } ?>
                    </a></div>
                <div class="item-name"><?= $item->name ?>
                    <?php if (isset($vpItem->itemType)) { ?>
                        <span class="type-name"><?= $vpItem->itemType->name ?></span>
                    <?php } ?>
                </div>
                <span class="included-tickets-description">
                <?php foreach ($arTypes as $key => $arType) { ?>
                    <span class="ticket-count"><?= $key < 1 ? '' : ', ' ?> <?= $arType['count'] ?></span>
                    <span class="ticket-type"><?= $arType['name'] ?></span>
                <?php } ?>
                </span>
                <span class="included-tickets">Tickets included</span>
            </div>
            <div class="col-sm-6 col-lg-4 mt-3 mt-sm-0">
                <div class="row">
                    <div class="col-6 text-left text-sm-end item-selected-date js-selected-date"></div>
                    <div class="col-6 icon-checkbox js-open-close">
                        <button class="btn-vp-cancel w-100 ps-0 pe-0">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach ($arTypes as $type) {?>
            <?php if (in_array(
                $type['name'],
                [
                    PackageForm::PRICE_TYPE_FAMILY_PASS,
                    PackageForm::PRICE_TYPE_FAMILY_PASS_4_PACK,
                    PackageForm::PRICE_TYPE_FAMILY_PASS_8_PACK
                ],
                false
            )) { ?>
        <?php if ($PackageForm->getFpFieldName($item, $type['id'], $type['name'])) {?>
        <div class="family-pass js-family-pass">
            <?= $form->field($PackageForm, $PackageForm->getFpFieldName($item, $type['id'], $type['name']))->textInput(['type' => 'number'])->label(false) ?>
            <div class="description">Please specify the amount of places you need<br/><strong><?= $type['name']?></strong> fits for your request</div>
        </div>
        <?php }?>
        <?php }?>
        <?php }?>
	</div>
	<div class="week-wrap calendar-slider calendar-slider-in-package">
		<div class="frame horizontal" id="basic">
		<ul>
		<?php foreach ($range as $k => $date) {?>
			<li class="it has-ticket <?=
            General::getDatePeriod()->getStartDate()->format('Y-m-d') === $date->format('Y-m-d') ? 'act' : ''?>">
        		<div class="date"><?= $date->format('M d')?></div>
        		<div class="w"><?= $date->format('D')?></div>
        		<?php if (!empty($data[$date->format('Y-m-d')])) {?>
        			<?php $has = false;?>
    				<?php foreach ($data[$date->format('Y-m-d')] as $time => $typeId) {?>
    					<?php $time = explode('-', $time);?>
    					<?php $anyTime = $time[1];?>
    					<?php $time = $time[0];?>
        				<?php $dateTime = new DateTime($date->format('Y-m-d').' '.$time);?>
        				<?php $_count = !empty($countTypesByDate[$dateTime->format('Y-m-d')][$dateTime->format('H:i:s')]) ? count($countTypesByDate[$dateTime->format('Y-m-d')][$dateTime->format('H:i:s')]) : 0;?>
        				<?php if ($_count === count($arTypes)) {?>
        				<?php $has = true;?>
        				<a class="js-package-item-time btn btn-third btn-sm w-100 px-0"
                           href="<?= $item->getUrl($item->code, $dateTime->format('Y-m-d H:i:s'))?>"
                           data-item-type="<?= $item->type?>"
                           data-item-type-real="<?= $item->type?>" data-item-id="<?= $item->id_external?>"
                           data-date="<?= $dateTime->format('Y-m-d H:i:s')?>"
                           data-date-formatting="<?= $dateTime->format('m/d/y h:iA')?>"><?= (int)$anyTime === 1 ? 'Any Time' : $dateTime->format('h:iA')?></a>
        				<?php }?>
                	<?php }?>
                	<?php if (!$has) {?>
                        <div class="btn btn-link btn-sm w-100 cursor-default mb-1">N/A</div>
    				<?php }?>
				<?php } else {?>
                    <div class="btn btn-link btn-sm w-100 cursor-default mb-1">N/A</div>
				<?php }?>
    		</li>
		<?php }?>
    	</ul>
</div>	</div>
</div>

<?php

use common\models\TrAttractions;
use common\models\TrPosHotels;
use common\models\TrShows;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$packages = !empty($this->context->VPLWidget) ? $this->context->VPLWidget->run() : '';
$packagesCount = !empty($this->context->VPLWidget) ? $this->context->VPLWidget->getTotalCountQuery() : 0;

/**
 * @var TrShows[]       $shows
 * @var TrAttractions[] $attractions
 */

$shows = $this->context->shows;
$attractions = $this->context->attractions;
?>

<div class="search-form">
	<?php $form = ActiveForm::begin(['action'=>['site/search'], 'method'=>'get']); ?>
	<div class="box-search row mb-3">
        <div class="<?= Yii::$app->request->url === Url::to('/') ? 'col-12 col-md-7'  : 'col-sm-9 col-md-10' ?>">
		    <?= $form->field($this->context->searchForm, 'q')->input('q', ['placeholder' => "I'm looking for"])->label(false) ?>
        </div>
        <div class="<?= Yii::$app->request->url === Url::to('/') ? 'col-12 col-md-5'  : 'col-sm-3 col-md-2' ?>">
		<button class="btn btn-secondary w-100">Search</button>
    </div>
	</div>
	<?php ActiveForm::end(); ?>
</div>

<?php if (!empty($this->context->query)) {?>
<div class="search-data">
    <div class="search-menu-content">
        <ul>
            <li class="mb-3">
                <a <?= count($this->context->shows) === 0 ? ' class="empty"' : "" ?> href="#shows">
                    Shows - <?= count($this->context->shows) ?>
                </a>
            </li>
            <li class="mb-3">
                <a <?= count($this->context->attractions) === 0 ? ' class="empty"' : "" ?> href="#attractions">
                    Attractions - <?= count($this->context->attractions) ?>
                </a>
            </li>
            <li class="mb-3">
                <a <?= count($this->context->hotels) === 0 ? ' class="empty"' : "" ?> href="#lodging">
                    Lodging - <?= count($this->context->hotels) ?>
                </a>
            </li>
            <li class="mb-3">
                <a <?= $packagesCount === 0 ? ' class="empty"' : "" ?> href="#packages">
                    Packages - <?= $packagesCount ?>
                </a>
            </li>
        </ul>
    </div>
	<div>
		<div class="white-block shadow-block mb-4 overflow-hidden ms-n15 me-n15" id="shows">
            <div class="items-header item-<?= TrShows::TYPE ?>"><?= TrShows::NAME_PLURAL ?></div>
			<?php if ($shows) {?>
		    	<?php foreach ($shows as $item) {?>
					<?= $this->render('search-item', compact('item'))?>
				<?php }?>
			<?php } else {?>
				<div class="items-not-found">
                    <div>
                        <p><strong>No items found.</strong></p>
                        <p>Sorry, we can't find items on your request, try to change filter criteria.</p>
                    </div>
				</div>
			<?php }?>
		</div>
		<div class="white-block shadow-block mb-4 overflow-hidden ms-n15 me-n15" id="attractions">
            <div class="items-header item-<?= TrAttractions::TYPE ?>"><?= TrAttractions::NAME_PLURAL ?></div>
    		<?php if ($attractions) {?>
    		    <?php foreach ($attractions as $item) {?>
    				<?= $this->render('search-item', compact('item'))?>
    			<?php }?>
    		<?php } else {?>
                <div class="items-not-found">
                    <div>
                        <p><strong>No items found.</strong></p>
                        <p>Sorry, we can't find items on your request, try to change filter criteria.</p>
                    </div>
                </div>
    		<?php }?>
		</div>
		<div class="white-block shadow-block mb-4 overflow-hidden ms-n15 me-n15" id="lodging">
            <div class="items-header item-<?= TrPosHotels::TYPE?>"><?= TrPosHotels::NAME_PLURAL ?></div>
			<?php if ($this->context->hotels) {?>
    		    <?php foreach ($this->context->hotels as $item) {?>
    				<?= $this->render('search-item', compact('item'))?>
    			<?php }?>
    		<?php } else {?>
                <div class="items-not-found">
                    <div>
                        <p><strong>No items found.</strong></p>
                        <p>Sorry, we can't find items on your request, try to change filter criteria.</p>
                    </div>
                </div>
    		<?php }?>
		</div>
		<div class="white-block shadow-block mb-4 overflow-hidden ms-n15 me-n15" id="packages">
            <div class="items-header item-packages">Vacation Packages</div>
			<?php if ($packagesCount) {?>
    		    <?= $packages?>
    		<?php } else {?>
                <div class="items-not-found">
                    <div>
                        <p><strong>No items found.</strong></p>
                        <p>Sorry, we can't find items on your request, try to change filter criteria.</p>
                    </div>
                </div>
    		<?php }?>
		</div>
	</div>
</div>
<?php }?>

<?php

use common\models\TrShows;
use yii\helpers\Url;

/**
 * @var TrShows $model
 */

?>
<?php //= $this->render('info-over-conteiner')?>
<div class="calendar-slider-block">
    <div class="week-wrap calendar-slider calendar-slider-in-order">
        <?= $this->render('frame', compact('model', 'prices', 'range')) ?>
    </div>
    <div class="text-end">
        <a href="#" id="more-available">
            More Available Dates <span class="icon br-t-arrow-middle-right"></span>
        </a>
    </div>
</div>

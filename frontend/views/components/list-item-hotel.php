<?php

/**
 * @var TrPosHotels $model
 */

use common\models\TrPosHotels;

?>
<?php if (!empty($model->amenities)) { ?>
    <ul class="amenities">
        <?php foreach (explode(";", $model->amenities) as $amenity) { ?>
            <li><?= $amenity ?></li>
        <?php } ?>
    </ul>
<?php } ?>

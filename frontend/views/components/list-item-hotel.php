<?php

/**
 * @var TrPosHotels|TrPosPlHotels $model
 */

use common\models\TrPosHotels;
use common\models\TrPosPlHotels;

?>
<?php if (!empty($model->amenities)) { ?>
    <ul class="amenities">
        <?php foreach (explode(";", $model->amenities) as $amenity) { ?>
            <li><?= $amenity ?></li>
        <?php } ?>
    </ul>
<?php } ?>

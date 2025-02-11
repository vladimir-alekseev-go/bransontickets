<?php

use common\models\TrPosHotels;
use common\models\TrPosPlHotels;

/**
 * @var TrPosPlHotels[]|TrPosHotels[] $items
 */
$pagination = null;
$priceAll = [];

echo $this->render(
    '@app/views/shows/items',
    compact(
        'items',
        'pagination',
        'priceAll',
        'Search',
    )
);

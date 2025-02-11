<?php

use common\models\TrPosHotels;

/**
 * @var TrPosHotels[] $items
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

<?php

$locations = [];
$RestaurantCuisine = [];
$items = [];
$priceAll = [];
$itemCount = 0;

echo $this->render(
    '@app/views/shows/index',
    compact(
        'items',
        'pagination',
        'categories',
        'locations',
        'rangePrice',
        'priceAll',
        'Search',
        'itemCount',
        'RestaurantCuisine',
        'textAfterList'
    )
);
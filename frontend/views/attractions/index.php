<?php

echo $this->render(
    '@app/views/shows/index',
    compact(
        'items',
        'pagination',
        'categories',
        'rangePrice',
        'priceAll',
        'Search',
        'itemCount',
    )
);

<?php

namespace frontend\widgets\search;

class SearchWidget extends \common\widgets\search\SearchWidget
{
    public function run()
    {
        return $this->render('search');
    }
}

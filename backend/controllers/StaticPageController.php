<?php

namespace backend\controllers;

use backend\models\search\StaticPageSearch;
use common\models\StaticPage;

class StaticPageController extends CrudController
{
    public $modelClass = StaticPage::class;
    public $modelSearchClass = StaticPageSearch::class;
}

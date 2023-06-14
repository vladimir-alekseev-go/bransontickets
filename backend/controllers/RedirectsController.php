<?php

namespace backend\controllers;

use backend\models\search\RedirectsSearch;
use common\models\redirects\Redirects;

class RedirectsController extends CrudController
{
    public $modelClass = Redirects::class;
    public $modelSearchClass = RedirectsSearch::class;
}

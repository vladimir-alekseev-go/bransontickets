<?php

namespace backend\controllers;

use backend\models\search\FeedbackSubjectSearch;
use common\models\FeedbackSubject;

class FeedbackSubjectController extends CrudController
{
    public $modelClass = FeedbackSubject::class;
    public $modelSearchClass = FeedbackSubjectSearch::class;
}

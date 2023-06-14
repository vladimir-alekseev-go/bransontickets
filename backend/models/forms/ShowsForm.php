<?php

namespace backend\models\forms;

use common\models\TrShows;
use common\models\TrShowsSimilar;

class ShowsForm extends TrShows
{
    use SimilarFormTrait;

    public function afterFind()
    {
        parent::afterFind();
        $this->similarObject = new TrShowsSimilar();
        $this->similarIds = $this->getTrSimilar()->select(['similar_external_id'])->column();
    }
}
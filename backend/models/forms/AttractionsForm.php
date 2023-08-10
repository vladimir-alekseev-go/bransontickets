<?php

namespace backend\models\forms;

use common\models\TrAttractions;
use common\models\TrAttractionsSimilar;

class AttractionsForm extends TrAttractions
{
    use SimilarFormTrait;

    public function afterFind()
    {
        parent::afterFind();
        $this->similarObject = new TrAttractionsSimilar();
        $this->similarIds = $this->getTrSimilar()->select(['similar_external_id'])->column();
    }
}
<?php

namespace backend\models\forms;

use common\models\TrShowsSimilar;
use Exception;

/**
 * Trait SimilarFormTrait
 *
 * @package backend\models\forms
 */
trait SimilarFormTrait
{
    public $similarIds = [];

    /**
     * @var TrShowsSimilar $similarObject
     */
    protected $similarObject;

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                ['similarIds', 'each', 'rule' => ['string']],
            ]
        );
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $this->saveSimilar();
        return parent::save($runValidation, $attributeNames);
    }

    private function saveSimilar()
    {
        $old = $this->getTrSimilar()->select(['external_id'])->column();
        $similarIds = self::find()
            ->select(['id_external'])
            ->where(['id_external' => $this->similarIds])
            ->column();

        if (array_diff($old, $similarIds) || array_diff($similarIds, $old)) {
            $this->similarObject::deleteAll(['external_id' => $this->id_external]);

            $rows = [];
            foreach ($similarIds as $id) {
                $rows[] = [$this->id_external, $id];
            }
            try {
                self::getDb()->createCommand()->batchInsert(
                    $this->similarObject::tableName(),
                    ['external_id', 'similar_external_id',],
                    $rows
                )->execute();
            } catch (Exception $e) {
                $this->addError('similarIds', $e->getMessage());
            }
        }
    }
}
<?php

namespace common\models;

use Yii;

trait ItemsPhotoJoinTrait
{
    public $deletePhotoIds;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['deletePhotoIds'], 'each', 'rule' => ['integer']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        if (!empty($this->photo)) {
            $this->photo->delete();
        }
        if (!empty($this->preview)) {
            $this->preview->delete();
        }
        return true;
    }
}

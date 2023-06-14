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
//
//    /**
//     * @return \yii\db\ActiveQuery
//     * DEPRECADET, use mothod getPhoto()
//     */
//    public function getContentFilesPhoto()
//    {
//        return parent::getPhoto();
//    }
//
//    /**
//     * @return \yii\db\ActiveQuery
//     * DEPRECADET, use mothod getPreview()
//     */
//    public function getContentFilesPreview()
//    {
//        return parent::getPreview();
//    }
//
//    public function setPhotos($itemId, $attributes)
//    {
//        if (empty($itemId)) {
//            return false;
//        }
//
//        $output = false;
//
//        foreach ($attributes as $attr) {
//            if ($attr['photo_id'] || $attr['preview_id']) {
//                $photoJoin = new static;
//
//                $photoJoin->setAttributes([
//                    'item_id' => $itemId,
//                    'activity' => 1,
//                    'photo_id' => $attr['photo_id'],
//                    'preview_id' => $attr['preview_id'],
//                ]);
//
//                if ($photoJoin->save()) {
//                    if (!$output) {
//                        $output = true;
//                    }
//                }
//            }
//        }
//
//        if ($this->deletePhotoIds) {
//            static::deleteAll(['item_id' => $itemId, 'photo_id' => $this->deletePhotoIds]);
//        }
//
//        return $output;
//    }

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

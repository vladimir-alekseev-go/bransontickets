<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shows_photo_join".
 *
 * @property int $id
 * @property int $photo_id
 * @property int $item_id
 * @property int|null $preview_id
 * @property int $activity
 * @property int $sort
 *
 * @property TrShows $item
 * @property ContentFiles $photo
 * @property ContentFiles $preview
 */
class _source_ShowsPhotoJoin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_photo_join';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['photo_id', 'item_id'], 'required'],
            [['photo_id', 'item_id', 'preview_id', 'activity', 'sort'], 'integer'],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrShows::class, 'targetAttribute' => ['item_id' => 'id']],
            [['photo_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentFiles::class, 'targetAttribute' => ['photo_id' => 'id']],
            [['preview_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentFiles::class, 'targetAttribute' => ['preview_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'photo_id' => 'Photo ID',
            'item_id' => 'Item ID',
            'preview_id' => 'Preview ID',
            'activity' => 'Activity',
            'sort' => 'Sort',
        ];
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(TrShows::class, ['id' => 'item_id']);
    }

    /**
     * Gets query for [[Photo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasOne(ContentFiles::class, ['id' => 'photo_id']);
    }

    /**
     * Gets query for [[Preview]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPreview()
    {
        return $this->hasOne(ContentFiles::class, ['id' => 'preview_id']);
    }
}

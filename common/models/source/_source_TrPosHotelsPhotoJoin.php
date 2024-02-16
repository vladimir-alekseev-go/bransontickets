<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_pos_hotels_photo_join".
 *
 * @property int $id
 * @property int|null $preview_id
 * @property int|null $photo_id
 * @property int $item_id
 * @property int $activity
 * @property int $sort
 * @property string|null $subcategory
 * @property int|null $room_type_external_id
 * @property string $hash
 * @property string|null $tags
 *
 * @property TrPosHotels $item
 * @property ContentFiles $photo
 * @property ContentFiles $preview
 * @property TrPosRoomTypes $roomTypeExternal
 */
class _source_TrPosHotelsPhotoJoin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_pos_hotels_photo_join';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['preview_id', 'photo_id', 'item_id', 'activity', 'sort', 'room_type_external_id'], 'integer'],
            [['item_id', 'hash'], 'required'],
            [['subcategory'], 'string', 'max' => 16],
            [['hash'], 'string', 'max' => 32],
            [['tags'], 'string', 'max' => 128],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrPosHotels::class, 'targetAttribute' => ['item_id' => 'id']],
            [['photo_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentFiles::class, 'targetAttribute' => ['photo_id' => 'id']],
            [['preview_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentFiles::class, 'targetAttribute' => ['preview_id' => 'id']],
            [['room_type_external_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrPosRoomTypes::class, 'targetAttribute' => ['room_type_external_id' => 'id_external']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'preview_id' => 'Preview ID',
            'photo_id' => 'Photo ID',
            'item_id' => 'Item ID',
            'activity' => 'Activity',
            'sort' => 'Sort',
            'subcategory' => 'Subcategory',
            'room_type_external_id' => 'Room Type External ID',
            'hash' => 'Hash',
            'tags' => 'Tags',
        ];
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(TrPosHotels::class, ['id' => 'item_id']);
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

    /**
     * Gets query for [[RoomTypeExternal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoomTypeExternal()
    {
        return $this->hasOne(TrPosRoomTypes::class, ['id_external' => 'room_type_external_id']);
    }
}

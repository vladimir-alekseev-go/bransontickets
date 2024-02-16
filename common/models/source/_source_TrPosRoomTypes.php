<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_pos_room_types".
 *
 * @property int $id
 * @property int|null $id_external
 * @property int|null $id_external_item
 * @property string $name
 * @property string $hash_summ
 * @property string|null $tags
 *
 * @property TrPosHotels $externalItem
 * @property TrPosHotelsPhotoJoin[] $trPosHotelsPhotoJoins
 * @property TrPosHotelsPriceExtra[] $trPosHotelsPriceExtras
 * @property TrPosHotelsPriceRoom[] $trPosHotelsPriceRooms
 */
class _source_TrPosRoomTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_pos_room_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'id_external_item'], 'integer'],
            [['name', 'hash_summ'], 'required'],
            [['name'], 'string', 'max' => 64],
            [['hash_summ'], 'string', 'max' => 32],
            [['tags'], 'string', 'max' => 128],
            [['id_external_item'], 'exist', 'skipOnError' => true, 'targetClass' => TrPosHotels::class, 'targetAttribute' => ['id_external_item' => 'id_external']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_external' => 'Id External',
            'id_external_item' => 'Id External Item',
            'name' => 'Name',
            'hash_summ' => 'Hash Summ',
            'tags' => 'Tags',
        ];
    }

    /**
     * Gets query for [[ExternalItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternalItem()
    {
        return $this->hasOne(TrPosHotels::class, ['id_external' => 'id_external_item']);
    }

    /**
     * Gets query for [[TrPosHotelsPhotoJoins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrPosHotelsPhotoJoins()
    {
        return $this->hasMany(TrPosHotelsPhotoJoin::class, ['room_type_external_id' => 'id_external']);
    }

    /**
     * Gets query for [[TrPosHotelsPriceExtras]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrPosHotelsPriceExtras()
    {
        return $this->hasMany(TrPosHotelsPriceExtra::class, ['id_external' => 'id_external']);
    }

    /**
     * Gets query for [[TrPosHotelsPriceRooms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrPosHotelsPriceRooms()
    {
        return $this->hasMany(TrPosHotelsPriceRoom::class, ['id_external' => 'id_external']);
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_theaters".
 *
 * @property int $id
 * @property int $id_external
 * @property string|null $name
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip_code
 * @property string|null $directions
 * @property int $status
 * @property string|null $image
 * @property string|null $contacts_phone
 * @property string|null $contacts_email
 * @property string|null $contacts_fax
 * @property string|null $additional_phone
 * @property string $hash_summ
 * @property string $updated_at
 * @property string|null $location_lat
 * @property string|null $location_lng
 * @property string|null $location_updated_at
 *
 * @property TrAttractions[] $trAttractions
 * @property TrShows[] $trShows
 */
class _source_TrTheaters extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_theaters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'hash_summ'], 'required'],
            [['id_external', 'status'], 'integer'],
            [['updated_at', 'location_updated_at'], 'safe'],
            [['name', 'city'], 'string', 'max' => 64],
            [['address1', 'address2'], 'string', 'max' => 128],
            [['state'], 'string', 'max' => 4],
            [['zip_code'], 'string', 'max' => 8],
            [['directions'], 'string', 'max' => 1024],
            [['image', 'contacts_email'], 'string', 'max' => 256],
            [['contacts_phone', 'contacts_fax', 'additional_phone', 'location_lat', 'location_lng'], 'string', 'max' => 16],
            [['hash_summ'], 'string', 'max' => 32],
            [['id_external'], 'unique'],
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
            'name' => 'Name',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'city' => 'City',
            'state' => 'State',
            'zip_code' => 'Zip Code',
            'directions' => 'Directions',
            'status' => 'Status',
            'image' => 'Image',
            'contacts_phone' => 'Contacts Phone',
            'contacts_email' => 'Contacts Email',
            'contacts_fax' => 'Contacts Fax',
            'additional_phone' => 'Additional Phone',
            'hash_summ' => 'Hash Summ',
            'updated_at' => 'Updated At',
            'location_lat' => 'Location Lat',
            'location_lng' => 'Location Lng',
            'location_updated_at' => 'Location Updated At',
        ];
    }

    /**
     * Gets query for [[TrAttractions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrAttractions()
    {
        return $this->hasMany(TrAttractions::class, ['theatre_id' => 'id_external']);
    }

    /**
     * Gets query for [[TrShows]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrShows()
    {
        return $this->hasMany(TrShows::class, ['theatre_id' => 'id_external']);
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_hotels".
 *
 * @property int $id
 * @property int $id_external
 * @property int $status
 * @property int $show_in_footer
 * @property string $name
 * @property string|null $description
 * @property string|null $amenities
 * @property string|null $property_amenities
 * @property int|null $preview_id
 * @property string $hash_summ
 * @property string|null $hash_summ_fast_update
 * @property string $code
 * @property string|null $photos
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $phone
 * @property string|null $zip_code
 * @property string|null $fax
 * @property string|null $email
 * @property float|null $hotel_rating
 * @property string $updated_at
 * @property string|null $location_lat
 * @property string|null $location_lng
 * @property string|null $amenities_description
 * @property string|null $property_information
 * @property string|null $area_information
 * @property string|null $property_description
 * @property string|null $hotel_policy
 * @property string|null $deposit_credit_cards_accepted
 * @property string|null $room_information
 * @property string|null $driving_directions
 * @property string|null $check_in_instructions
 * @property string|null $location_description
 * @property string|null $room_detail_description
 * @property int|null $sort
 * @property string|null $cancel_policy_text
 * @property string|null $voucher_procedure
 * @property int $rating
 * @property string|null $external_service
 *
 * @property ContentFiles $preview
 */
class _source_TrHotels extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_hotels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'status', 'name', 'hash_summ', 'code'], 'required'],
            [['id_external', 'status', 'show_in_footer', 'preview_id', 'sort', 'rating'], 'integer'],
            [['description', 'photos', 'amenities_description', 'property_information', 'area_information', 'property_description', 'hotel_policy', 'deposit_credit_cards_accepted', 'room_information', 'driving_directions', 'check_in_instructions', 'location_description', 'room_detail_description'], 'string'],
            [['hotel_rating'], 'number'],
            [['updated_at'], 'safe'],
            [['name', 'code', 'address', 'email'], 'string', 'max' => 128],
            [['amenities', 'property_amenities', 'cancel_policy_text', 'voucher_procedure'], 'string', 'max' => 2048],
            [['hash_summ', 'hash_summ_fast_update', 'city', 'phone'], 'string', 'max' => 64],
            [['state'], 'string', 'max' => 8],
            [['zip_code', 'fax', 'location_lat', 'location_lng', 'external_service'], 'string', 'max' => 16],
            [['id_external'], 'unique'],
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
            'id_external' => 'Id External',
            'status' => 'Status',
            'show_in_footer' => 'Show In Footer',
            'name' => 'Name',
            'description' => 'Description',
            'amenities' => 'Amenities',
            'property_amenities' => 'Property Amenities',
            'preview_id' => 'Preview ID',
            'hash_summ' => 'Hash Summ',
            'hash_summ_fast_update' => 'Hash Summ Fast Update',
            'code' => 'Code',
            'photos' => 'Photos',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'phone' => 'Phone',
            'zip_code' => 'Zip Code',
            'fax' => 'Fax',
            'email' => 'Email',
            'hotel_rating' => 'Hotel Rating',
            'updated_at' => 'Updated At',
            'location_lat' => 'Location Lat',
            'location_lng' => 'Location Lng',
            'amenities_description' => 'Amenities Description',
            'property_information' => 'Property Information',
            'area_information' => 'Area Information',
            'property_description' => 'Property Description',
            'hotel_policy' => 'Hotel Policy',
            'deposit_credit_cards_accepted' => 'Deposit Credit Cards Accepted',
            'room_information' => 'Room Information',
            'driving_directions' => 'Driving Directions',
            'check_in_instructions' => 'Check In Instructions',
            'location_description' => 'Location Description',
            'room_detail_description' => 'Room Detail Description',
            'sort' => 'Sort',
            'cancel_policy_text' => 'Cancel Policy Text',
            'voucher_procedure' => 'Voucher Procedure',
            'rating' => 'Rating',
            'external_service' => 'External Service',
        ];
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

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_pos_pl_hotels".
 *
 * @property int $id
 * @property int $id_external
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip_code
 * @property string|null $phone
 * @property string|null $fax
 * @property string|null $email
 * @property string|null $directions
 * @property int $status
 * @property int $show_in_footer
 * @property int|null $location_external_id
 * @property int|null $rank
 * @property int|null $marketing_level
 * @property int|null $weekly_schedule
 * @property string|null $voucher_procedure
 * @property string|null $on_special_text
 * @property string|null $tags
 * @property string|null $photos
 * @property string|null $videos
 * @property string|null $amenities
 * @property string|null $cancel_policy_text
 * @property string|null $location_lat
 * @property string|null $location_lng
 * @property string|null $external_service
 * @property int|null $call_us_to_book
 * @property int|null $preview_id
 * @property int|null $image_id
 * @property int $display_image
 * @property int|null $theatre_id
 * @property float|null $min_rate
 * @property float|null $min_rate_source
 * @property string $hash_summ
 * @property string|null $hash_image_content
 * @property float|null $rating
 * @property float|null $review_rating
 * @property string|null $review_rating_desc
 * @property int|null $update_preview
 * @property string|null $check_in
 * @property string|null $check_out
 * @property string|null $updated_at
 *
 * @property ContentFiles $image
 * @property ContentFiles $preview
 * @property TrPosPlHotelsCategories[] $trPosPlHotelsCategories
 * @property TrPosPlHotelsPhotoJoin[] $trPosPlHotelsPhotoJoins
 */
class _source_TrPosPlHotels extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_pos_pl_hotels';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'code', 'name', 'hash_summ'], 'required'],
            [['id_external', 'status', 'show_in_footer', 'location_external_id', 'rank', 'marketing_level', 'weekly_schedule', 'call_us_to_book', 'preview_id', 'image_id', 'display_image', 'theatre_id', 'update_preview'], 'integer'],
            [['description', 'directions'], 'string'],
            [['min_rate', 'min_rate_source', 'rating', 'review_rating'], 'number'],
            [['updated_at'], 'safe'],
            [['code', 'name', 'address', 'email'], 'string', 'max' => 128],
            [['city'], 'string', 'max' => 164],
            [['state', 'zip_code', 'check_in', 'check_out'], 'string', 'max' => 8],
            [['phone', 'fax'], 'string', 'max' => 64],
            [['voucher_procedure', 'on_special_text'], 'string', 'max' => 1024],
            [['tags'], 'string', 'max' => 256],
            [['photos', 'videos', 'amenities', 'cancel_policy_text'], 'string', 'max' => 2048],
            [['location_lat', 'location_lng', 'external_service', 'review_rating_desc'], 'string', 'max' => 16],
            [['hash_summ', 'hash_image_content'], 'string', 'max' => 32],
            [['id_external'], 'unique'],
            [['code'], 'unique'],
            [['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentFiles::class, 'targetAttribute' => ['image_id' => 'id']],
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
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'zip_code' => 'Zip Code',
            'phone' => 'Phone',
            'fax' => 'Fax',
            'email' => 'Email',
            'directions' => 'Directions',
            'status' => 'Status',
            'show_in_footer' => 'Show In Footer',
            'location_external_id' => 'Location External ID',
            'rank' => 'Rank',
            'marketing_level' => 'Marketing Level',
            'weekly_schedule' => 'Weekly Schedule',
            'voucher_procedure' => 'Voucher Procedure',
            'on_special_text' => 'On Special Text',
            'tags' => 'Tags',
            'photos' => 'Photos',
            'videos' => 'Videos',
            'amenities' => 'Amenities',
            'cancel_policy_text' => 'Cancel Policy Text',
            'location_lat' => 'Location Lat',
            'location_lng' => 'Location Lng',
            'external_service' => 'External Service',
            'call_us_to_book' => 'Call Us To Book',
            'preview_id' => 'Preview ID',
            'image_id' => 'Image ID',
            'display_image' => 'Display Image',
            'theatre_id' => 'Theatre ID',
            'min_rate' => 'Min Rate',
            'min_rate_source' => 'Min Rate Source',
            'hash_summ' => 'Hash Summ',
            'hash_image_content' => 'Hash Image Content',
            'rating' => 'Rating',
            'review_rating' => 'Review Rating',
            'review_rating_desc' => 'Review Rating Desc',
            'update_preview' => 'Update Preview',
            'check_in' => 'Check In',
            'check_out' => 'Check Out',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Image]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasOne(ContentFiles::class, ['id' => 'image_id']);
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
     * Gets query for [[TrPosPlHotelsCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrPosPlHotelsCategories()
    {
        return $this->hasMany(TrPosPlHotelsCategories::class, ['id_external_show' => 'id_external']);
    }

    /**
     * Gets query for [[TrPosPlHotelsPhotoJoins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrPosPlHotelsPhotoJoins()
    {
        return $this->hasMany(TrPosPlHotelsPhotoJoin::class, ['item_id' => 'id']);
    }
}

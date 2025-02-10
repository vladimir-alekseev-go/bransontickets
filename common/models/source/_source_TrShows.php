<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_shows".
 *
 * @property int $id
 * @property int $id_external
 * @property string $external_id
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
 * @property int|null $rank_level
 * @property int|null $marketing_level
 * @property string|null $voucher_procedure
 * @property int|null $weekly_schedule
 * @property string|null $on_special_text
 * @property string|null $cast_size
 * @property int|null $seats
 * @property int|null $show_length
 * @property string|null $intermissions
 * @property int|null $cut_off
 * @property float|null $tax_rate
 * @property string|null $hash_summ
 * @property string|null $photos
 * @property int|null $preview_id
 * @property int|null $image_id
 * @property int $display_image
 * @property int|null $theatre_id
 * @property string|null $theatre_name
 * @property string|null $amenities
 * @property string|null $tags
 * @property string|null $videos
 * @property float|null $min_rate
 * @property float|null $min_rate_source
 * @property string|null $cancel_policy_text
 * @property string $updated_at
 * @property string|null $location_lat
 * @property string|null $location_lng
 * @property int|null $call_us_to_book
 * @property string|null $external_service
 * @property string $change_status_date
 *
 * @property ContentFiles $image
 * @property ContentFiles $preview
 * @property ShowsPhotoJoin[] $showsPhotoJoins
 * @property TrPrices[] $trPrices
 * @property TrShowsCategories[] $trShowsCategories
 * @property TrShowsSimilar[] $trShowsSimilars
 * @property TrShowsSimilar[] $trShowsSimilars0
 * @property VacationPackageShow[] $vacationPackageShows
 */
class _source_TrShows extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_shows';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'external_id', 'code', 'name'], 'required'],
            [['id_external', 'status', 'show_in_footer', 'location_external_id', 'rank_level', 'marketing_level', 'weekly_schedule', 'seats', 'show_length', 'cut_off', 'preview_id', 'image_id', 'display_image', 'theatre_id', 'call_us_to_book'], 'integer'],
            [['description', 'directions'], 'string'],
            [['tax_rate', 'min_rate', 'min_rate_source'], 'number'],
            [['updated_at', 'change_status_date'], 'safe'],
            [['external_id'], 'string', 'max' => 10],
            [['code', 'name', 'address', 'email', 'theatre_name'], 'string', 'max' => 128],
            [['city', 'phone', 'fax', 'intermissions'], 'string', 'max' => 64],
            [['state', 'zip_code'], 'string', 'max' => 8],
            [['voucher_procedure', 'amenities', 'videos', 'cancel_policy_text'], 'string', 'max' => 2048],
            [['on_special_text'], 'string', 'max' => 1024],
            [['cast_size', 'location_lat', 'location_lng', 'external_service'], 'string', 'max' => 16],
            [['hash_summ'], 'string', 'max' => 32],
            [['photos'], 'string', 'max' => 4096],
            [['tags'], 'string', 'max' => 256],
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
            'external_id' => 'External ID',
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
            'rank_level' => 'Rank Level',
            'marketing_level' => 'Marketing Level',
            'voucher_procedure' => 'Voucher Procedure',
            'weekly_schedule' => 'Weekly Schedule',
            'on_special_text' => 'On Special Text',
            'cast_size' => 'Cast Size',
            'seats' => 'Seats',
            'show_length' => 'Show Length',
            'intermissions' => 'Intermissions',
            'cut_off' => 'Cut Off',
            'tax_rate' => 'Tax Rate',
            'hash_summ' => 'Hash Summ',
            'photos' => 'Photos',
            'preview_id' => 'Preview ID',
            'image_id' => 'Image ID',
            'display_image' => 'Display Image',
            'theatre_id' => 'Theatre ID',
            'theatre_name' => 'Theatre Name',
            'amenities' => 'Amenities',
            'tags' => 'Tags',
            'videos' => 'Videos',
            'min_rate' => 'Min Rate',
            'min_rate_source' => 'Min Rate Source',
            'cancel_policy_text' => 'Cancel Policy Text',
            'updated_at' => 'Updated At',
            'location_lat' => 'Location Lat',
            'location_lng' => 'Location Lng',
            'call_us_to_book' => 'Call Us To Book',
            'external_service' => 'External Service',
            'change_status_date' => 'Change Status Date',
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
     * Gets query for [[ShowsPhotoJoins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShowsPhotoJoins()
    {
        return $this->hasMany(ShowsPhotoJoin::class, ['item_id' => 'id']);
    }

    /**
     * Gets query for [[TrPrices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrPrices()
    {
        return $this->hasMany(TrPrices::class, ['id_external' => 'id_external']);
    }

    /**
     * Gets query for [[TrShowsCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrShowsCategories()
    {
        return $this->hasMany(TrShowsCategories::class, ['id_external_show' => 'id_external']);
    }

    /**
     * Gets query for [[TrShowsSimilars]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrShowsSimilars()
    {
        return $this->hasMany(TrShowsSimilar::class, ['external_id' => 'id_external']);
    }

    /**
     * Gets query for [[TrShowsSimilars0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrShowsSimilars0()
    {
        return $this->hasMany(TrShowsSimilar::class, ['similar_external_id' => 'id_external']);
    }

    /**
     * Gets query for [[VacationPackageShows]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVacationPackageShows()
    {
        return $this->hasMany(VacationPackageShow::class, ['item_external_id' => 'id_external']);
    }
}

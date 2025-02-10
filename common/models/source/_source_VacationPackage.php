<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vacation_package".
 *
 * @property int $id
 * @property int $vp_external_id
 * @property string $name
 * @property string|null $code
 * @property string|null $description
 * @property int $status
 * @property string $period_start
 * @property string $period_end
 * @property string $valid_start
 * @property string $valid_end
 * @property string|null $hash
 * @property string|null $data
 * @property int|null $preview_id
 * @property int|null $image_id
 * @property string|null $channel
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string $change_status_date
 *
 * @property ContentFiles $image
 * @property ContentFiles $preview
 * @property VacationPackageAttraction[] $vacationPackageAttractions
 * @property VacationPackageCategory[] $vacationPackageCategories
 * @property VacationPackagePrice[] $vacationPackagePrices
 * @property VacationPackageShow[] $vacationPackageShows
 */
class _source_VacationPackage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vacation_package';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vp_external_id', 'name', 'status', 'period_start', 'period_end', 'valid_start', 'valid_end'], 'required'],
            [['vp_external_id', 'status', 'preview_id', 'image_id'], 'integer'],
            [['period_start', 'period_end', 'valid_start', 'valid_end', 'created_at', 'updated_at', 'change_status_date'], 'safe'],
            [['data'], 'string'],
            [['name', 'code'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 4096],
            [['hash', 'channel'], 'string', 'max' => 32],
            [['vp_external_id'], 'unique'],
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
            'vp_external_id' => 'Vp External ID',
            'name' => 'Name',
            'code' => 'Code',
            'description' => 'Description',
            'status' => 'Status',
            'period_start' => 'Period Start',
            'period_end' => 'Period End',
            'valid_start' => 'Valid Start',
            'valid_end' => 'Valid End',
            'hash' => 'Hash',
            'data' => 'Data',
            'preview_id' => 'Preview ID',
            'image_id' => 'Image ID',
            'channel' => 'Channel',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
     * Gets query for [[VacationPackageAttractions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVacationPackageAttractions()
    {
        return $this->hasMany(VacationPackageAttraction::class, ['vp_external_id' => 'vp_external_id']);
    }

    /**
     * Gets query for [[VacationPackageCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVacationPackageCategories()
    {
        return $this->hasMany(VacationPackageCategory::class, ['vp_external_id' => 'vp_external_id']);
    }

    /**
     * Gets query for [[VacationPackagePrices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVacationPackagePrices()
    {
        return $this->hasMany(VacationPackagePrice::class, ['vp_external_id' => 'vp_external_id']);
    }

    /**
     * Gets query for [[VacationPackageShows]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVacationPackageShows()
    {
        return $this->hasMany(VacationPackageShow::class, ['vp_external_id' => 'vp_external_id']);
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vacation_package_price".
 *
 * @property int $id
 * @property int $vp_external_id
 * @property float $price
 * @property int $count
 *
 * @property VacationPackage $vpExternal
 */
class _source_VacationPackagePrice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vacation_package_price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vp_external_id', 'price', 'count'], 'required'],
            [['vp_external_id', 'count'], 'integer'],
            [['price'], 'number'],
            [['vp_external_id'], 'exist', 'skipOnError' => true, 'targetClass' => VacationPackage::class, 'targetAttribute' => ['vp_external_id' => 'vp_external_id']],
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
            'price' => 'Price',
            'count' => 'Count',
        ];
    }

    /**
     * Gets query for [[VpExternal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVpExternal()
    {
        return $this->hasOne(VacationPackage::class, ['vp_external_id' => 'vp_external_id']);
    }
}

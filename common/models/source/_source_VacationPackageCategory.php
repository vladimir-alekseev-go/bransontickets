<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vacation_package_category".
 *
 * @property int $id
 * @property int $vp_external_id
 * @property string $name
 *
 * @property VacationPackage $vpExternal
 */
class _source_VacationPackageCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vacation_package_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vp_external_id', 'name'], 'required'],
            [['vp_external_id'], 'integer'],
            [['name'], 'string', 'max' => 128],
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
            'name' => 'Name',
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

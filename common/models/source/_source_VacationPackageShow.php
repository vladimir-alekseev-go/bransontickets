<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vacation_package_show".
 *
 * @property int $id
 * @property int $vp_external_id
 * @property int $item_external_id
 * @property int|null $item_type_id
 *
 * @property TrShows $itemExternal
 * @property VacationPackage $vpExternal
 */
class _source_VacationPackageShow extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vacation_package_show';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vp_external_id', 'item_external_id'], 'required'],
            [['vp_external_id', 'item_external_id', 'item_type_id'], 'integer'],
            [['item_external_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrShows::class, 'targetAttribute' => ['item_external_id' => 'id_external']],
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
            'item_external_id' => 'Item External ID',
            'item_type_id' => 'Item Type ID',
        ];
    }

    /**
     * Gets query for [[ItemExternal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItemExternal()
    {
        return $this->hasOne(TrShows::class, ['id_external' => 'item_external_id']);
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

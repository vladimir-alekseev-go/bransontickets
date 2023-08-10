<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_admissions".
 *
 * @property int $id
 * @property int $id_external
 * @property int $id_external_item
 * @property string $name
 * @property string $hash_summ
 * @property string|null $inclusions
 * @property string|null $exclusions
 *
 * @property TrAttractions $externalItem
 * @property TrAttractionsAvailability[] $trAttractionsAvailabilities
 * @property TrAttractionsPrices[] $trAttractionsPrices
 */
class _source_TrAdmissions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_admissions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'id_external_item', 'name', 'hash_summ'], 'required'],
            [['id_external', 'id_external_item'], 'integer'],
            [['inclusions', 'exclusions'], 'string'],
            [['name'], 'string', 'max' => 64],
            [['hash_summ'], 'string', 'max' => 32],
            [['id_external_item'], 'exist', 'skipOnError' => true, 'targetClass' => TrAttractions::class, 'targetAttribute' => ['id_external_item' => 'id_external']],
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
            'inclusions' => 'Inclusions',
            'exclusions' => 'Exclusions',
        ];
    }

    /**
     * Gets query for [[ExternalItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternalItem()
    {
        return $this->hasOne(TrAttractions::class, ['id_external' => 'id_external_item']);
    }

    /**
     * Gets query for [[TrAttractionsAvailabilities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrAttractionsAvailabilities()
    {
        return $this->hasMany(TrAttractionsAvailability::class, ['id_external' => 'id_external']);
    }

    /**
     * Gets query for [[TrAttractionsPrices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrAttractionsPrices()
    {
        return $this->hasMany(TrAttractionsPrices::class, ['id_external' => 'id_external']);
    }
}

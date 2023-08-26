<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_categories".
 *
 * @property int $id
 * @property int $id_external
 * @property string|null $name
 * @property string $hash_summ
 * @property int|null $sort_shows
 * @property int|null $sort_attractions
 * @property int|null $sort_hotels
 *
 * @property TrAttractionsCategories[] $trAttractionsCategories
 * @property TrPosHotelsCategories[] $trPosHotelsCategories
 * @property TrPosPlHotelsCategories[] $trPosPlHotelsCategories
 * @property TrShowsCategories[] $trShowsCategories
 */
class _source_TrCategories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'hash_summ'], 'required'],
            [['id_external', 'sort_shows', 'sort_attractions', 'sort_hotels'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['hash_summ'], 'string', 'max' => 32],
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
            'hash_summ' => 'Hash Summ',
            'sort_shows' => 'Sort Shows',
            'sort_attractions' => 'Sort Attractions',
            'sort_hotels' => 'Sort Hotels',
        ];
    }

    /**
     * Gets query for [[TrAttractionsCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrAttractionsCategories()
    {
        return $this->hasMany(TrAttractionsCategories::class, ['id_external_category' => 'id_external']);
    }

    /**
     * Gets query for [[TrPosHotelsCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrPosHotelsCategories()
    {
        return $this->hasMany(TrPosHotelsCategories::class, ['id_external_category' => 'id_external']);
    }

    /**
     * Gets query for [[TrPosPlHotelsCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrPosPlHotelsCategories()
    {
        return $this->hasMany(TrPosPlHotelsCategories::class, ['id_external_category' => 'id_external']);
    }

    /**
     * Gets query for [[TrShowsCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrShowsCategories()
    {
        return $this->hasMany(TrShowsCategories::class, ['id_external_category' => 'id_external']);
    }
}

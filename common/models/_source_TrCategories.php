<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_categories".
 *
 * @property int $id
 * @property int $id_external
 * @property string $name
 * @property string|null $hash_summ
 * @property int|null $sort_shows
 * @property int|null $sort_attractions
 * @property int|null $sort_hotels
 *
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
            [['id_external', 'name'], 'required'],
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
     * Gets query for [[TrShowsCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrShowsCategories()
    {
        return $this->hasMany(TrShowsCategories::class, ['id_external_category' => 'id_external']);
    }
}

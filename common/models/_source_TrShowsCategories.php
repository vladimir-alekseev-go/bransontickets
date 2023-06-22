<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_shows_categories".
 *
 * @property int $id
 * @property int $id_external_show
 * @property int $id_external_category
 *
 * @property TrCategories $externalCategory
 * @property TrShows $externalShow
 */
class _source_TrShowsCategories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_shows_categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external_show', 'id_external_category'], 'required'],
            [['id_external_show', 'id_external_category'], 'integer'],
            [['id_external_show'], 'exist', 'skipOnError' => true, 'targetClass' => TrShows::class, 'targetAttribute' => ['id_external_show' => 'id_external']],
            [['id_external_category'], 'exist', 'skipOnError' => true, 'targetClass' => TrCategories::class, 'targetAttribute' => ['id_external_category' => 'id_external']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_external_show' => 'Id External Show',
            'id_external_category' => 'Id External Category',
        ];
    }

    /**
     * Gets query for [[ExternalCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternalCategory()
    {
        return $this->hasOne(TrCategories::class, ['id_external' => 'id_external_category']);
    }

    /**
     * Gets query for [[ExternalShow]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternalShow()
    {
        return $this->hasOne(TrShows::class, ['id_external' => 'id_external_show']);
    }
}

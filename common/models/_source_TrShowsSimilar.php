<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_shows_similar".
 *
 * @property int $id
 * @property int $external_id
 * @property int $similar_external_id
 * @property string|null $created_at
 *
 * @property TrShows $external
 * @property TrShows $similarExternal
 */
class _source_TrShowsSimilar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_shows_similar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['external_id', 'similar_external_id'], 'required'],
            [['external_id', 'similar_external_id'], 'integer'],
            [['created_at'], 'safe'],
            [['external_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrShows::class, 'targetAttribute' => ['external_id' => 'id_external']],
            [['similar_external_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrShows::class, 'targetAttribute' => ['similar_external_id' => 'id_external']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'external_id' => 'External ID',
            'similar_external_id' => 'Similar External ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[External]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternal()
    {
        return $this->hasOne(TrShows::class, ['id_external' => 'external_id']);
    }

    /**
     * Gets query for [[SimilarExternal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSimilarExternal()
    {
        return $this->hasOne(TrShows::class, ['id_external' => 'similar_external_id']);
    }
}

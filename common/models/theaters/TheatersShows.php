<?php

namespace common\models\theaters;

use Yii;

/**
 * This is the model class for table "theaters_shows".
 *
 * @property int $id
 * @property int $theater_id
 * @property int $id_external
 */
class TheatersShows extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'theaters_shows';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['theater_id', 'id_external'], 'required'],
            [['theater_id', 'id_external'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'theater_id' => 'Theater ID',
            'id_external' => 'Id External',
        ];
    }
}

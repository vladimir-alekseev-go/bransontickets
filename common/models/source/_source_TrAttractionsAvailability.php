<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tr_attractions_availability".
 *
 * @property int $id
 * @property int $id_external
 * @property string $date
 *
 * @property TrAdmissions $external
 */
class _source_TrAttractionsAvailability extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tr_attractions_availability';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'date'], 'required'],
            [['id_external'], 'integer'],
            [['date'], 'safe'],
            [['id_external'], 'exist', 'skipOnError' => true, 'targetClass' => TrAdmissions::class, 'targetAttribute' => ['id_external' => 'id_external']],
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
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[External]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExternal()
    {
        return $this->hasOne(TrAdmissions::class, ['id_external' => 'id_external']);
    }
}

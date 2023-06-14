<?php

namespace common\models\theaters;

use Yii;

/**
 * This is the model class for table "theaters".
 *
 * @property int $id
 * @property string $name
 * @property string $domain
 * @property string $site
 * @property string $phone
 */
class Theaters extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'theaters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'domain', 'site', 'phone'], 'required'],
            [['name', 'site', 'phone'], 'string', 'max' => 64],
            [['domain'], 'string', 'max' => 2048],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'domain' => 'Domain',
            'site' => 'Site',
            'phone' => 'Phone',
        ];
    }

    public static function getAllowable()
    {
    	return self::find()->where(['like','domain', Yii::$app->request->getServerName()]);
    }
}

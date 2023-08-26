<?php

namespace backend\models\search;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\TrPosHotels;

class TrPosHotelsSearch extends TrPosHotels
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'id', 'status'], 'integer'],
            [['name'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);

        $this->load($params);

        $query->andFilterWhere([
            'id' => $this->id,
            'id_external' => $this->id_external,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}

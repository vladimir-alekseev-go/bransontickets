<?php

namespace backend\models\search;

use common\models\redirects\Redirects;
use Yii;
use yii\data\ActiveDataProvider;

class RedirectsSearch extends Redirects
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id', 'id'], 'integer'],
            [['old_url', 'new_url', 'category', 'status_code'], 'string'],
        ];
    }

    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
                'pagination' => [
                    'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
                ],
                'sort' => [
                    'defaultOrder' => [
                        'id' => SORT_DESC,
                    ],
                ],
            ]
        );

        $this->load($params);

        $query->andFilterWhere(
            [
                'id' => $this->id,
                'item_id' => $this->item_id,
                'category' => $this->category,
            ]
        );

        $query->andFilterWhere(['like', 'old_url', $this->old_url]);
        $query->andFilterWhere(['like', 'new_url', $this->new_url]);
        $query->andFilterWhere(['like', 'status_code', $this->status_code]);

        return $dataProvider;
    }
}

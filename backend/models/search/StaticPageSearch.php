<?php

namespace backend\models\search;

use common\models\StaticPage;
use Yii;
use yii\data\ActiveDataProvider;

class StaticPageSearch extends StaticPage
{
    public function rules(): array
    {
        return [
            [['id', 'status'], 'integer'],
            [['title', 'url', 'text'], 'string'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider(
            [
                'query'      => $query,
                'pagination' => [
                    'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
                ],
                'sort'       => [
                    'defaultOrder' => [
                        'id' => SORT_ASC,
                    ],
                ],
            ]
        );

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(
            [
                'id'     => $this->id,
                'status' => $this->status,
            ]
        );

        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'url', $this->url]);
        $query->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}

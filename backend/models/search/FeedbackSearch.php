<?php

namespace backend\models\search;

use common\models\Feedback;
use Yii;
use yii\data\ActiveDataProvider;

class FeedbackSearch extends Feedback
{
    public function rules(): array
    {
        return [
            [['id', 'subject_id'], 'integer'],
            [['name', 'email', 'message'], 'string'],
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
                        'id' => SORT_DESC,
                    ],
                ],
            ]
        );

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(
            [
                'id'         => $this->id,
                'subject_id' => $this->subject_id,
            ]
        );

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'email', $this->email]);
        $query->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}

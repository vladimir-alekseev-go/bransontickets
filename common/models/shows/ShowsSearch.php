<?php
namespace common\models\shows;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ShowsSearch extends \common\models\TrShows
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'id_external',
                    'status',
                    'show_in_footer',
                    'rank_level',
                    'marketing_level',
                    'weekly_schedule',
                    'cut_off',
                    'display_image'
                ],
                'integer'
            ],
            [
                [
                    'code',
                    'name',
                    'tags'
                ],
                'string'
            ]
        ];
    }

    /**
     * @param [] $params
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
            'show_in_footer' => $this->show_in_footer,
            'rank_level' => $this->rank_level,
            'marketing_level' => $this->marketing_level,
            'weekly_schedule' => $this->weekly_schedule,
            'cut_off' => $this->cut_off,
            'display_image' => $this->display_image,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'tags', $this->tags]);

        return $dataProvider;
    }
}

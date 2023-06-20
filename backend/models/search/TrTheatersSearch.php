<?php
namespace backend\models\search;

use Yii;
use yii\data\ActiveDataProvider;

class TrTheatersSearch extends \common\models\TrTheaters
{
    public $status = \common\models\TrTheaters::STATUS_ACTIVE;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_external', 'id', 'status'], 'integer'],
            [['name', 'address1', 'address2', 'city', 'state', 'zip_code'], 'string'],
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
                    'location_updated_at' => SORT_ASC,
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
        $query->andFilterWhere(['like', 'address1', $this->address1]);
        $query->andFilterWhere(['like', 'address2', $this->address2]);
        $query->andFilterWhere(['like', 'city', $this->city]);
        $query->andFilterWhere(['like', 'state', $this->state]);
        $query->andFilterWhere(['like', 'zip_code', $this->zip_code]);
        
        return $dataProvider;
    }
}

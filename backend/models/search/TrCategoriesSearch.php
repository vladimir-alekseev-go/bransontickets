<?php

namespace backend\models\search;

/*use common\models\TrAttractions;
use common\models\TrAttractionsCategories;*/
use common\models\TrCategories;
/*use common\models\TrLunchs;
use common\models\TrLunchsCategories;
use common\models\TrPosHotels;
use common\models\TrPosHotelsCategories;*/
use common\models\TrShows;
use common\models\TrShowsCategories;
use Yii;
use yii\data\ActiveDataProvider;

class TrCategoriesSearch extends TrCategories
{
    public $type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['type', 'string']
        ]);
    }


    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = self::find()
            ->joinWith(['trShowsCategories'/*, 'trAttractionsCategories', 'trLunchsCategories', 'trPosHotelsCategories'*/])
            ->groupBy([self::tableName() . '.id'])
        ;

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

        $tableName = self::getCategoryTable($this->type);

        if ($tableName) {
            $query->select(
                [
                    self::tableName() . '.*',
                    'count(' . $tableName . '.id_external_category) as type_count'
                ]
            )->having('type_count > 0');
        }

        $query->andFilterWhere(
            [
                'id'               => $this->id,
                'id_external'      => $this->id_external,
                'sort_shows'       => $this->sort_shows,
                /*'sort_attractions' => $this->sort_attractions,
                'sort_hotels'      => $this->sort_hotels,
                'sort_dining'      => $this->sort_dining,*/
            ]
        )
        ;

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    private static function getCategoryTable($type): ?string
    {
        return self::getCategoriesTables()[$type] ?? null;
    }

    private static function getCategoriesTables(): array
    {
        return [
            TrShows::TYPE => TrShowsCategories::tableName(),
            /*TrAttractions::TYPE => TrAttractionsCategories::tableName(),
            TrLunchs::TYPE => TrLunchsCategories::tableName(),
            TrPosHotels::TYPE => TrPosHotelsCategories::tableName(),*/
        ];
    }

    public static function getTypes(): array
    {
        return [
            TrShows::TYPE => TrShows::NAME,
            /*TrAttractions::TYPE => TrAttractions::NAME,
            TrLunchs::TYPE => TrLunchs::NAME,
            TrPosHotels::TYPE => TrPosHotels::NAME,*/
        ];
    }
}

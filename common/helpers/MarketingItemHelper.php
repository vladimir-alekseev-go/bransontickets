<?php

namespace common\helpers;

use common\models\TrAttractions;
use common\models\TrPosHotels;
use common\models\TrShows;

class MarketingItemHelper
{
    /**
     * @return array The array keys are the class names, and the array values are the corresponding item types.
     */
    public static function getItemTypes(): array
    {
        return [
            TrAttractions::class => TrAttractions::TYPE,
            TrShows::class       => TrShows::TYPE,
            TrPosHotels::class   => TrPosHotels::TYPE,
        ];
    }

    public static function getItemClassName($type): ?string
    {
        return self::getItemClassNames()[$type] ?: null;
    }

    /**
     * @return array
     * The array keys are the item types, and the array values are the corresponding class names.
     */
    public static function getItemClassNames(): array
    {
        return [
            TrAttractions::TYPE => TrAttractions::class,
            TrShows::TYPE       => TrShows::class,
            TrPosHotels::TYPE   => TrPosHotels::class,
            'hotels'            => TrPosHotels::class,
        ];
    }

    /**
     * @return array The array keys are the item types, and the array values are the corresponding names.
     */
    public static function getItemNames(): array
    {
        return [
            TrAttractions::TYPE => TrAttractions::NAME,
            TrShows::TYPE       => TrShows::NAME,
            'hotel'             => TrPosHotels::NAME,
            'hotels'            => TrPosHotels::NAME,
        ];
    }

//    /**
//     * @param TrShows|TrAttractions|Lunchs|Restaurant|TrPosHotels $items
//     * @param array                                               $params
//     *
//     * @return ActiveDataProvider
//     */
//    public static function getDataProviderItemsSearch($items, $params): ActiveDataProvider
//    {
//        $query = $items::find();
//
//        $dataProvider = new ActiveDataProvider(
//            [
//                'query' => $query,
//                'pagination' => [
//                    'pageSize' => Yii::$app->request->cookies->getValue('_grid_page_size', 20),
//                ],
//                'sort' => [
//                    'defaultOrder' => [
//                        'id' => SORT_ASC,
//                    ],
//                ],
//            ]
//        );
//
//        if (!($items->load($params) && $items->validate())) {
//            return $dataProvider;
//        }
//
//        $query->andFilterWhere(
//            [
//                'id' => $items->id,
//                'id_external' => $items->id_external,
//                'status' => $items->status,
//                'show_in_footer' => $items->show_in_footer,
//                //'location' => $items->location,
//                'rank_level' => $items->rank_level,
//                'marketing_level' => $items->marketing_level,
//                'weekly_schedule' => $items->weekly_schedule,
//                'seats' => $items->seats,
//                'show_length' => $items->show_length,
//                'cut_off' => $items->cut_off,
//                'image_id' => $items->image_id,
//                'display_image' => $items->display_image,
//                'tax_rate' => $items->tax_rate,
//                'theatre_id' => $items->theatre_id,
//                'updated_at' => $items->updated_at,
//            ]
//        );
//
//        $query->andFilterWhere(['like', 'code', $items->code]);
//        $query->andFilterWhere(['like', 'name', $items->name]);
//        $query->andFilterWhere(['like', 'description', $items->description]);
//        $query->andFilterWhere(['like', 'voucher_procedure', $items->voucher_procedure]);
//        $query->andFilterWhere(['like', 'on_special_text', $items->on_special_text]);
//        $query->andFilterWhere(['like', 'cast_size', $items->cast_size]);
//        $query->andFilterWhere(['like', 'intermissions', $items->intermissions]);
//        $query->andFilterWhere(['like', 'photos', $items->photos]);
//        $query->andFilterWhere(['like', 'tags', $items->tags]);
//        $query->andFilterWhere(['like', 'videos', $items->videos]);
//        $query->andFilterWhere(['like', 'updated_at', $items->updated_at]);
//
//        return $dataProvider;
//    }
//
//    /**
//     * @return array
//     */
//    public static function getRulesItemsSearch(): array
//    {
//        return [
//            [
//                [
//                    'id',
//                    'id_external',
//                    'status',
//                    'show_in_footer',
//                    'rank_level',
//                    'marketing_level',
//                    'weekly_schedule',
//                    'seats',
//                    'show_length',
//                    'cut_off',
//                    'image_id',
//                    'display_image',
//                ],
//                'integer',
//            ],
//            [
//                [
//                    'code',
//                    'name',
//                    'description',
//                    'voucher_procedure',
//                    'on_special_text',
//                    'cast_size',
//                    'intermissions',
//                    'tax_rate',
//                    'photos',
//                    'tags',
//                    'videos',
//                    'updated_at',
//                ],
//                'string',
//            ],
//        ];
//    }
}

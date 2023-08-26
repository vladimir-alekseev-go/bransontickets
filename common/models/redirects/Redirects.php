<?php

namespace common\models\redirects;

use common\models\TrAttractions;
use common\models\TrShows;
use DateTime;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class Redirects extends _source_Redirects
{
    public const CATEGORY_SHOW = 'show';
    public const CATEGORY_ATTRACTION = 'attraction';
    public const CATEGORY_HOTEL_POS = 'hotel-pos';
    public const CATEGORY_HOTEL_PRICE_LINE = 'hotel-pl';

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'created_at',
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }
}

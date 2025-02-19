<?php

namespace common\events;

use common\models\TrBasket;
use yii\db\Expression;

class UserEvents
{
    public static function handleAfterLogin($event): void
    {
        $event->sender->identity->logined_at = new Expression('NOW()');
        $event->sender->identity->save();

        // set the connect between basket with user
        (new TrBasket())->setForUser($event->sender->id);
    }
}

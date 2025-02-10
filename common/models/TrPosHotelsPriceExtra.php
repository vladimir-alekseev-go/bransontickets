<?php

namespace common\models;

/**
 * @deprecated
 */
class TrPosHotelsPriceExtra extends TrPosHotelsPrice
{
    use PricesExtensionTrait;

    public const MAIN_CLASS = TrPosHotels::class;

    public const ANY_TIME = 'Any time';
    public const TYPE_ID = 5;
    /**
     * @deprecated
     */
    public const type = 'hotels_extra';
    public const TYPE = 'hotels_extra';

}

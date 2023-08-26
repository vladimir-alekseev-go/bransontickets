<?php

namespace common\models\priceLine;

use yii\base\Model;

class PriceLine extends Model
{
    /**
     * @var float $price
     */
    public $price;

    /**
     * @var float $strikeoutPrice
     */
    public $strikeoutPrice;

    /**
     * @var string $promoTitle
     */
    public $promoTitle;

    /**
     * @var string $promoTerms
     */
    public $promoTerms;

    /**
     * @var float $displaySubTotal
     */
    public $displaySubTotal;

    /**
     * @var float $displayInsuranceFee
     */
    public $displayInsuranceFee;

    /**
     * @var float $displayProcessingFee
     */
    public $displayProcessingFee;

    /**
     * @var float $displayPropertyFee
     */
    public $displayPropertyFee;

    /**
     * @var array $nightPriceData
     */
    public $nightPriceData = [];

    public function loadData($data): void
    {
        if (!empty($data['promo']['display_strikeout_price'])) {
            $this->strikeoutPrice = $data['promo']['display_strikeout_price'];
        }
        if (!empty($data['promo']['title'])) {
            $this->promoTitle = $data['promo']['title'];
        }
        if (!empty($data['promo']['terms'])) {
            $this->promoTerms = $data['promo']['terms'];
        }
        if (!empty($data['display_price'])) {
            $this->price = $data['display_price'];
        }
        if (!empty($data['display_sub_total'])) {
            $this->displaySubTotal = $data['display_sub_total'];
        }
        if (!empty($data['display_insurance_fee'])) {
            $this->displayInsuranceFee = $data['display_insurance_fee'];
        }
        if (!empty($data['display_processing_fee'])) {
            $this->displayProcessingFee = $data['display_processing_fee'];
        }
        if (!empty($data['display_property_fee'])) {
            $this->displayPropertyFee = $data['display_property_fee'];
        }
        if (!empty($data['night_price_data'])) {
            $this->nightPriceData = $data['night_price_data'];
        }
    }
}

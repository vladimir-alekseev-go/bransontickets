<?php

namespace common\models\form;

use common\models\Coupon;
use common\models\TrBasket;
use common\tripium\Tripium;
use Throwable;
use yii\base\Model;
use yii\db\StaleObjectException;

class CartCouponForm extends Model
{
    /**
     * @var Coupon $coupon_data
     */
    private $coupon_data;

    public $coupon;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['coupon', 'ValidateCoupon'],
            ['coupon', 'trim'],
            [['coupon'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'], //xss protection
        ];
    }

    public function ValidateCoupon($attribute): void
    {
        $Tripium = new Tripium;
        $this->coupon_data = $Tripium->getCouponByCode($this->$attribute);
        if (!($this->coupon_data instanceof Coupon)) {
            $this->addError($attribute, 'Discount code "' . $this->$attribute . '" is not valid');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'coupon' => 'Promo Code',
        ];
    }

    /**
     * @return bool|false|int
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function send()
    {
        if (!$this->validate()) {
            return false;
        }
        $Basket = TrBasket::build();
        $Basket->setAttribute('accept_terms', TrBasket::ACCEPT_TERMS_NO);
        return $Basket->setCoupon($this->coupon_data);
    }
}

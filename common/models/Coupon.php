<?php

namespace common\models;

use yii\base\Model;

class Coupon extends Model
{
    public const COUPON_TYPE_DESKTOP = 'desktop';
    public const COUPON_TYPE_MOBILE = 'mobile';
    
    private $_discounted_total;
    private $_discount;
    
    public $code;
    public $type;
    public $value;
    public $description;
    public $discount_type;
    public $site_types = [];
    public $auto = false;
    
    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'discount';
        $fields[] = 'discounted_total';
        $fields[] = 'full_total';
        return $fields;
    }
    
    /**
     * Set discount
     * @param float $discount
     */
    public function setDiscount($discount)
    {
        $this->_discount = number_format(round($discount,2), 2, '.', '');
	}
	
    /**
     * Get discount
     * @return float
     */
    public function getDiscount()
    {
        return number_format(round($this->_discount,2), 2, '.', '');
	}
    
    /**
     * Set Discounted Total
     * @param float $discountedTotal
     */
	public function setDiscounted_total($discountedTotal)
    {
        $this->_discounted_total = number_format(round($discountedTotal,2), 2, '.', '');
	}
	
    /**
     * Get Discounted Total
     * @return float
     */
	public function getDiscounted_total()
    {
        return number_format(round($this->_discounted_total,2), 2, '.', '');
	}
	
	public function loadData($data)
    {
        $data = self::setSiteTypes($data);

        if (!empty($data['code'])) {
            $this->code = $data['code'];
        }
        if (!empty($data['discountedTotal'])) {
            $this->setDiscounted_total($data['discountedTotal']);
        }
        if (!empty($data['discounted_total'])) {
            $this->setDiscounted_total($data['discounted_total']);
        }
        if (!empty($data['discount'])) {
            $this->setDiscount($data['discount']);
        }
        if (!empty($data['type'])) {
            $this->type = $data['type'];
        }
        if (!empty($data['value'])) {
            $this->value = $data['value'];
        }
        if (!empty($data['description'])) {
            $this->description = $data['description'];
        }
        if (!empty($data['discountType'])) {
            $this->discount_type = $data['discountType'];
        }
        if (!empty($data['discount_type'])) {
            $this->discount_type = $data['discount_type'];
        }
        if (!empty($data['siteTypes'])) {
            $this->site_types = $data['siteTypes'];
        }
        if (!empty($data['site_types'])) {
            $this->site_types = $data['site_types'];
        }
        if (isset($data['auto'])) {
            $this->auto = $data['auto'];
        }
    }
    
    public function isTypeOf($type)
    {
        return in_array($type, $this->site_types, false);
    }
    
    public function getFullTotal()
    {
        return number_format(round($this->getDiscounted_total() + $this->getDiscount(),2), 2, '.', '');
    }
    
    public function getFull_total()
    {
        return $this->getFullTotal();
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    private static function setSiteTypes($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        if (!isset($data['channelType'])){
            $data['siteTypes'] = [
                self::COUPON_TYPE_DESKTOP,
                self::COUPON_TYPE_MOBILE
            ];
        } elseif ($data['channelType'] === true) {
            $data['siteTypes'] = [self::COUPON_TYPE_DESKTOP];
        } elseif ($data['channelType'] === false) {
            $data['siteTypes'] = [self::COUPON_TYPE_MOBILE];
        }
        return $data;
    }
}

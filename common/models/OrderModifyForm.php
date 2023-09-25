<?php
namespace common\models;

use common\helpers\StrHelper;
use common\tripium\Tripium;
use yii\helpers\Json;

class OrderModifyForm extends OrderForm
{
    private $hashData;
    public $coupon_code;
    
    /**
     * @inheritdoc
     */
    function formName()
	{
		return 'OrderForm';
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
	    return array_merge(parent::attributeLabels(), [
	        'coupon_code' => 'Promo Code',
	    ]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
	    return array_merge(parent::rules(), [
	        ['coupon_code', 'safe'],
	        ['date_format', 'safe'],
	    ]);
	}
	
	public function getCouponCode(): ?string
	{
	    return !empty($this->coupon_code) ? $this->coupon_code : null;
	}

	public function createRequest(): array
	{
        $request = [
            "order" => $this->package_modify_data->order,
            "packageId" => $this->package_modify_data->package_id,
            "id" => $this->package_modify_data->id, //[show/attracation/dining id],
            "typeId" => $this->package_modify_data->type_id, //[admission/certificate id],
            "category" => $this->package_modify_data->category, //[shows/attractions/dining],
            "date" => $this->date->format("m/d/Y"),
            "time" => $this->package_modify_data->category === TrShows::TYPE
            || $this->package_modify_data->category === TrAttractions::TYPE ? $this->date->format("h:i A") : '',
        ];
		
		if (isset($this->prices[0]['any_time']) && $this->prices[0]['any_time']) {
		    $request['time'] = TrAttractionsPrices::ANY_TIME;
		}

		foreach ($this->prices as $price) {
//            $qty = $this->getAttributes([self::getAttributeName($price)])[self::getAttributeName($price)];
//            $qty = !empty($qty) && (int)$qty !== 0 ? (int)$qty : null;
//
//		    $family_pass = 0;
//		    if ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS) {
//		        $family_pass = (int)$this->{self::getAttributeName($price).self::SEATS_FIELD_SUB_NAME};
//                $qty = $family_pass === 0 ? 0 : $qty;
//		    }
//		    if ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS_4_PACK) {
//		        $family_pass = (int)$this->family_pass_4_seat;
//                $qty = $family_pass === 0 ? 0 : $qty;
//		    }
//		    if ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS_8_PACK) {
//		        $family_pass = (int)$this->family_pass_8_seat;
//                $qty = $family_pass === 0 ? 0 : $qty;
//		    }
			$request["tickets"][] = [
				"name" => $price["name"],
	    		"description" => $price["description"],
			    "qty" => $this->getQuantity($price),
	    		"info" => $price["id"],
	    		"id" => $price["price_external_id"],
                'seats' => $this->getQuantitySeats($price),
			    'nonRefundable' => $this->isAlternativeRate($price),
			];
		}

		return $request;
	}

	public function correctFamilyPack(): void
    {
//        foreach ($this->prices as $price) {
//            if (($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS)
//                && (int)$this->{self::getAttributeName($price) . self::SEATS_FIELD_SUB_NAME} === 0) {
//                $this->setAttributes([self::getAttributeName($price) => 0]);
//            }
//            if ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS_4_PACK
//                && (int)$this->family_pass_4_seat === 0) {
//                $this->setAttributes([self::getAttributeName($price) => 0]);
//            }
//            if ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS_8_PACK
//                && (int)$this->family_pass_8_seat === 0) {
//                $this->setAttributes([self::getAttributeName($price) => 0]);
//            }
//        }
	}

	public function check()
    {
    	$Tripium = new Tripium;
    	$Order = $this->getOrder();
    	$ModifyPackageCurrent = $Order->getPackageById($this->package_modify_data->package_id);
    	
		$result = $Tripium->orderModifyCheck($this->package_modify_data->order, $this->package_modify_data->package_id, $this->createRequest());
		
        if (!empty($result["globalErrors"])) {
			$this->addError("check", $result["globalErrors"][0]);
			return false;
		}
		
		$coupon = $this->getCoupon((bool)$this->getCouponCode());

		$ModifyPackageNew = new Package;
		$ModifyPackageNew->loadData($result);
		
		$this->updatePricesByPackages([$ModifyPackageNew]);
		
		$result['coupon'] = $coupon;
		$result['getCouponCode'] = $this->getCouponCode();

		$result['_currentOrderData'] = $Order->getData();
		$result['_resultRequest'] = [$this->package_modify_data->order, $this->package_modify_data->package_id, $this->createRequest()];
		$result['_resultRequestData'] = $Tripium->requestData;
		$result['_resultResponse'] = $result;
		
		$result['totalModifyPackageNew'] = $ModifyPackageNew->total ?? 0;
		$result['ticketsQtyModifyPackageNew'] = $ModifyPackageNew->getTicketsQty();
		
		$result['fullTotalOrderCurrent'] = $Order->getFullTotal() + ($Order->getCoupon() ? $Order->getCoupon()->discount : 0);
		$result['fullTotalModifyPackageCurrent'] = $ModifyPackageCurrent->full_total ?? 0;
		$result['fullTotalModifyPackageNew'] = $ModifyPackageNew->full_total ?? 0;

		$fullTotalOrderNew = $result['fullTotalOrderCurrent'] - $result['fullTotalModifyPackageCurrent'] + $result['fullTotalModifyPackageNew'];

        if ((bool)$this->getCouponCode() === false && $coupon && $coupon->auto
            && ((float)$coupon->getFullTotal() - $coupon->discount) > ($fullTotalOrderNew - ($Order->getCoupon() ? $Order->getCoupon()->discount : 0))) {
            $result['coupon'] = $coupon = null;
        }

		if ($coupon) {
		    $result['fullTotalOrderNew'] = (float)$coupon->getFullTotal();
		} else {
            $result['fullTotalOrderNew'] = $fullTotalOrderNew;
		}

		$result['modifyAmount'] = - $result['fullTotalOrderCurrent'] + $result['fullTotalOrderNew'] + ($Order->getCoupon() ? $Order->getCoupon()->discount : 0) - ($coupon ? $coupon->discount : 0);
		$result['isSpecialPricesCanceled'] = $coupon && $result['fullTotalOrderNew'] != ($coupon->discounted_total + $coupon->discount);

    	return $result; 
    }
	
	public function run($transactions = [])
    {
    	$request = [
    		"packages"=>[$this->createRequest()],
    		"transactions"=>$transactions,
    	]; 
    	
    	if ($this->coupon_code && $coupons = $this->getCoupons()) {
    	    foreach ($coupons as $coupon) {
    	        if (StrHelper::strtolower($coupon->code) == StrHelper::strtolower($this->coupon_code)) {
    	            $request['transactions'][] = [
    	                "paymentMethod" => "Discount Code",
    	                "discountCode" => $coupon->code,
    	                "amount" => $coupon->discount
    	            ];
    	        }
    	    }
    	}
    	
    	$Tripium = new Tripium;
		$result = $Tripium->orderModifyPackage($this->package_modify_data->order, $this->package_modify_data->package_id, $request);

    	if (!empty($result["globalErrors"])) {
			$this->addError("payment", $result["globalErrors"][0]);
			return false;
		}

        return true;
    }
    
    public function getHashData()
    {
        return md5(Json::encode($this->createRequest()));
    }
//
//    /**
//     * Initialize Modify Package
//     * @return bool
//     */
//    public function initPackageData(): bool
//    {
//        if (!parent::initPackageData()) {
//            return false;
//        }
////        if (empty($this->package_modify_data)) {
////            return false;
////        }
////        foreach ($this->package_modify_data->getTickets() as $ticket) {
////            if ($ticket->non_refundable) {
////                $this->isNonRefundable = true;
////            }
////        }
//        return true;
//    }
}

<?php

namespace common\models;

use common\tripium\Tripium;
use DateInterval;
use DateTime;
use Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class TrOrders extends _source_TrOrders
{
    use VacationPackagesTrait;

    public const DISCOUNT = "Discount";
    public const DISCOUNT_COUPON = "Coupon";

    public const STATUS_CANCELLED = 'CANCELLED';
    public const STATUS_CONFIRMED = 'CONFIRMED';

    public $messageCallUsToBookModification;

    /**
     * @return ActiveQuery
     */
    public function getTripiumUser(): ActiveQuery
    {
        return $this->hasOne(Users::class, ['tripium_id' => 'tripium_user_id']);
    }

    /**
     * Builder
     *
     * @param $order
     *
     * @return TrOrders
     */
    public static function build($order)
    {
        $data = [
            "order_number" => $order["orderNumber"],
            "created_at" => date("Y-m-d H:i:s", $order["created"]/1000),
            "data" => Json::encode($order),
            "tripium_user_id" => $order['customerId'],
            "past" => empty($order["past"]) ? 0 : 1,
            "discount" => Orders::getDiscount($order),
            "coupon" => Orders::getDiscountCoupon($order),
            "created" => self::getCreated($order["created"] / 1000)->format('Y-m-d H:i:s'),
        ];

        $data["hash_summ"] = md5(Json::encode($data));

        $TrOrders = new self();
        $TrOrders->setAttributes($data);
        return $TrOrders;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'updated_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $phone = '1-417-337-4814';
        $this->messageCallUsToBookModification = 'Changes and/or cancellations for this item are not able to be done online. <br>Please call '.$phone.' and we can assist you with this item.';
    }

    public static function getDiscount($data)
    {
        $discount = 0;

        if (!$data["transactions"]) {
            return $discount;
        }

        if ($data["transactions"]) {
            foreach ($data["transactions"] as $transaction) {
                if ($transaction["paid"] && $transaction["paymentMethod"] == self::DISCOUNT) {
                    $discount += $transaction["amount"];
                }
            }
        }

        return $discount;
    }

    public static function getDiscountCoupon($data)
    {
        $discount = 0;

        if (!$data["transactions"]) {
            return $discount;
        }

        if ($data["transactions"]) {
            foreach ($data["transactions"] as $transaction) {
                if ($transaction["paymentMethod"] == self::DISCOUNT_COUPON) {
                    $discount += $transaction["amount"];
                }
            }
        }

        return $discount;
    }

    /**
     * Update order from tripium
     *
     * @param bool $force
     *
     * @return bool
     * @throws Exception
     */
    public function updateByTripium($force = false)
    {
        $updatedAt = new DateTime($this->updated_at);
        $limitUpdatedAt = (new DateTime())->sub(new DateInterval('PT5M'));
        if (!$force && $updatedAt > $limitUpdatedAt) {
            return false;
        }

        $Tripium = new Tripium;

        $order = $Tripium->getOrder($this->order_number);
        if (empty($Tripium->errors)) {
            $data = [
                "data" => Json::encode($order),
                "tripium_user_id" => $order["customerId"],
                "discount" => self::getDiscount($order),
                "coupon" => self::getDiscountCoupon($order),
                "created" => self::getCreated($order["created"] / 1000)->format('Y-m-d H:i:s'),
            ];

            $this->setAttributes($data);

            if ($this->save()) {
                foreach ($this->getPackages() as $package) {
                    $item = $package->getItem();
                    if ($item && !empty($item->external_service)
                        && $item->external_service == $item::EXTERNAL_SERVICE_SDC) {
                        $Tripium = new Tripium;
                        $sdcVouchers = $Tripium->getSdcVouchersOrder($this->order_number);
                        if (empty($Tripium->errors)) {
                            $this->setAttributes(['sdc_vouchers'=>Json::encode($sdcVouchers)]);
                            $this->save();
                        }
                        break;
                    }
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Update by tripium user id.
     *
     * @param      $id
     * @param bool $force
     *
     * @return bool
     * @throws Exception
     */
    public static function updateFromTripium($id, $force = false)
    {
        if (!$id) {
            return false;
        }

        /**
         * @var TrOrders $lastOrderUpdate
         */
        $lastOrderUpdate = self::find()
            ->select("updated_at")
            ->where(["tripium_user_id"=>$id])
            ->orderBy('updated_at desc')
            ->one();
        if ($lastOrderUpdate) {
            $updatedAt = new DateTime($lastOrderUpdate->updated_at);
            $limitUpdatedAt = (new DateTime())->sub(new DateInterval('PT5M'));
            if (!$force && $updatedAt > $limitUpdatedAt) {
                return false;
            }
        }
        $orders = self::find()->select("id, order_number, hash_summ")->where(["tripium_user_id"=>$id])->asArray()->all();
        $orders = ArrayHelper::index($orders, 'order_number');

        $Tripium = new Tripium;

        $tripiumOrders1 = $Tripium->getCustomerOrders($id, true);
        $tripiumOrders2 = $Tripium->getCustomerOrders($id, false);

        if (!empty($Tripium->errors)) {
            return false;
        }

        $tripiumOrders = [];

        if (!empty($tripiumOrders1["results"])) {
            foreach ($tripiumOrders1["results"] as &$it) {
                $it["past"] = 1;
            }
        }
        unset($it);
        if (!empty($tripiumOrders2["results"])) {
            foreach ($tripiumOrders2["results"] as &$it) {
                $it["past"] = 0;
            }
        }
        unset($it);
        if (!empty($tripiumOrders1["results"])) {
            $tripiumOrders = array_merge($tripiumOrders, $tripiumOrders1["results"]);
        }

        if (!empty($tripiumOrders2["results"])) {
            $tripiumOrders = array_merge($tripiumOrders, $tripiumOrders2["results"]);
        }

        unset($tripiumOrders1, $tripiumOrders2);
        foreach ($tripiumOrders as $order) {
            $dataShow = [
                "order_number" => $order["orderNumber"],
                "created_at" => date("Y-m-d H:i:s",$order["created"]/1000),
                "data" => Json::encode($order),
                "tripium_user_id" => $id,
                "past" => $order["past"],
                "discount" => Orders::getDiscount($order),
                "coupon" => Orders::getDiscountCoupon($order),
                "created" => self::getCreated($order["created"] / 1000)->format('Y-m-d H:i:s'),
            ];

            $dataShow["hash_summ"] = md5(Json::encode($dataShow));

            if (empty($orders[$order["orderNumber"]])) {

                $model = new self;
                $model->setAttributes($dataShow);
                $model->save();

            } else if ($dataShow["hash_summ"] != $orders[$order["orderNumber"]]["hash_summ"]) {

                $model = self::find()->where(["order_number"=>$order["orderNumber"], "tripium_user_id"=>$id])->one();
                $model->setAttributes($dataShow);
                $model->save();

            }

            unset($orders[$order["orderNumber"]]);
        }

        //delete old orders
        if (!empty($orders)) {
            $orderNumbers = array_keys($orders);
            if ($orderNumbers) {
                self::deleteAll(["order_number"=>$orderNumbers, "tripium_user_id"=>$id]);
            }
        }

        return true;
    }

    /**
     * @param $packageNumber
     *
     * @return Package|null
     */
    public function getPackage($packageNumber)
    {
        foreach ($this->getPackages() as $package) {
            if ($package->package_id == $packageNumber) {
                return $package;
            }
        }

        return null;
    }

    /**
     * @return Package[]
     */
    public function getPackages(): array
    {
        $data = $this->getData();

        $packages = [];

        if (!empty($data["packages"])) {
            foreach ($data["packages"] as $packageData) {
                if (!empty($this->sdc_vouchers)) {
                    $sdc_vouchers = Json::decode($this->sdc_vouchers);
                    foreach ($sdc_vouchers as $sdc_voucher) {
                        if ($sdc_voucher['packageId'] == $packageData['packageId']) {
                            $packageData['sdc_voucher'] = $sdc_voucher;
                        }
                    }
                }
                $package = new Package;
                $package->loadData($packageData);
                $packages[] = $package;
            }
        }

        return $packages;
    }

    public function getData()
    {
        return Json::decode($this->data);
    }

    public function canCancel()
    {
        $categories = ArrayHelper::getColumn($this->getPackages(), 'category');

        $canBeCanselled = in_array(TrShows::TYPE, $categories, false) || in_array(TrAttractions::TYPE, $categories, false) ||
            $this->getVacationPackages();

        $user = User::getCurrentUser();

        $limitTime = null;
        foreach ($this->getPackages() as $package) {

            if (!$package->canCancel()) {
                return false;
            }
            $item = $package->getItem();

            if ($item && $item->external_service == $item::EXTERNAL_SERVICE_SDC) {
                return false;
            }

            if ($package->category === TrShows::TYPE) {
                $_limitTime = $package->getStartDataTime();
            } else {
                $_limitTime = $package->getEndDataTime();
            }
            if ($limitTime === null || $limitTime > $_limitTime) {
                $limitTime = $_limitTime;
            }
        }

        foreach ($this->getVacationPackages() as $VacationPackage) {
            foreach ($VacationPackage->getPackages() as $package) {

                if ($package->category === TrShows::TYPE) {
                    $_limitTime = $package->getStartDataTime();
                } else {
                    $_limitTime = $package->getEndDataTime();
                }
                if ($limitTime === null || $limitTime > $_limitTime) {
                    $limitTime = $_limitTime;
                }
            }
        }

        return $canBeCanselled && $user['tripium_id'] == $this->getData()['customer']['id']
            && $this->getData()["status"] != self::STATUS_CANCELLED && $limitTime->format('U') > time();
    }

    /**
     * Cancel order
     * @return boolean
     */
    public function cancel(): bool
    {
        $return = true;

        if (!empty($this->getVacationPackages())) {
            foreach ($this->getVacationPackages() as $vacationPackage) {
                if (!$vacationPackage->cancelled && !$this->cancelVacationPackage($vacationPackage->id)) {
                    $return = false;
                }
            }
        }

        if ($return) {
            $Tripium = new Tripium;
            $Tripium->timeout = 120;
            $Tripium->orderCancel($this->order_number);

            if ($Tripium->errors) {
                $this->addErrors($Tripium->errors);
                $return = false;
            }
        }
        return $return;
    }

    /**
     * Cancel all similar vacation packages
     * @param int $vacationPackageId
     * @return boolean
     */
    public function cancelUniqueVacationPackage($vacationPackageId)
    {
        $return = true;

        $VacationPackages = null;
        if (!empty($this->getGroupVacationPackages()[$this->getGroupHashVacationPackageById($vacationPackageId)])) {
            $VacationPackages = $this->getGroupVacationPackages()[$this->getGroupHashVacationPackageById($vacationPackageId)];
        } else {
            $return = false;
        }

        if ($VacationPackages) {
            foreach ($VacationPackages as $vacationPackage) {
                if (!$vacationPackage->cancelled && !$this->cancelVacationPackage($vacationPackage->id)) {
                    $return = false;
                }
            }
        }
        return $return;
    }

    /**
     * Cancel Vacation Package
     * @param int $vacationPackageId
     * @return boolean
     */
    public function cancelVacationPackage($vacationPackageId)
    {
        if ($vacationPackage = $this->getVacationPackage($vacationPackageId)) {
            $Tripium = new Tripium;
            $Tripium->timeout = 120;
            $Tripium->cancelVacationPackage($this->order_number, $vacationPackage->id);
            if ($Tripium->errors) {
                $this->addErrors($Tripium->errors);
                return false;
            }

            return true;
        }

        $this->addErrors(["Vacation Package $vacationPackageId hasn't found"]);
        return false;
    }

    /**
     * Cancel Package
     *
     * @param $packageNumber
     *
     * @return boolean
     */
    public function cancelPackage($packageNumber)
    {
        if ($package = $this->getPackage($packageNumber)) {
            $Tripium = new Tripium;
            $Tripium->timeout = 120;
            $Tripium->orderCancelPackage($this->order_number, $packageNumber);
            if ($Tripium->errors) {
                $this->addErrors($Tripium->errors);
                return false;
            }

            return true;
        }

        $this->addErrors(["Package $packageNumber hasn't found"]);
        return false;
    }

    public function getPackageById($packageId)
    {
        foreach ($this->getPackages() as $package) {
            if ($package->package_id == $packageId) {
                return $package;
            }
        }
        return null;
    }

    /**
     * Return valid packages
     *
     * @return Package[]
     */
    public function getValidPackages()
    {
        $packages = [];
        foreach ($this->getPackages() as $package) {
            if (!$package->cancelled) {
                $packages[] = $package;
            }
        }
        return $packages;
    }

    public function isCallUsToBook()
    {
        foreach ($this->getPackages() as $package) {
            if (isset($package->getItem()->call_us_to_book) && $package->getItem()->call_us_to_book) {
                return true;
            }
        }

        return false;
    }

    public function getCoupon()
    {
        $data = $this->getData();

        if (empty($data['discountCode'])) {
            return null;
        }

        $Coupon = new Coupon();
        $Coupon->loadData(ArrayHelper::merge($data['discountCode'], ['discounted_total'=>$this->getFullTotal()]));

        if ($Coupon->code && $Coupon->discount > 0) {
            return $Coupon;
        }
        return null;
    }

    public function getDataByKey($key)
    {
        $data = $this->getData();

        return $data[$key] ?? null;
    }

    public function getCustomerData()
    {
        return $this->getDataByKey("customer");
    }

    public function getStatus()
    {
        return $this->getDataByKey("status");
    }

    public function getStatusClass()
    {
        $statusClass = 'warning';
        if ($this->getStatus() === self::STATUS_CONFIRMED) {
            $statusClass = 'success';
        }
        if ($this->getStatus() === self::STATUS_CANCELLED) {
            $statusClass = 'danger';
        }
        return $statusClass;
    }

    public function getFullTotal(): float
    {
        return round($this->getDataByKey("fullTotal"), 2);
    }

    public function getFormatFullTotal(): string
    {
        return number_format($this->getFullTotal(), 2, '.', '');
    }

    public function getFullDiscount()
    {
        return $this->getDataByKey("fullDiscount");
    }

    public function getProcessingFee()
    {
        return $this->getDataByKey("processingFee");
    }

    public function getFullCancellationFee()
    {
        return $this->getDataByKey("fullCancellationFee");
    }

    public function getServiceFee()
    {
        $total = 0;
        foreach ($this->getPackages() as $package) {
            $total += $package->serviceFee;
        }
        return $total;
    }

    public function getFullTax()
    {
        return $this->getDataByKey("fullTax");
    }

    public function getValidSubTotal()
    {
        $subTotal = 0;
        foreach ($this->getValidPackages() as $package) {
            $subTotal += $package->total;
        }
        foreach ($this->getValidUniqueVacationPackages() as $vacationPackage) {
            $subTotal += $vacationPackage->total;
        }
        return $subTotal;
    }

    /**
     * Get user name.
     *
     * @return string
     */
    public function getUserFullName()
    {
        $user = User::getCurrentUser();
        $userFullName = $user !== null ? trim($user->first_name . " " . $user->last_name) : '';
        return empty($userFullName) ? $this->getCustomerFullName() : $userFullName;
    }

    /**
     * Get customer name.
     *
     * @return string
     */
    public function getCustomerFullName(): string
    {
        return trim($this->getData()['customer']['firstName'] . ' ' . $this->getData()['customer']['lastName']);
    }

    /**
     * Get tickets count by group data
     *
     * @return array
     */
    public function getValidPackagesByGroupData()
    {
    	$itemsByCategory = [];
        foreach ($this->getValidVacationPackages() as $vacationPackage) {
            foreach ($vacationPackage->getPackages() as $package) {
                $itemsByCategory[$package->getHashData()][] = $package;
            }
        }
        foreach ($this->getValidPackages() as $package) {
            $itemsByCategory[$package->getHashData()][] = $package;
        }

        return $itemsByCategory;
    }

    /**
     * Get tickets count by group data
     *
     * @return array
     */
    public function getValidTicketsCountByGroupData()
    {
        $ticketCount = [];
        foreach ($this->getValidPackagesByGroupData() as $packages) {
            foreach ($packages as $package) {
                foreach ($package->tickets as $ticket) {
                    if (empty($ticketCount[$package->getHashData()][$ticket->getHashData()])) {
                        $ticketCount[$package->getHashData()][$ticket->getHashData()] = $ticket->qty;
                    } else {
                        $ticketCount[$package->getHashData()][$ticket->getHashData()] += $ticket->qty;
                    }
                }
            }
        }
        return $ticketCount;
    }

    /**
     * Get tickets by group data
     *
     * @return array
     */
    public function getValidTicketsByGroupData()
    {
        $tickets = [];
        foreach ($this->getValidPackagesByGroupData() as $packages) {
            foreach ($packages as $package) {
                foreach ($package->tickets as $ticket) {
                    $tickets[$package->getHashData()][$ticket->getHashData()] = $ticket;
                }
            }
        }
        return $tickets;
    }

    /**
     * Return VP with has $item
     *
     * @param Package $package
     *
     * @return VacationPackageOrder[]
     */
    public function getVacationPackagesByPackage(Package $package)
    {
        $VacationPackages = [];
        foreach ($this->getUniqueVacationPackages() as $vacationPackage) {
            foreach ($vacationPackage->getPackages() as $_package) {
                if ($_package->getHashData() == $package->getHashData()) {
                    $VacationPackages[$this->getGroupHashVacationPackageById($vacationPackage->id)] = $vacationPackage;
                }
            }
        }
        return $VacationPackages;
    }

    /**
     * Return cancellation text of VP with has $item
     *
     * @param Package $package
     *
     * @return string
     */
    public function cancellationTextOfVacationPackagesByPackage(Package $package)
    {
        foreach ($this->getVacationPackagesByPackage($package) as $vacationPackage) {
            if (!empty($vacationPackage->cancellation_text)) {
                return $vacationPackage->cancellation_text;
            }
        }
        return '';
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getCreatedAt()
    {
        return new DateTime($this->created_at);
    }

    /**
     * @return float
     */
    public function getResortFee(): float
    {
        $total = 0;
        foreach ($this->getPackages() as $package) {
            if (!empty($package->priceLine->displayPropertyFee)) {
                $total += $package->priceLine->displayPropertyFee;
            }
        }
        return $total;
    }

    /**
     * @return DateTime
     */
    public function getCreatedDate(): DateTime
    {
        return self::getCreated($this->getData()['created'] / 1000);
    }

    /**
     * @param $created
     *
     * @return DateTime
     */
    public static function getCreated($created): DateTime
    {
        return (new DateTime())->setTimestamp((int)$created);
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getUpdatedDate(): DateTime
    {
        return (new DateTime($this->updated_at));
    }
}

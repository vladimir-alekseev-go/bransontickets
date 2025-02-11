<?php

namespace common\models;

use common\analytics\Analytics;
use common\helpers\General;
use common\helpers\MarketingItemHelper;
use common\models\form\HotelReserveForm;
use common\tripium\Tripium;
use DateInterval;
use DateTime;
use Exception;
use Throwable;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\StaleObjectException;
use yii\helpers\Json;
use yii\helpers\Url;

class TrBasket extends _source_TrBasket
{
    use VacationPackagesTrait;

    public const FAMILY_PASS = 'FAMILY PASS';

    public const SCENARIO_RESERVATION = 'reservation';
    public const SCENARIO_PAYMENT = 'payment';

    public const ACCEPT_TERMS_YES = 1;
    public const ACCEPT_TERMS_NO = 0;

    public $needResetCoupon = true;

    public $errors = [];
    public $warnings = [];
    public $messages = [];
    public $removeHotelBeforeAddNew = false;

    private $total;

    /**
     * @var Tripium $tripium
     */
    public $tripium;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
            'timestampReservation' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'reserve_at',
                ],
                'value' => static function ($event) {
                    if ($event->sender->scenario === self::SCENARIO_RESERVATION) {
                        return new Expression('NOW()');
                    }

                    return $event->sender->reserve_at;
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                ['accept_terms', 'required', 'on' => self::SCENARIO_RESERVATION],
                ['accept_terms', 'ruleAcceptTerms', 'on' => self::SCENARIO_RESERVATION],
                ['accept_terms', 'required', 'on' => self::SCENARIO_PAYMENT],
                ['accept_terms', 'ruleAcceptTerms', 'on' => self::SCENARIO_PAYMENT],
            ]
        );
    }

    public function ruleAcceptTerms($attribute, $params)
    {
        if ((int)$this->{$attribute} !== self::ACCEPT_TERMS_YES) {
            $this->addError($attribute, 'Need to accept terms');
        }
    }

    /**
     * @return Itinerary|null
     */
    public function reserve(): ?Itinerary
    {
        $this->scenario = self::SCENARIO_RESERVATION;
        $this->needResetCoupon = false;

        if (!$this->validate()) {
            return null;
        }

        $total_before = $this->getItinerary()->getTotalCount();

        $this->tripium = new Tripium();
        $itinerary = $this->tripium->reserve($this->session_id);

        if (!empty($this->tripium->getFirstErrors())) {
            $this->addError('reserve', $this->tripium->getFirstErrors()[0]);
            return null;
        }

//        $data = $this->get(true);

        if ($itinerary === null) {
            return null;
        }

        $total_after = $itinerary->getTotalCount();

        if ($total_before < $total_after) {
            $this->warnings[] = 'Oops... the price of the items in your itinerary has changed since your last search. It is now $'.number_format($total_after, 2, ',', '').'. ';
        } else if ($total_before > $total_after) {
            $this->messages[] = 'Great news - we were able to find even lower prices for you itinerary! It is now $'.number_format($total_after, 2, ',', '').'.';
        }

        $this->data = Json::encode($itinerary->getData()[Itinerary::KEY_ITINERARY]);
        $this->save();

//        $this->reset();

//        $res = $this->get();
        $categories = [];
        foreach ($itinerary->getPackages() as $package) {
            $categories[$package->category] = $package->category;
        }

        $MarketingItem = MarketingItemHelper::getItemClassNames();

        foreach ($categories as $category) {
            $class = $MarketingItem[$category];

            if (defined($class. '::priceClass')) {
                $cl = $class::priceClass;
                (new $cl)->updateFromTripium();
            }
        }

        return $itinerary;
    }

    /**
     * Return Last Update Item Url.
     *
     * @return string|null
     */
    public function getLastUpdateItemUrl(): ?string
    {
        $package = $this->getLastUpdatePackage();
        if ($package && $package->getItem()) {
            return $package->getItem()->getUrl();
        }
        return null;
    }

    /**
     * Return Last Update Package.
     *
     * @return Package|null
     */
    public function getLastUpdatePackage(): ?Package
    {
        $packages = $this->getPackages();

        if (empty($packages)) {
            return null;
        }

        uasort(
            $packages,
            static function ($package1, $package2) {
                /**
                 * @var Package $package1
                 * @var Package $package2
                 */
                if ($package1->dateUpdated === $package2->dateUpdated) {
                    return 0;
                }
                return ($package1->dateUpdated > $package2->dateUpdated) ? -1 : 1;
            }
        );

        return array_shift($packages);
    }

    /**
     * Return packages
     *
     * @return Package[]
     */
    public function getPackages(): array
    {
        $data = $this->getData();

        $packages = [];

        if (!empty($data["packages"])) {
            foreach ($data["packages"] as $packageData) {
                $package = new Package();
                $package->loadData($packageData);
                $packages[] = $package;
            }
        }

        return $packages;
    }

    /**
     * Return Package by packageId
     *
     * @param string $packageId
     *
     * @return Package|null
     */
    public function getPackage($packageId): ?Package
    {
        if (!empty($this->getPackages())) {
            foreach ($this->getPackages() as $package) {
                if ($package->package_id == $packageId) {
                    return $package;
                    break;
                }
            }
        }

        return null;
    }

    /**
     * Return Data of cart
     *
     * @return array
     */
    public function getData()
    {
        return Json::decode($this->data);
    }

    public function getItinerary(): Itinerary
    {
        return (new Itinerary())->loadData([Itinerary::KEY_ITINERARY => Json::decode($this->data)]);
    }

    /**
     * Set up coupon
     *
     * @param Coupon $Coupon
     *
     * @return bool|int
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function setCoupon($Coupon)
    {
        return false;
//        $type = Yii::$app->params['siteType'] === 'mobile' ? Coupon::COUPON_TYPE_MOBILE : Coupon::COUPON_TYPE_DESKTOP;
//
//        if ($Coupon instanceof Coupon && !empty($Coupon->code) && $Coupon->isTypeOf($type)) {
//            $tripium = new Tripium;
//            $itinerary = $tripium->getItineraryByCoupon($this->session_id, $Coupon);
//            if (!$itinerary) {
//                return false;
//            }
//            $this->progressResult($itinerary, $AnalyticsData);
//        } else {
//            $this->data = Json::encode($this->get(true));
//            $this->coupon_data = null;
//            $this->update(false);
//        }
//
//        return true;
    }

    /**
     * Get coupon
     *
     * @return Coupon|null
     */
    public function getCoupon(): ?Coupon
    {
        if (empty($this->coupon_data)) {
            return null;
        }

        $Coupon = new Coupon;
        $Coupon->loadData(Json::decode($this->coupon_data));

        $type = Yii::$app->params['siteType'] === 'mobile' ? Coupon::COUPON_TYPE_MOBILE : Coupon::COUPON_TYPE_DESKTOP;
        if ($Coupon->isTypeOf($type)) {
            return $Coupon;
        }
        return null;
    }

    /**
     * Return original data (without coupon)
     * @return null|array
     */
    public function getOriginal(): ?array
    {
        if (!empty($this->getData()['originalData'])) {
            return $this->getData()['originalData'];
        }

        return null;
    }

    /**
     * Return discount by price
     *
     * @return double
     */
    public function getDiscountByPrice()
    {
        $discount = 0;

        if (empty($this->getOriginal())) {
            return $discount;
        }

        $packages = $this->getOriginal()["packages"];
        if (!empty($packages)) {
            foreach ($packages as $packageData) {
                $package = new Package();
                $package->loadData($packageData);
                foreach ($package->getTickets() as $ticket) {
                    if ($ticket->retail_rate != $ticket->special_rate && $ticket->special_rate) {
                        $discount += ($ticket->retail_rate - $ticket->special_rate) * $ticket->qty;
                    }
                }
            }
        }

        return $discount;
    }

    /**
     * Can display form for adding Coupon
     *
     * @return bool
     */
    public function showCouponForm(): bool
    {
        return !empty($this->getPackages());
    }

    /**
     * Return all packages
     * @return Package[]
     */
    public function getAllPackages(): array
    {
        $packages = [];
        foreach ($this->getUniqueVacationPackages() as $uniqueHash => $vacationPackage) {
            if (!empty($vacationPackage->packages)) {
                $p = array_merge($packages, $vacationPackage->packages);
                $packages = $p;
            }
        }
        if (!empty($this->packages)) {
            $packages = array_merge($packages, $this->packages);
        }
        return $packages;
    }

    /**
     * Return saved amount
     *
     * @return float
     */
    public function getSaved()
    {
        $saved = 0;
        foreach ($this->getPackages() as $package) {
            foreach ($package->getTickets() as $ticket) {
                $saved += $ticket->getSaved() * $ticket->qty;
            }
        }
        return $saved;
    }

    /**
     * Return Service Fee Total
     *
     * @return float
     */
    public function getServiceFee()
    {
        $total = 0;
        foreach ($this->getPackages() as $package) {
            $total += $package->serviceFee;
        }
        return $total;
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
     * @return string|null
     */
    public function hasHotel(): ?string
    {
        foreach ($this->getPackages() as $package) {
            if ($package->category === TrPosHotels::TYPE) {
                return $package->package_id;
            }
        }
        return null;
    }

    /**
     * @param array $roomType
     *
     * @return bool
     */
    public function hasRoomType(array $roomType): bool
    {
        foreach ($this->getPackages() as $package) {
            foreach ($package->getTickets() as $ticket) {
                if ($ticket->getRoomId() === $roomType['id']
                    && $ticket->getRoomCapacity() === (int)$roomType['capacity']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $packageId
     * @param bool   $addEvent
     *
     * @return bool
     */
    public function removePackage($packageId, $addEvent = true): bool
    {
        $this->sessionIsCreated();

        $tripium = new Tripium();

        $package = $this->getPackage($packageId);

        $itinerary = $tripium->removePackage($this->getAttribute('session_id'), $packageId);

        if ($itinerary) {
            $this->setFullData($itinerary);
        } else if (!empty($tripium->getFirstErrors())) {
            $this->addError('removePackage', $tripium->getFirstErrors()[0]);
            return false;
        }

        if ($addEvent) {
            Analytics::addEvent(Analytics::EVENT_REMOVEFROMCART, ['package' => $package]);
        }

        return true;
    }

    /**
     * Clear the cart
     */
    public function removeAll(): void
    {
        $itinerary = $this->getItinerary();

        if (!empty($itinerary->getPackages())) {
            $AnalyticsData = [];
            foreach ($itinerary->getPackages() as $package) {
                $AnalyticsData[] = ['package' => $package];
            }
            Analytics::addEvent(Analytics::EVENT_REMOVEFROMCART, $AnalyticsData);
        }

        $tripium = new Tripium();
        $itinerary = $tripium->clearItinerary($this->getAttribute('session_id'));
        $this->setAttributes(
            [
                'reserve_at' => null,
            ]
        );
        if ($itinerary) {
            $this->setFullData($itinerary);
        } else if (!empty($tripium->getFirstErrors())) {
            $this->addError(
                'clear',
                $tripium->getFirstErrors()[0]
            );
        }
    }

    /**
     * Get saved tripium session id
     *
     * @param bool $withoutUser
     *
     * @return bool|mixed|null
     */
    public static function getSessionID($withoutUser = false)
    {
        $tripiumBasketId_session = Yii::$app->session->get("tripium_basket_id");
        $tripiumBasketId_cookie = false;//$_COOKIE['tripium_basket_id'];

        $session_id = false;
        if (!$withoutUser && !Yii::$app->user->isGuest) {
            $u = User::getCurrentUser();
            $b = self::find()->where(["user_id"=>$u->id])->one();
            if ($b) {
                $session_id = $b->session_id;
            }
        }

        if (!$session_id && $tripiumBasketId_cookie) {
            $session_id = $tripiumBasketId_cookie;
        }

        if (!$session_id && $tripiumBasketId_session) {
            $session_id = $tripiumBasketId_session;
        }

        if ($session_id && !self::find()->where(["session_id" => $session_id])->one()) {
            self::removeSessionID($session_id);
            $session_id = false;
        }

        return $session_id;
    }

    /**
     * @return Itinerary|null
     */
    public static function createSessionID(): ?Itinerary
    {
        $tripium = new Tripium();
        $itinerary = $tripium->postItinerary(['packages' => []]);

        if (!empty($tripium->getFirstErrors())) {
//            $this->addError('create_session_id', $tripium->getFirstErrors()[0]);
            return null;
        }

        if ($itinerary && $itinerary->session) {
            self::setSessionId($itinerary->session);

            $user_id = null;
            if (!Yii::$app->user->isGuest) {
                $u = User::getCurrentUser();
                $user_id = $u ? $u->id : null;
            }

            $b = self::find()->where(["or", ["user_id" => $user_id, "session_id" => $itinerary->session]])->one();
            if ($b) {
                if ($user_id) {
                    $b->user_id = $user_id;
                }
                $b->save();
            } else {
                $b = new self;
                $b->setAttributes(
                    [
                        "session_id" => $itinerary->session,
                        "user_id" => $user_id
                    ]
                );
                $b->save();
            }

            return $itinerary;
        }

        return null;
    }

    public function getQRCodeUrl(): string
    {
        $url = 'https://' . Yii::$app->params['domain'];
        return $url . Url::to(
                [
                    'order/kiosk-payment',
                    'sessionId' => $this->session_id,
                    'hash' => self::getHashSessionId($this->session_id)
                ]
            );
    }

    public static function getHashSessionId($session_id): string
    {
        return hash('sha256',Yii::$app->params['kiosk-server-key'] . $session_id);
    }

    public static function setSessionId($sessionId): void
    {
        Yii::$app->session->set("tripium_basket_id", $sessionId);
    }

    public static function removeSessionID($session_id = null, $for_user = true): void
    {
        if (!$session_id) {
            $session_id = self::getSessionID();
        }
        setcookie("tripium_basket_id", "", time() - 999999, "/");
        Yii::$app->session->remove("tripium_basket_id");
        if ($session_id && $for_user) {
            self::deleteAll(["session_id" => $session_id]);
        }
    }

    /**
     * @param bool $force
     *
     * @return array|null
     */
    public function get($force = false): ?Itinerary
    {
        $this->sessionIsCreated();

        if ($force) {
            $tripium = new Tripium;
            $itinerary = $tripium->getItinerary($this->getAttribute('session_id'));

            if (!empty($tripium->getFirstErrors())) {
                $this->addError('reserve', $tripium->getFirstErrors()[0]);
                return null;
            }
            return $itinerary;
        }
        return $this->getItinerary();
//        } else {
//            $res = self::find()->where(["session_id"=>$this->getAttribute('session_id')])->one();
//            if ($res) {
//                $data = Json::decode($res->data);
//                $data["db"] = $res;
//                return $data;
//            }
//
//            return $this->getItinerary();
//        }
//
//        return $res;
    }

    private function sessionIsCreated(): bool
    {
        if (!empty($this->getAttribute('session_id'))) {
            return true;
        }
        $this->addError(
            'server',
            'The server is temporarily unavailable, please try again later or please call us at ' .
            General::getConfigPhoneNumber()
        );

        try {
            Yii::$app->mailer->compose('basket/session-is-not-created', ['basket' => $this])
                ->setFrom(Yii::$app->params['subscriptionEmailFrom'])
                ->setTo(Yii::$app->params['technicalNotificationEmail'])
                ->setSubject('Adding/modify items in a basket. ' . Yii::$app->name)
                ->send();
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * Add tickets
     *
     * @param OrderForm|HotelReserveForm $OrderForm
     *
     * @return bool
     */
    public function set($OrderForm): bool
    {
        $this->sessionIsCreated();

        $this->setAttribute('accept_terms', self::ACCEPT_TERMS_NO);

        $this->tripium = new Tripium;
        $AnalyticsData = [];
        $request = null;

        if ($OrderForm instanceof HotelReserveForm) {
            $itinerary = $this->tripium->postPackage(
                $this->getAttribute('session_id'),  $OrderForm->requestAddToBasket($this->get())
            );

            if ($itinerary) {
                $this->progressResult($itinerary, $AnalyticsData);
            }

            if ($this->tripium->getErrors()) {
                $this->addError('add_to_basket', $this->tripium->getFirstError('globalErrors'));
                return false;
            }

            return true;
        }

        if ($OrderForm instanceof OrderForm && empty($OrderForm->prices)) {
            $this->addError('prices', 'Prices are absent');
            return false;
        }

        if ($OrderForm instanceof OrderForm &&
            ($OrderForm->model instanceof TrShows
                || $OrderForm->model instanceof TrAttractions)
        ) {
            $request = $OrderForm->requestAddToBasket($this->get());
        }

        if (!empty($request)) {

            if ($OrderForm->count > 0) {

                $itinerary = $this->tripium->postPackage($this->getAttribute('session_id'), $request, $this->getCoupon());

                if ($this->tripium->errorCode === Tripium::STATUS_ONE_HOTEL_PER_ORDER
                    && !empty($this->tripium->getFirstErrors())) {
                    $this->addError(
                        'add_to_basket',
                        $this->tripium->getFirstErrors()[0] . ' <a href="' . Url::to(['/order/cart']) . '">Cart</a>'
                    );
                } else if (!empty($this->tripium->getFirstErrors())) {
                    $this->addError('add_to_basket', $this->tripium->getFirstErrors()[0]);
                } else if($itinerary !== null) {
                    $this->progressResult($itinerary, $AnalyticsData);
                }
            } else {
                $this->addError('request', 'Request is wrong');
            }
        }

        Analytics::addEvent(Analytics::EVENT_ADDTOCART, $AnalyticsData, ['list' => 'all']);

        if ($this->getErrors()) {
            return false;
        }

        return true;
    }

    private function progressResult(Itinerary $itinerary, &$AnalyticsData): void
    {
        $packageIds = [];
        foreach ($itinerary->getPackages() as $package) {
            $packageIds[] = $package->package_id;
        }
        foreach ($itinerary->getPackages() as $package) {
            if (!in_array($package->package_id, $packageIds, true)) {
                $AnalyticsData[] = ['package' => $package];
            }
        }
        $this->setFullData($itinerary);
    }

    /**
     * @param Itinerary $itinerary
     *
     * @deprecated refactor
     * @return bool
     */
    private function setFullData($itinerary): bool
    {
        if ($itinerary) {
            $result = $itinerary->getData();
            if (empty($result['itinerary'])) {
                return false;
            }
        } else {
            return false;
        }

//        $autoCodeItinerary = !empty($result['autoCodeItinerary'])
//            ? self::calculateParams($result['autoCodeItinerary']) : null;
        $autoCodeItinerary = null;

        $data = !empty($autoCodeItinerary) ? $autoCodeItinerary : $itinerary->getItineraryData();
        $data['originalData'] = !empty($autoCodeItinerary) ? $itinerary->getItineraryData() : null;
        if (!empty($data['originalData']['originalData'])) {
            unset($data['originalData']['originalData']);
        }

        $coupon = null;
        if (!empty($result['appliedCode'])) {
            $coupon = new Coupon;
            $coupon->loadData($result['appliedCode']);
        }

        $this->setAttributes(
            [
                'session_id' => $itinerary['session'],
                'data' => Json::encode($data),
                'coupon_data' => $coupon ? Json::encode($coupon->getAttributes($coupon->fields())) : null,
            ]
        );

        self::setSessionId($itinerary['session']);

        return $this->save();
    }
//
//    /**
//     * @param Itinerary $itinerary
//     *
//     * @return mixed
//     */
//    public static function calculateParams($itinerary)
//    {
//        $data = $itinerary->getData()['itinerary'];
//        $total_count = 0;
//        if (!empty($data['packages'])) {
//            foreach ($data['packages'] as &$package) {
//                if ($package['category'] === TrPosHotels::TYPE) {
//                    $total_count++;
//                    continue;
//                }
//                if ($package['category'] === TrPosPlHotels::TYPE) {
//                    $total_count += count($package['tickets']);
//                }
//                foreach ($package['tickets'] as &$ticket) {
//                    if ($package['category'] !== TrPosPlHotels::TYPE) {
//                        $total_count += $ticket['qty'];
//                    }
//                    $ticket['resultRate'] = number_format($ticket['specialRate'] ?: $ticket['retailRate'], 2, '.', '');
//                }
//            }
//            unset($package, $ticket);
//        }
//        $data['total_count'] = $total_count;
//        $data['total'] = number_format($data['total'], 2, '.', '');
//
//        var_dump($data);exit();
//        return $data;
//    }
    /**
     * @return Package[]
     */
    public function getHotelsRooms(): array
    {
        $packages = [];
        foreach ($this->getPackages() as $package) {
            if ($package->getItem()::TYPE === TrPosHotels::TYPE) {
                $packages[] = $package;
            }
        }
        return $packages;
    }

    /**
     * @return false|int
     * @throws Throwable
     */
    protected function removeCoupon()
    {
        try {
            $this->coupon_data = null;
            return $this->update(false);
        } catch (StaleObjectException $e) {
            return false;
        }
    }

    public function setForUser($user_id): bool
    {
        $session_id = self::getSessionID(true);

        if (!$session_id) {
            return false;
        }

        $b = self::find()->where(["session_id" => $session_id, "user_id" => null])->one();

        if ($b) {
            self::deleteAll("user_id = " . $user_id);
            $b->user_id = $user_id;
            $b->save();
        }
        return true;
    }

    /**
     * Purchase
     *
     * @see https://docs.google.com/spreadsheets/d/1X0UDV1fmyGNn3rgmq3nyRDrmn3r_ij8Da8l7rbJYo7w/edit#gid=1396088170
     *
     * @param $customer_id
     * @param $service
     *
     * @return bool|mixed|string[]
     * @throws Exception
     */
    public function order($customer_id, $service)
    {
        $this->sessionIsCreated();

        $Tripium = new Tripium;
        $userTripium = $Tripium->getCustomer($customer_id);

        if (empty($userTripium) && $Tripium->statusCode == Tripium::STATUS_NOT_ACCEPTABLE) {
            $user = User::find()->where(["tripium_id" => $customer_id])->one();

            if ($user) {
                $user->tripium_id = null;
                $user->save();
                $Tripium = new Tripium;
                $userTripium = $Tripium->getCustomer($user->tripium_id);
                $customer_id = $user->tripium_id;
            }
        }

        $data = [
            "customer" => $customer_id,
            "session" => $this->getAttribute('session_id'),
            "info" => !empty(Yii::$app->params["tripium_info"]) ? Yii::$app->params["tripium_info"] : '',
            "voucherName" => $userTripium["firstName"] . " " . $userTripium["lastName"],
            "transactions" => [
                [
                    "paymentMethod" => "online credit card",
                    "amount" => $this->getFullTotal(),
                    "service" => $service,
                ]
            ],
            "subscribe" => true,
        ];

        if ($this->getCoupon()) {
            $data['transactions'][] = [
                "paymentMethod" => "Discount Code",
                "discountCode" => $this->getCoupon()->code,
                "amount" => $this->getCoupon()->discount
            ];
        }
        $tripium = new Tripium;
        $tripium->timeout = 300;
        $order = $tripium->addOrder($data);

        if (!empty($tripium->errors)) {
            if ($tripium->errorCode === Tripium::CUSTOMER_WAS_NOT_FOUND) {
                $Customer = new Custumer();
                $res = $Customer->reCreate();

                if ($res) {
                    $data["customer"] = $res["id"];
                    $tripium = new Tripium;
                    $tripium->timeout = 300;
                    $order = $tripium->addOrder($data);

                    if ($tripium->errors) {
                        $this->addErrors($tripium->errors);
                        return false;
                    }
                } else {
                    $this->addError('reserve', "Create customer:" . $Customer->getErrors()["errors"][0]);
                    return false;
                }
            } else {
                $this->addErrors($tripium->errors);
                return false;
            }
        } else {
            $AnalyticsData = [];

            if ($order["packages"]) {
                foreach ($order["packages"] as $package) {
                    $AnalyticsData[] = ['package' => $package];
                }
            }

            Analytics::addEvent(
                Analytics::EVENT_PURCHASE,
                $AnalyticsData,
                [
                    'id' => $order["orderNumber"],
                    'affiliation' => Yii::$app->name,
                    'revenue' => number_format($order["fullTotal"], 2, '.', ''),
                    'tax' => number_format($order["fullTax"], 2, '.', ''),
                ]
            );

            $MarketingItem = MarketingItemHelper::getItemClassNames();

            if (!empty($order['packages'])) {
                foreach ($order['packages'] as $package) {
                    if (!empty($MarketingItem[$package['category']])) {
                        $class = $MarketingItem[$package['category']];
                        if (defined($class . '::priceClass')) {
                            $classPrice = $class::priceClass;
                            (new $classPrice)->updateFromTripium(
                                [
                                    "start" => $package['date'],
                                    "end" => $package['date']
                                ]
                            );
                        }
                    }
                }
            }
            $TrOrders = TrOrders::build($order);
            $TrOrders->save();
            $TrOrders->refresh();
            $TrOrders->updateByTripium(true);
        }

        self::removeSessionID();

        return $order;
    }

    /**
     * Cleaning old added item to cart
     */
    public static function removeOld(): void
    {
        /**
         * @var Basket[] $baskets
         */
        $date = new DateTime;
        $date->sub(new DateInterval('P60D'));
        self::deleteAll(['<', 'updated_at', $date->format('Y-m-d H:i:s')]);
        $baskets = self::find()->all();
        foreach ($baskets as $basket) {
            foreach ($basket->getPackages() as $package) {
                if ((!$package->isAnyTime && $package->getStartDataTime() < new DateTime())
                    || ($package->isAnyTime
                        && $package->getStartDataTime() < (new DateTime())->sub(new DateInterval('P1D'))
                    )) {
                    try {
                        $basket->delete();
                    } catch (StaleObjectException $e) {
                    } catch (Throwable $e) {
                    }
                }
            }
        }
    }

    public function getTotal()
    {
        return $this->getData() ? $this->getData()['total'] : null;
    }

    /**
     * @param bool   $create Create a basket in Tripium if it is absent in the session.
     *
     * @return TrBasket
     */
    public static function build($create = false): TrBasket
    {
        if (($create && (!self::getSessionID())) || (!self::getSessionID())) {
            self::createSessionID();
        }
        if (!self::getSessionID()) {
            return new self;
        }

        /**
         * @var TrBasket $basket
         */
        $basket = self::find()->where(['session_id' => self::getSessionID()])->one();
        return $basket;
    }

    public function getFullTotal()
    {
        $fullTotal = $this->getData() ? $this->getData()['fullTotal'] : null;
        $coupon = $this->getCoupon();
        return $coupon->discounted_total ?? $fullTotal;
    }

    public function getTotalCount()
    {
        $count = $this->getItinerary()->getTotalCount();

        $VacationPackages = $this->getVacationPackages();
        if ($VacationPackages) {
            foreach ($VacationPackages as $VacationPackage) {
                $count += $VacationPackage->ticketsCount;
            }
        }

        return $count;
    }

    public function getTax()
    {
        $tax = 0;
        foreach ($this->getPackages() as $package) {
            $tax += $package->tax;
        }

        $VacationPackages = $this->getVacationPackages();
        if ($VacationPackages) {
            foreach ($VacationPackages as $VacationPackage) {
                if (!empty($VacationPackage->packages)) {
                    foreach ($VacationPackage->packages as $package) {
                        $tax += $package->tax;
                    }
                }
            }
        }

        return $tax;
    }

    /**
     * @param array $data
     */
    public function reset(array $data = null): void
    {
//        if (!$data) {
//            $data = $this->get(true);
//        }
//        $data['originalData'] = $data;
//
//        $this->setAttributes(['data' => Json::encode($data)]);
//        $this->save();
//        $this->resetCoupon();
    }

    public function resetCoupon(): void
    {
        $coupon = $this->getCoupon();
        if ($coupon !== null) {
            $Tripium = new Tripium;
            $newCoupon = $Tripium->getCouponByCode($coupon->code);
            if ($newCoupon !== $coupon) {
                $this->setCoupon($newCoupon);
            }
        }

        if (!$this->needResetCoupon) {
            return;
        }

        $accessCoupons = [];
        if ($this->getCoupon() !== null) {
            $accessCoupons[] = $this->getCoupon();
        }

        $Tripium = new Tripium;
        $siteType = isset(Yii::$app->params['siteType']) && Yii::$app->params['siteType'] === Coupon::COUPON_TYPE_MOBILE ? Coupon::COUPON_TYPE_MOBILE : Coupon::COUPON_TYPE_DESKTOP;
        $coupons = $Tripium->getCoupons($this->sessionId, $siteType, true);

        if ($coupons) {
            $accessCoupons = array_merge($accessCoupons, $coupons);
            usort(
                $accessCoupons,
                static function ($a, $b) {
                    if ($a->discount === $b->discount) {
                        return 0;
                    }
                    return ($a->discount > $b->discount) ? -1 : 1;
                }
            );
            $accessCoupons = array_values($accessCoupons);
            if (($coupon === null && $accessCoupons[0]) || ($coupon && $coupon->code !== $accessCoupons[0]->code)) {
                $this->setCoupon($accessCoupons[0]);
            }
        }
    }

    /**
     * Delete Vacation Package by unique hash
     *
     * @param string $uniqueHash
     * @param bool   $addEvent
     *
     * @return bool
     */
    public function removeVacationPackage($uniqueHash, $addEvent = true)
    {
        if (!empty($this->getGroupVacationPackages()[$uniqueHash])) {
            $result = true;
            $packages = $this->getGroupVacationPackages()[$uniqueHash];
            foreach ($packages as $package) {
                $tripium = new Tripium;
                if (!$r = $tripium->deleteVacationPackages($this->session_id, $package->id)) {
                    $this->addErrors($tripium->errors);
                    $result = false;
                }
            }
            $this->reset();
            return $result;
        }

        return false;
    }
}

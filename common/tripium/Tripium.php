<?php

namespace common\tripium;

use common\helpers\General;
use common\helpers\MarketingItemHelper;
use common\helpers\StrHelper;
use common\models\Coupon;
use common\models\Itinerary;
use common\models\Package;
use common\models\TrAttractions;
use common\models\TrBasket;
use common\models\TrOrders;
use DateTime;
use Exception;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Tripium extends Model
{
    public const SITE_TYPE_DESKTOP = 'desktop';
    public const SITE_TYPE_MOBILE = 'mobile';

    public $requestData = [];
    public $errorCode;
    private $ch;
    private $token;
    private $url;
    private $globalErrors;
    private $errorData;
    private $curlInfo;

    public $statusCode;
    public $timeout = 60;

    public const CUTOFF = 422100;

    public const STATUS_ONE_HOTEL_PER_ORDER = 422008;

    public const STATUS_CODE_SUCCESS = 200;
    public const STATUS_CODE_BAD_REQUEST = 400;
    public const STATUS_CODE_ACCESS_DENIED = 403;
    public const STATUS_UNPROCESSABLE_ENTITY = 422;
    public const STATUS_NOT_ACCEPTABLE = 406;
    public const STATUS_GATEWAY_TIMEOUT = 504;

    public const ERROR_CANCELLED = 422001;
    public const ERROR_NOT_AVAILABLE = 422300;
    public const ERROR_NOT_AVAILABLE_SS = 422301;
    public const ERROR_CRUD_WITH_PAST_DATE = 422006;
    public const CUSTOMER_WAS_NOT_FOUND = 404000;
    public const ITINERARY_WAS_NOT_FOUND = 404001;
    public const ERROR_ACCESS_DENIED = 403000;
    public const ERROR_AUTH_ACCESS = 403101;
    public const ERROR_AUTH_LOCATION = 403102;
    public const ERROR_MAX_LENGTH_STAY = 422899;

    public static function getStatusList(): array
    {
        return [
            self::CUTOFF => 'CUTOFF',
            self::STATUS_CODE_SUCCESS => 'STATUS_CODE_SUCCESS',
            self::STATUS_ONE_HOTEL_PER_ORDER => 'Your shopping cart includes Hotel already. Please remove it if you want to purchase current one.',
        ];
    }

    public static function getStatusValue($val)
    {
        $ar = self::getStatusList();

        return $ar[$val] ?? $val;
    }

    public static function getRequestHotelParams()
    {
        return [
            'apiExperience' => !empty(Yii::$app->params['siteType']) && Yii::$app->params['siteType'] === 'mobile' ? 'PARTNER_MOBILE_WEB' : 'PARTNER_WEBSITE',
        ];
    }

    private function request($path, $params = [], $type = "get")
    {
        $this->clearErrors();
        $this->url = Yii::$app->params['tripium']['url'];
        $this->token = Yii::$app->params['tripium']['token'];

        $this->requestData = [
            'url' => $this->url . $path,
            'method' => $type,
            'params' => Json::encode($params),
        ];

        $headers = array();
        $headers[] = 'Authorization: ' . $this->token;
        $headers[] = 'Content-Type: application/json';

        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->ch, CURLOPT_URL, $this->url . $path);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);

        if ($type !== 'get') {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, Json::encode($params));
        }
        if ($type === 'post') {
            curl_setopt($this->ch, CURLOPT_POST, 1);
        }
        if ($type === 'put') {
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        }
        if ($type === 'delete') {
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }

        $server_output = curl_exec($this->ch);
        $this->curlInfo = curl_getinfo($this->ch);
        $this->statusCode = (int)$this->curlInfo['http_code'];
        $this->errorCode = null;
        if ($this->statusCode === 0 && $server_output === false && floor(
                $this->curlInfo['total_time']
            ) == $this->timeout) {
            $this->statusCode = self::STATUS_GATEWAY_TIMEOUT;
            $this->addError('request', 'Gateway Time-out');
        }
// 		echo '<pre>'; var_export($this->requestData); echo '</pre>';
// 		echo '<pre>'; echo $this->requestData['params']; echo '</pre>';
// 		echo "<pre>statusCode: "; var_export($this->statusCode); echo "</pre>";
// 		echo "<pre>server_output: "; var_export($server_output); echo "</pre>";
//exit();
        $phone = General::getConfigPhoneNumber();

        if ($this->statusCode !== self::STATUS_CODE_SUCCESS) {
            try {
                $res = Json::decode($server_output);
                if (!isset($res["errorCode"])
                    || (isset($res["errorCode"])
                        && !in_array(
                            (int)$res["errorCode"],
                            [self::ERROR_AUTH_LOCATION, self::ERROR_ACCESS_DENIED, self::ERROR_MAX_LENGTH_STAY],
                            true
                        ))) {
                    Yii::$app->mailer
                        ->compose(
                            "notification/tripium-request",
                            [
                                'requestData' => $this->requestData,
                                'statusCode' => $this->statusCode,
                                'serverOutput' => $server_output,
                            ]
                        )
                        ->setTo(Yii::$app->params['technicalNotificationEmail'])
                        ->setFrom([Yii::$app->params['tripiumRequestEmailFrom']])
                        ->setSubject('Tripium request ' . date('Y-m-d H:i:s') . ' ' . Yii::$app->name)
                        ->send();
                }
            } catch (Exception $e) {
            }
        }

        if ($this->statusCode === self::STATUS_CODE_SUCCESS
            || $this->statusCode === self::STATUS_UNPROCESSABLE_ENTITY) {
            $res = Json::decode($server_output);

            if (isset($res["errorCode"])) {
                $this->errorCode = (int)$res["errorCode"];
            }
            if ($this->errorCode === self::ERROR_CRUD_WITH_PAST_DATE) {
                $class = MarketingItemHelper::getItemClassNames()[$res["data"]["pkg"]['category']];
                if ($class) {
//					$item = $class::find()->where(['id_external'=>$res["data"]["pkg"]['id']])->asArray()->one();
                    if ($class::TYPE === TrAttractions::TYPE) {
                        $priceGroup = $class::priceGroup;
                        $itempriceGroup = $priceGroup::find()->where(
                            ['id_external' => $res["data"]["pkg"]['typeId']]
                        )->asArray()->one();
                    }
                }
                $res["originalErrors"] = $res["globalErrors"];
                if (!empty($itempriceGroup["name"])) {
                    $res["globalErrors"] = [
                        "Tickets " . ($itempriceGroup["name"] ? 'for ' . $itempriceGroup["name"] : '') . " are no longer available to purchase online. Please remove this item from your shopping cart to complete your order. 
				If you would like further assistance with purchasing " . $res["data"]["pkg"]["name"] . ", please call us at $phone."
                    ];
                }
            }

            if (!empty($res["errors"])) {
                $res["globalErrors"] = [$res["errorCode"]];
            }

            if (empty($res["globalErrors"]) && !empty($res["errorCode"])) {
                $res["globalErrors"] = array_merge($res["globalErrors"], [$res["errorCode"]]);
            }

            if (in_array(
                $this->errorCode,
                [
                    self::CUTOFF,
                    self::ERROR_NOT_AVAILABLE_SS,
                    self::ERROR_NOT_AVAILABLE
                ],
                true
            )) {
                $package = isset($res['data']['pkg']) ? $res['data']['pkg'] : $res['package'];
                if (!empty($package['category'])) {
                    $itemNames = MarketingItemHelper::getItemNames();
                    $itemName = isset($itemNames[$package['category']]) ? $itemNames[$package['category']] : '';
                    $res["globalErrors"] = [
                        "You are attempting to purchase tickets for " . strtolower(
                            $itemName
                        ) . " {$package['name']}, {$package['date']}, {$package['time']} within cutoff time or there are not enough tickets available. Please change your requested dates/times or call us $phone"
                    ];
                }
            }

            if (!empty($res["globalErrors"])) {
                $this->globalErrors = $res["globalErrors"];
                if (!empty($res["data"])) {
                    $this->errorData = $res["data"];
                }
            }

            if ($this->errorCode === self::ERROR_CANCELLED) {
                $res["globalErrors"][0] .= ".<br/>You could still cancel any individual item(s) that are still within cancellation period or call us $phone to assist you.";
            }

            if ($this->errorCode === self::STATUS_ONE_HOTEL_PER_ORDER) {
                $res["globalErrors"] = [self::getStatusValue(self::STATUS_ONE_HOTEL_PER_ORDER)];
            }

            if (!empty($res["globalErrors"])) {
                $this->addErrors($res["globalErrors"]);
                $this->addErrors(['globalErrors' => $res['globalErrors']]);
			}

            if (!empty($res['errors']) && is_array($res['errors'])) {
                foreach ($res['errors'] as $name => $errors) {
                    foreach ($errors as $error) {
                        $this->addErrors([$name => $error]);
                    }
                }
            }

			curl_close ($this->ch);

			return $res;

		}

//        Yii::error('request statusCode: '.$this->statusCode, 'tripium-request');
//        Yii::error('request path: '.$this->url.$path, 'tripium-request');
//        Yii::error('request type: '.$type, 'tripium-request');
//        Yii::error('request params: '.BaseJson::encode($params), 'tripium-request');
//        Yii::error('request curl_getinfo: '.BaseJson::encode(curl_getinfo($this->ch)), 'tripium-request');

        $errors = ['Server error: ' .$this->statusCode];

        if ($this->statusCode === 0) {
            $errors = ['The server is temporarily unavailable, please try again later or please call us at ' . $phone];
        }
        $this->addErrors($errors);

        curl_close($this->ch);

        return ['globalErrors' => $errors];
    }

    public function getShows($ids = null): ?array
    {
        $ids = !empty($ids) ? implode(',', $ids) : null;
        $res = $this->request('/v2/wl/shows/list?' . http_build_query(['status' => 'all', 'ids' => $ids]));
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res ? $res['results'] : null;
        }

        return null;
    }

    public function getAttractions($ids = null): ?array
    {
        $ids = !empty($ids) ? implode(',', $ids) : null;
        $res = $this->request('/v2/wl/attractions/list?' . http_build_query(['status' => 'all', 'ids' => $ids]));
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res ? $res['results'] : null;
        }

        return null;
    }

    public function getCategories(): ?array
    {
        $res = $this->request('/provider/category');
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res ? $res['results'] : null;
        }

        return null;
    }

    public function getCancellation()
    {
        $return = [];
        $res = $this->request('/cancellation');
        foreach ($res as $it) {
            foreach ($it as $r) {
                foreach ($r as $data) {
                    $return[] = $data;
                }
            }
        }
        return $return;
    }

    public function getShowsPrice($params): ?array
    {
        $start = !empty($params['start']) ? $params['start'] : date('m/d/Y');
        $end = !empty($params['end']) ? $params['end'] : date('m/d/Y', time() + 3600 * 24 * 60);
        $res = $this->request(
            "/v2/wl/shows/prices?start=" . $start . "&end=" . $end . (!empty($params['ids']) ? '&ids=' . implode(
                    ',',
                    $params['ids']
                ) : '')
        );
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : null;
        }

        return null;
    }

    public function getAttractionsPrice($params): ?array
    {
        $start = !empty($params['start']) ? $params['start'] : date('m/d/Y');
        $end = !empty($params['end']) ? $params['end'] : date("m/d/Y", time() + 3600 * 24 * 60);
        $res = $this->request(
            "/v2/wl/attractions/prices?start=" . $start . "&end=" . $end . (!empty($params['ids']) ? '&ids=' . implode(
                    ',',
                    $params['ids']
                ) : '')
        );
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : null;
        }

        return null;
    }

    public function getShowsLocation(): ?array
    {
        $res = $this->request('/provider/location');
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : null;
        }

        return null;
    }

    public function getDining($ids = null): ?array
    {
        $ids = !empty($ids) ? implode(',', $ids) : null;
        $res = $this->request('/v2/wl/dining/list?' . http_build_query(['status' => 'all', 'ids' => $ids]));
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : null;
        }

        return null;
    }

    public function getDiningPrice($params): ?array
    {
        $start = !empty($params['start']) ? $params['start'] : date('m/d/Y');
        $end = !empty($params['end']) ? $params['end'] : date('m/d/Y', time() + 3600 * 24 * 60);
        $status = !empty($params['status']) ? (bool)$params['status'] : true;
        $status = $status === true ? 'true' : '';
        $res = $this->request(
            "/v2/wl/dining/prices?start=" . $start . "&end=" . $end . "&status=" . $status . (!empty($params['ids']) ? '&ids=' . implode(
                    ',',
                    $params['ids']
                ) : '')
        );
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : null;
        }

        return null;
    }

    public function postCustomer($data): ?array
    {
        if (!empty($data['id'])) {
            $res = $this->request('/customer/' . $data['id'], $data, 'put');
        } else {
            $res = $this->request('/customer', $data, 'post');
        }
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res;
        }

        return null;
    }

    public function getCustomer($id): ?array
    {
        if (!$id) {
            return null;
        }
        $res = $this->request('/customer/' . $id);
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res;
        }
        return null;
    }

    /**
     * @param $data
     *
     * @return Itinerary|null
     */
    public function postItinerary($data): ?Itinerary
    {
        $result = $this->request('/v2/wl/ext/itinerary', $data, 'post');

        if (empty($this->getErrors())) {
            return (new Itinerary())->loadData($result);
        }
        return null;
    }

    /**
     * @param string $session
     * @param Coupon $coupon
     *
     * @return Itinerary|null
     */
    public function getItineraryByCoupon($session, Coupon $coupon): ?Itinerary
    {
        if (!$session) {
            return null;
        }

        $result = $this->request(
            '/ext/itinerary/' . $session . '/?' . http_build_query(['discountCode' => $coupon->code])
        );

        if (empty($this->getErrors())) {
            return (new Itinerary())->loadData($result);
        }
        return null;
    }

    /**
     * Gets hotels
     *
     * @param null  $ids
     * @param mixed $status
     *
     * @return array
     */
    public function getPosHotels($ids = null, $status = 'all'): ?array
    {
        $ids = !empty($ids) ? implode(',', $ids) : null;
        $res = $this->request('/v2/wl/hotel/list?' . http_build_query(['status' => $status, 'ids' => $ids]));

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : null;
        }

        return null;
    }

    /**
     * @param array|null    $ids
     * @param DateTime|null $start
     * @param DateTime|null $end
     * @param int|null      $adults
     * @param array|null    $childAges
     *
     * @param string|null   $status
     *
     * @return array
     */
    public function getPosHotelsPrice(
        array $ids = null,
        DateTime $start = null,
        DateTime $end = null,
        int $adults = null,
        array $childAges = null,
        string $status = null
    ): ?array {
        $res = $this->request(
            '/v2/wl/hotel/prices?'
            . implode(
                '&',
                [
                    'status=' . $status,
                    'adults=' . $adults,
                    'childAges=' . (is_array($childAges) ? implode(',', $childAges) : null),
                    'start=' . ($start ? $start->format('m/d/Y') : null),
                    'end=' . ($end ? $end->format('m/d/Y') : null),
                    'ids=' . (is_array($ids)
                        ? implode(',', array_slice($ids, 0, 250)) : null)
                ]
            )
        );

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : null;
        }
        return null;
    }

    /**
     * @param bool     $isPriceLine
     * @param string   $id
     * @param DateTime $start
     * @param DateTime $end
     * @param array    $rooms
     *
     * @return array|null
     */
    public function getHotelContent(
        bool $isPriceLine,
        string $id,
        DateTime $start,
        DateTime $end,
        $rooms = []
    ): ?array
    {
        $res = $this->getHotelData($isPriceLine, $id, $start, $end, $rooms);

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return array_values($res)[0][0] ?? null;
        }
        return null;
    }

    /**
     * @param bool     $isPriceLine
     * @param string   $id
     * @param DateTime $start
     * @param DateTime $end
     * @param array    $rooms
     *
     * @return array|null
     */
    private function getHotelData(
        bool $isPriceLine,
        string $id,
        DateTime $start,
        DateTime $end,
        $rooms = []
    ): ?array
    {
        $groups = array_map(
            static function ($el) {
                return "a:{$el['adult']},ch:" . (implode('_', !empty($el['age']) ? $el['age'] : []));
            },
            $rooms
        );

        $category = $isPriceLine ? 'hotels' : 'hotel';
        $res = $this->request(
            "/v2/wl/{$category}/prices/{$id}?"
            . implode(
                '&',
                [
                    "groups=" . implode(';', $groups),
                    'start=' . ($start ? $start->format('m/d/Y') : null),
                    'end=' . ($end ? $end->format('m/d/Y') : null)
                ]
            )
        );
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res;
        }
        return null;
    }

    /**
     * @param bool     $isPriceLine
     * @param string   $id
     * @param DateTime $start
     * @param DateTime $end
     * @param array    $rooms
     *
     * @return TripiumHotelPrice[]
     */
    public function getHotelPrices(
        bool $isPriceLine,
        string $id,
        DateTime $start,
        DateTime $end,
        $rooms = []
    ): ?array {
        $res = $this->getHotelData($isPriceLine, $id, $start, $end, $rooms);

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            $result = [];
            foreach ($res as $groupKey => $group) {
                if (is_array($group[0]['prices'])) {
                    foreach ($group[0]['prices'] as $price) {
                        $p = new TripiumHotelPrice();
                        $p->loadData(
                            array_merge(
                                $price,
                                [
                                    'time'          => $group[0]['time'],
                                    'start'         => $group[0]['start'],
                                    'end'           => $group[0]['end'],
                                    'hotelType'     => $group[0]['hotelType'],
                                    'checkInPolicy' => $group[0]['checkInPolicy'],
                                    'vendorId'      => $group[0]['vendorId'],
                                    'groupKey'      => $groupKey,
                                ]
                            )
                        );
                        $result[] = $p;
                    }
                }
            }
            return $result;
        }
        return null;
    }

    /**
     * @param $session
     *
     * @return Itinerary|null
     */
    public function getItinerary($session): ?Itinerary
    {
        if (!$session) {
            return null;
        }

        $result = $this->request('/v2/wl/itinerary/' . $session);

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return (new Itinerary())->loadData(['itinerary' => $result]);
        }
        return null;
    }

    /**
     * Add/modify a product in a cart
     *
     * @param string      $session
     * @param array       $data
     * @param Coupon|null $coupon
     *
     * @return Itinerary|null
     */
    public function postPackage($session, $data, Coupon $coupon = null): ?Itinerary
    {
        if (empty($session)) {
            return null;
        }
        $queryData = [];
        if ($coupon) {
            $queryData['discountCode'] = $coupon->code;
        }
        $query = '?' . http_build_query($queryData);

        if (!empty($data['packageId'])) {
            $result = $this->request(
                '/v2/wl/ext/itinerary/' . $session . '/' . $data['packageId'] . $query,
                $data,
                'put'
            );
        } else {
            $result = $this->request('/v2/wl/ext/itinerary/' . $session . $query, $data, 'post');
        }

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return (new Itinerary())->loadData($result);
        }

        try {
            Yii::$app->mailer->compose('basket/basket-add-edit', ['data' => $data, 'errors' => $this->getErrors()])
                ->setFrom(Yii::$app->params['subscriptionEmailFrom'])
                ->setTo(Yii::$app->params['technicalNotificationEmail'])
                ->setSubject('Adding/modify items in a basket. ' . Yii::$app->name)
                ->send();
        } catch (Exception $e) {
        }

        return null;
    }

    /**
     * @return string
     */
    private static function getSiteType(): string
    {
        return !empty(Yii::$app->params['siteType']) ? Yii::$app->params['siteType'] : self::SITE_TYPE_DESKTOP;
    }

    /**
     * @param $session
     * @param $packageId
     *
     * @return Itinerary|null
     */
    public function removePackage($session, $packageId): ?Itinerary
    {
        $result = $this->request('/v2/wl/ext/itinerary/' . $session . '/' . $packageId, [], 'delete');
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return (new Itinerary())->loadData($result);
        }
        return null;
    }

    /**
     * @param $session
     *
     * @return Itinerary|null
     */
    public function clearItinerary($session): ?Itinerary
    {
        $result = $this->request('/v2/wl/ext/itinerary/clear/' . $session, [], 'delete');
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return (new Itinerary())->loadData($result);
        }
        return null;
    }

    /**
     * @param $session
     *
     * @return Itinerary|null
     */
    public function reserve($session): ?Itinerary
    {
        $result = $this->request('/v2/wl/itinerary/' . $session . '/reserve', [], 'post');
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return (new Itinerary())->loadData($result);
        }
        return null;
    }

    public function addOrder($data): ?array
    {
        $res = $this->request('/v2/wl/order', $data, 'post');
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res;
        }
        return null;
    }

    public function getOrder($orderNumber): ?array
    {
        $res = $this->request("/v2/wl/order/$orderNumber");
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res;
        }
        return null;
    }

    /**
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param null          $search
     * @param string        $sort
     * @param int           $page
     * @param int           $size
     *
     * @return array|null
     */
    public function getOrders(
        DateTime $startDate = null,
        DateTime $endDate = null,
        $search = null,
        int $page = 0,
        int $size = 20,
        $sort = 'orderDate,desc'
    ): ?array {
        $query = http_build_query(
            [
                'startDate' => $startDate ? $startDate->format('m/d/Y') : null,
                'endDate' => $endDate ? $endDate->format('m/d/Y') : null,
//                'search' => $search,
                'sort' => $sort,
                'page' => $page,
                'size' => $size,
            ]
        );
        $res = $this->request("/v2/wl/order?" . $query);
        if ($this->statusCode !== self::STATUS_CODE_SUCCESS) {
            return null;
        }
        $results = [];
        foreach ($res['results'] as $order) {
            $results[] = TrOrders::build($order);
        }
        $res['results'] = $results;
        return $res;
    }

    /**
     * @param int      $customerNumber
     * @param DateTime $startDate
     * @param DateTime $endDate
     *
     * @return TrOrders[]|null
     */
    public function getWlCustomerOrders(
        int $customerNumber,
        DateTime $startDate,
        DateTime $endDate
    ): ?array {
        $query = http_build_query(
            [
                'startDate' => $startDate ? $startDate->format('m/d/Y') : null,
                'endDate' => $endDate ? $endDate->format('m/d/Y') : null,
            ]
        );
        $res = $this->request("/v2/wl/report/customer/{$customerNumber}/orders?" . $query);
        if ($this->statusCode !== self::STATUS_CODE_SUCCESS) {
            return null;
        }
        $results = [];
        foreach ($res['results'] as $order) {
            $results[] = TrOrders::build($order);
        }
        return $results;
    }

//    /**
//     * @param $category
//     * @param $vendorId
//     * @param $types
//     * @param $tags
//     * @return array
//     */
//    function getContent($category, $vendorId, $types = null, $tags = null)
//    {
//        $url = "/content/vendor/{$category}/{$vendorId}";
//        $url .= '?' . http_build_query(['types' => $types, 'tags' => $tags]);
//        $res = $this->request($url);
//        return !empty($res) ? $res : [];
//    }

    public function getCustomerCars($id)
    {
        $customer = $this->getCustomer($id);

        if (empty($customer['axiaId'])) {
            return false;
        }

		$res = $this->request('/customer/axia/' . $customer['axiaId']);

        if (empty($res['results'])) {
            return false;
        }

		$res['results'] = ArrayHelper::index($res['results'], 'id');
		foreach($res['results'] as &$it) {
		    $ar = array_merge($it, $it["card"]);
            $it = $ar;
        }

        return $res['results'];
    }

    public function getCustomerOrders($id, $past = true): ?array
    {
        if (!$id) {
            return null;
        }

        $res = $this->request('/v2/wl/customer/' . $id . '/orders?size=2000&page=0&past=' . ($past ? 'true' : 'false'));

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res;
        }
        return null;
    }

    public function getLocations(): ?array
    {
        $result = $this->request("/location?size=2000");
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($result["results"]) ? $result["results"] : null;
        }
        return null;
    }

    public function getLocation($id)
    {
        $result = $this->request("/location/" . $id);
        return !empty($result["results"]) ? $result["results"] : null;
    }

//    public function getAttractionsAvailability($params)
//    {
//        $start = $params['start'] ?: date("m/d/Y");
//        $end = $params['end'] ?: date("m/d/Y", time() + 3600 * 24 * 360);
//        return $this->request("/v2/wl/attractions/availability?start=" . $start . "&end=" . $end)["results"];
//    }

    public function getDiningAvailability($params)
    {
        $start = $params['start'] ?: date("m/d/Y");
        $end = $params['end'] ?: date("m/d/Y", time() + 3600 * 24 * 360);
        return $this->request("/v2/wl/dining/availability?start=" . $start . "&end=" . $end)["results"];
    }

    public function orderCancel($orderNumber): bool
    {
        $this->request(
            '/v2/wl/order/' . $orderNumber . '/cancel?generateTransactions=true',
            ['transactions' => []],
            'post'
        );
        return $this->statusCode === self::STATUS_CODE_SUCCESS;
    }

    public function orderCancelPackage($orderNumber, $packageId): bool
    {
        $this->request(
            '/v2/wl/order/' . $orderNumber . '/cancel/' . $packageId . '?generateTransactions=true',
            ['transactions' => []],
            'post'
        );
        return $this->statusCode === self::STATUS_CODE_SUCCESS;
    }

    public function orderModifyPackage($orderNumber, $packageId, $params)
    {
        return $this->request(
            '/order/' . $orderNumber . '/modify/' . $packageId . '?generateTransactions=true',
            $params,
            "put"
        );
    }

    public function orderCards($orderNumber)
    {
        $res = $this->request('/order/' . $orderNumber . '/cards');
        if (empty($this->errors)) {
            return $res['results'];
        }

        return null;
    }

    public function orderModifyCheck($orderNumber, $packageNumber, $params)
    {
        return $this->request('/order/' . $orderNumber . '/check/' . $packageNumber, $params, "post");
    }

    /**
     * @param array $query
     * @param bool  $useParams
     *
     * @return null
     * @deprecated
     */
    public function getHotels($query = [], $useParams = true)
    {
        $params = self::getRequestHotelParams();

        if ($useParams) {
            $params = array_merge(
                $params,
                [
                    'maxRatePlanCount' => 8,
//                    'customerSessionId' => yii\web\Session::getId(),
//                    'customerIpAddress' => yii\web\Request::getUserIP(),
//                    'customerUserAgent' => yii\web\Request::getUserAgent(),
                ]
            );
        }

        $params = array_merge($params, $query);

        $srt = '';
        foreach ($params as $k => $v) {
            $srt .= '&' . $k . '=' . $v;
        }

        $params = str_replace(' ', '%20', $srt);

        $url = '/hotels?k=1' . $params;

        $res = $this->request($url);

        if (!empty($res['results'])) {
            return $res;
        }

        return null;
    }

    public function getHotel($id)
    {
        $params = self::getRequestHotelParams();

        $srt = '';
        foreach ($params as $k => $v) {
            $srt .= '&' . $k . '=' . $v;
        }

        $srt = trim($srt, ' &');
        $params = str_replace(' ', '%20', $srt);
        $url = '/hotels/' . $id . '?' . $params;

        $res = $this->request($url);
        if (!empty($res["results"])) {
            return $res["results"];
        }

        return $res;
    }

    public function getGeoHotels()
    {
        $params = array_merge(
            self::getRequestHotelParams(),
            [
//                'customerSessionId' => yii\web\Session::getId(),
//                'customerIpAddress' => yii\web\Request::getUserIP(),
//                'customerUserAgent' => yii\web\Request::getUserAgent(),
            ]
        );

        $url = '/geo/hotels?' . http_build_query($params);

        $res = $this->request($url);
        if (!empty($res["results"])) {
            return $res["results"];
        }

        return $res;
    }

    public function getHotelsPrice($query = [], $useParams = true)
    {
        $params = self::getRequestHotelParams();

        if ($useParams) {
            $params = array_merge(
                $params,
                [
//                    'customerSessionId' => yii\web\Session::getId(),
//                    'customerIpAddress' => yii\web\Request::getUserIP(),
//                    'customerUserAgent' => yii\web\Request::getUserAgent(),
                ]
            );
        }

        if ($query) {
            $params = array_merge($params, $query);
        }

        $srt = '';
        foreach ($params as $k => $v) {

            $srt .= '&' . $k . '=' . $v;
        }

        $params = str_replace(' ', '%20', $srt);

        $url = '/hotels/price?k=1' . $params;

        $res = $this->request($url);

        if (!empty($res['results'])) {
            return $res;
        }

        return null;
    }

    /**
     * @param $sessionId
     * @param $type
     * @param $auto
     *
     * @return Coupon[]
     * */
    public function getCoupons($sessionId, $type, $auto = null): array
    {
        $url = "/discount/itinerary/$sessionId";
        $res = $this->request($url);

        return isset($res['results']) ? self::filterCoupons($res['results'], $type, $auto) : [];
    }

    /**
     * @param array|null $list
     * @param string     $type
     * @param null       $auto
     *
     * @return Coupon[]
     */
    public static function filterCoupons($list, $type, $auto = null): array
    {
        $result = [];

        if (!empty($list)) {
            foreach ($list as $data) {
                $Coupon = new Coupon;
                $Coupon->loadData($data);
                if (($auto === null || $Coupon->auto === $auto) && $Coupon->isTypeOf($type)) {
                    $result[$Coupon->code] = $Coupon;
                }
            }
        }

        return $result;
    }

    /**
     * @param array|null $list
     * @param string     $type
     * @param null       $auto
     *
     * @return Coupon|null
     */
    public static function getTheBestCoupon($list, $type, $auto = null): ?Coupon
    {
        if (empty($list)) {
            return null;
        }

        $list = self::filterCoupons($list, $type, $auto);

        usort(
            $list,
            static function ($a, $b) {
                if ($a->discount === $b->discount) {
                    return 0;
                }
                return ($a->discount > $b->discount) ? -1 : 1;
            }
        );
        $list = array_values($list);
        return $list[0];
    }

	/**
	 * @param $code
	 * @param $siteType
	 *
	 * @return Coupon|null
	 * */
	public function getCouponByCode($code, $siteType = null): ?Coupon
    {
	    if (empty($code)) {
	        return null;
	    }

	    if (!$siteType) {
	        $siteType = Yii::$app->params['siteType'] === Coupon::COUPON_TYPE_MOBILE ? Coupon::COUPON_TYPE_MOBILE : Coupon::COUPON_TYPE_DESKTOP;
	    } else if (!in_array($siteType, [Coupon::COUPON_TYPE_DESKTOP, Coupon::COUPON_TYPE_MOBILE], true)) {
	        return null;
	    }

	    $Basket = TrBasket::find()->where(['session_id' => TrBasket::getSessionID()])->one();
	    $Coupons = $this->getCoupons($Basket->sessionId, $siteType);

	    foreach ($Coupons as $Coupon) {
	        if (StrHelper::strtolower($Coupon->code) === StrHelper::strtolower($code)) {
	            return $Coupon;
	        }
	    }

	    return null;
	}

	/**
	 * @param $orderNumber
	 * @param $packageId
	 * @param $params
	 *
	 * @return Coupon[]
	 * */
	public function getCouponsForOrder($orderNumber, $packageId, $params): array
    {
	    $res = $this->request("/discount/order/$orderNumber/package/$packageId", $params, "post");

	    $result = [];

	    if (!empty($res["results"])) {
	        foreach ($res["results"] as $data) {
	            $Coupon = new Coupon;
	            $Coupon->loadData($data);
	            $result[] = $Coupon;
	        }
	    }

	    return $result;
	}

//	/**
//	 * @param $orderNumber
//	 * @param $discountCode
//	 * @param $type
//	 *
//	 * @return array
//	 * */
//	public function getRecalculatingPakages($orderNumber, $discountCode, $type = null)
//	{
//	    $res = $this->request("/discount/itinerary/$orderNumber/$discountCode?".http_build_query(['type'=>$type]), [], "get");
//
//	    $result = [];
//
//	    if (!empty($res["results"])) {
//	        $result = $res["results"];
//	    }
//
//	    return $result;
//	}

    /**
     * @param string $orderNumber
     * @param string $discountCode
     * @param array  $request
     *
     * @return Package[]
     */
    public function getRecalculatingPackagesInOrder($orderNumber, $discountCode, array $request): array
    {
        $res = $this->request("/discount/order/$orderNumber/$discountCode", $request, "post");

        $result = [];

        if (!empty($res["results"])) {
            foreach ($res["results"] as $data) {
                $package = new Package();
                $package->loadData($data);
                $result[] = $package;
            }
        }

        return $result;
    }

    /**
     * Return List of All Available Vacation Packages.
     *
     * @see https://pos23.docs.apiary.io/#reference/0/vacation-packages/list-of-all-available-vacation-packages
     */
    public function getVacationPackages()
    {
        $res = $this->request('/vacation/list?currentChannel=true');
        if (!empty($res["results"])) {
            return $res["results"];
        }

        return [];
    }

    /**
     * Add to Cart.
     *
     * @param string $session
     * @param string $packageId
     * @param array  $items
     *
     * @return null|array
     * @see https://pos23.docs.apiary.io/#reference/0/vacation-packages/add-vacation-package-to-itinerary
     */
    public function addPackageToCart($session, $packageId, array $items): ?array
    {
        $request = [
            'configId' => $packageId,
            'items' => $items
        ];
        $res = $this->request('/vacation/itinerary/' . $session, $request, 'post');

        if (!empty($res["results"])) {
            return $res["results"];
        }

        return null;
    }

    /**
     * Delete Vacation Package in cart.
     *
     * @param string $session
     * @param string $packageId
     *
     * @return boolean
     * @see https://pos23.docs.apiary.io/#reference/0/vacation-packages/delete-vacation-package-from-itinerary
     */
    public function deleteVacationPackages($session, $packageId)
    {
        $this->request('/vacation/itinerary/' . $session . '/' . $packageId, [], 'delete');
        return empty($this->errors);
    }

    /**
     * Cancel Vacation Package in order.
     *
     * @param string $orderNumber
     * @param string $packageId
     *
     * @return boolean
     * @see https://pos23.docs.apiary.io/#reference/0/vacation-packages/cancel-vacation-package
     */
    public function cancelVacationPackage($orderNumber, $packageId)
    {
        $this->request(
            '/vacation/order/' . $orderNumber . '/cancel/' . $packageId . '?generateTransactions=true',
            ["transactions" => [], "vacationPackages" => []],
            'post'
        );
        return empty($this->errors);
    }

    /**
     * Get url of barcode.
     *
     * @param $category
     * @param $packageId
     * @param $barCode
     *
     * @return string
     */
    public static function getBarcodeUrl($category, $packageId, $barCode)
    {
        return Yii::$app->params["tripium"]["urlRoot"] . '/barcode/' . $category . '/' . $packageId . '/' . $barCode;
    }

    /**
     * SDC Voucher Data / List of vouchers for specific order.
     *
     * @see https://pos23.docs.apiary.io/#reference/0/sdc-voucher-data/list-of-vouchers-for-specific-order
     *
     * @param string $orderNumber
     *
     * @return array
     */
    public function getSdcVouchersOrder($orderNumber)
    {
        $res = $this->request('/order/sdc/' . $orderNumber);
        if (!empty($res["results"])) {
            return $res["results"];
        }

        return null;
    }

    /**
     * Get link for Voucher file.
     *
     * @param string      $orderNumber
     * @param null|string $packageId
     *
     * @return string
     */
    public function getVoucherLink(string $orderNumber, $packageId = null): ?string
    {
        $res = $this->request('/v2/wl/voucher/' . $orderNumber . ($packageId ? '/' . $packageId : ''));
        if ($this->statusCode === self::STATUS_CODE_SUCCESS && !empty($res["url"])) {
            if (!empty(Yii::$app->params['replaceDownlowUrlFile']) && is_array(
                    Yii::$app->params['replaceDownlowUrlFile']
                )) {
                foreach (Yii::$app->params['replaceDownlowUrlFile'] as $search => $replace) {
                    $res['url'] = str_replace($search, $replace, $res['url']);
                }
            }
            return $res['url'];
        }

        return null;
    }

    /**
     * Gets Price Line hotels.
     *
     * @param DateTime $checkIn
     * @param DateTime $checkOut
     * @param int      $rooms
     * @param int      $adults
     * @param int      $children
     * @param string   $sortBy
     * @param array    $hotelIds
     *
     * @return array
     */
    public function getPLHotels(
        DateTime $checkIn,
        DateTime $checkOut,
        int $rooms = 1,
        int $adults = 2,
        int $children = 0,
        $sortBy = null,
        array $hotelIds = []
    ): array {
        $query = http_build_query(
            [
                'check_in' => $checkIn->format('m/d/Y'),
                'check_out' => $checkOut->format('m/d/Y'),
                'rooms' => $rooms,
                'adults' => $adults,
                'children' => $children,
                'sort_by' => $sortBy,
                'hotel_ids' => !empty($hotelIds) ? implode(',', $hotelIds) : null
            ]
        );
        $res = $this->request('/hotels?' . $query);

        return !empty($res['results']) ? $res['results'] : [];
    }

    /**
     * Gets a Price Line hotel detail.
     *
     * @param int $id
     *
     * @return array|null
     */
    public function getPLHotelDetail(int $id): ?array
    {
        $res = $this->request('/hotels/' . $id);

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return is_array($res) ? $res : null;
        }

        return null;
    }

    /**
     * Gets a price of Price Line hotel.
     *
     * @param string $ppnBundle
     *
     * @return array|null
     */
//    public function getPLHotelPrice(string $ppnBundle): ?array
//    {
//        $query = http_build_query(['ppn_bundle' => $ppnBundle]);
//
//        $res = $this->request('/hotels/price?' . $query);
//
//        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
//            return is_array($res['results']) && !empty($res['results']) ? $res['results'][0] : null;
//        }
//        return null;
//    }

    /**
     * Gets the terms and conditions of Price Line hotel.
     *
     * @return array|null
     */
    public function getPLTermsConditions(): ?array
    {
        $res = $this->request('/priceline/policy?category=terms_and_conditions');

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return is_array($res['results']) ? $res['results'] : null;
        }
        return null;
    }

    /**
     * Gets the privacy policy of Price Line hotel.
     *
     * @return array|null
     */
    public function getPLPrivacyPolicy(): ?array
    {
        $res = $this->request('/priceline/policy?category=privacy_policy');

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return is_array($res['results']) ? $res['results'] : null;
        }

        return null;
    }

    /**
     * Affiliate Report.
     *
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param bool     $status
     * @param string   $checkNumber
     *
     * @return array|null
     */
    public function getAffiliateReport(
        DateTime $startDate,
        DateTime $endDate,
        bool $status,
        $checkNumber = null
    ): ?array {
        $query = http_build_query(
            [
                'startDate' => $startDate->format('m/d/Y'),
                'endDate' => $endDate->format('m/d/Y'),
                'status' => $status ? 'true' : 'false',
                'checkNumber' => $checkNumber,
            ]
        );

        $res = $this->request('/v2/wl/report/affiliate?' . $query);
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return isset($res['rows']) ? $res : null;
        }
        return null;
    }

    /**
     * Incentive Report.
     *
     * @param string|int    $vendor
     * @param DateTime|null $orderStart
     * @param DateTime|null $orderEnd
     * @param DateTime|null $itemStart
     * @param DateTime|null $itemEnd
     * @param string|null   $agent
     * @param string|null   $location
     *
     * @return array|null
     */
    public function getIncentiveReport(
        $vendor,
        DateTime $orderStart = null,
        DateTime $orderEnd = null,
        DateTime $itemStart = null,
        DateTime $itemEnd = null,
        $agent = null,
        $location = null
    ): ?array {
        $query = http_build_query(
            [
                'vendor' => $vendor,
                'orderStart' => $orderStart ? $orderStart->format('m/d/Y') : null,
                'orderEnd' => $orderEnd ? $orderEnd->format('m/d/Y') : null,
                'itemStart' => $itemStart ? $itemStart->format('m/d/Y') : null,
                'itemEnd' => $itemEnd ? $itemEnd->format('m/d/Y') : null,
                'info' => $location ?: null,
                'agent' => $agent ?: null,
            ]
        );
        $res = $this->request('/v2/wl/report/incentive?' . $query);
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return is_array($res) ? $res : null;
        }
        return null;
    }

    /**
     * Reservation Report.
     *
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param string        $vendorId
     *
     * @return array|null
     */
    public function getReservationReport(
        DateTime $startDate = null,
        DateTime $endDate = null,
        $vendorId = null
    ): ?array {
        $params = [
            'startDate' => $startDate ? $startDate->format('m/d/Y') : null,
            'endDate' => $endDate ? $endDate->format('m/d/Y') : null,
        ];
        if ($vendorId) {
            $params['vendor'] = $vendorId;
        }
        $query = http_build_query($params);
        $res = $this->request('/v2/wl/report/reservation?' . $query);
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : null;
        }
        return null;
    }

    /**
     * Delivery Report.
     *
     * @param DateTime|null $date
     * @param string        $location
     *
     * @return array|null
     */
    public function getDeliveryReport(
        DateTime $date = null,
        $location = null
    ): ?array {
        $query = http_build_query(
            [
                'date' => $date ? $date->format('m/d/Y') : null,
                'info' => $location ?: null
            ]
        );
        $res = $this->request('/v2/wl/report/delivery?' . $query);

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : null;
        }
        return null;
    }

    /**
     * Customer Report.
     *
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     *
     * @return array|null
     */
    public function getCustomerReport(
        DateTime $startDate = null,
        DateTime $endDate = null
    ): ?array {
        $query = http_build_query(
            [
                'startDate' => $startDate ? $startDate->format('m/d/Y') : null,
                'endDate' => $endDate ? $endDate->format('m/d/Y') : null,
            ]
        );
        $res = $this->request('/v2/wl/report/customer?' . $query);

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : null;
        }
        return null;
    }

    /**
     * Get the Wl location config.
     *
     * @return array|null
     */
    public function getLocationConfigWl(): ?array
    {
        $res = $this->request('/location/config/wl');

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res;
        }
        return null;
    }

    /**
     * Set the Wl location config.
     *
     * @param array $config
     *
     * @return array|null
     */
    public function setLocationConfigWl(array $config): ?array
    {
        $res = $this->request('/location/config/wl', $config, 'post');
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res;
        }
        return null;
    }

    /**
     * Set the Wl location config.
     *
     * @param string $sessionId
     *
     * @return array|null
     */
    public function getCancellationTexts(string $sessionId): ?array
    {
        $res = $this->request("/cancellation/session/$sessionId/text");
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res;
        }
        return null;
    }

    public function logKioskPayment(string $sessionId): ?array
    {
        $res = $this->request("/kiosk/log/payment/$sessionId");
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res;
        }
        return null;
    }
}

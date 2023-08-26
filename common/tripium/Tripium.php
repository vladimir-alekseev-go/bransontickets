<?php

namespace common\tripium;

use common\helpers\General;
use common\helpers\MarketingItemHelper;
use DateTime;
use Exception;
use Yii;
use yii\base\Model;
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

        return isset($ar[$val]) ? $ar[$val] : $val;
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
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 10);
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
        if ($this->statusCode === 0 && $server_output === false && floor(
                $this->curlInfo['total_time']
            ) == $this->timeout) {
            $this->statusCode = self::STATUS_GATEWAY_TIMEOUT;
            $this->addError('request', 'Gateway Time-out');
        }
// 		echo '<pre>'; var_dump($this->requestData); echo '</pre>';
// 		echo '<pre>'; echo $this->requestData['params']; echo '</pre>';
// 		echo "<pre>statusCode: "; var_dump($this->statusCode); echo "</pre>";
// 		echo "<pre>server_output: "; var_dump($server_output); echo "</pre>";
//exit();
        $phone = General::getConfigPhoneNumber();

        if ($this->statusCode !== self::STATUS_CODE_SUCCESS) {
            try {
                self::requestSendMail($server_output);
            } catch (Exception $e) {
            }
        }

        if ($this->statusCode === self::STATUS_CODE_SUCCESS
            || $this->statusCode === self::STATUS_UNPROCESSABLE_ENTITY) {
            $res = Json::decode($server_output);

            if (isset($res["errorCode"]) && $res["errorCode"] == self::ERROR_CRUD_WITH_PAST_DATE) {
                if (!empty($itempriceGroup["name"])) {
                    $this->addErrors([
                        "Tickets " . ($itempriceGroup["name"] ? 'for ' . $itempriceGroup["name"] : '') . " are no longer available to purchase online. Please remove this item from your shopping cart to complete your order. 
				If you would like further assistance with purchasing " . $res["data"]["pkg"]["name"] . ", please call us at $phone."
                    ]);
                }
            }
            if (isset($res["errorCode"])) {
                $this->errorCode = $res["errorCode"];
            }

            if (!empty($res["errors"])) {
                $this->addErrors([$res["errorCode"]]);
            }

            if (isset($res["errorCode"]) && in_array(
                    $res["errorCode"],
                    [
                        self::CUTOFF,
                        self::ERROR_NOT_AVAILABLE_SS,
                        self::ERROR_NOT_AVAILABLE
                    ]
                )) {
                $package = isset($res['data']['pkg']) ? $res['data']['pkg'] : $res['package'];
                if (!empty($package['category'])) {
                    $itemNames = MarketingItemHelper::getItemNames();
                    $itemName = isset($itemNames[$package['category']]) ? $itemNames[$package['category']] : '';
                    $this->addErrors([
                        "You are attempting to purchase tickets for " . strtolower(
                            $itemName
                        ) . " {$package['name']}, {$package['date']}, {$package['time']} within cutoff time or there are not enough tickets available. Please change your requested dates/times or call us $phone"
                    ]);
                }
            }

            if (isset($res["errorCode"]) && $res["errorCode"] == self::ERROR_CANCELLED) {
                $this->addErrors([".<br/>You could still cancel any individual item(s) that are still within cancellation period or call us $phone to assist you."]);
            }

            if (!empty($this->errorCode) && $this->errorCode == self::STATUS_ONE_HOTEL_PER_ORDER) {
                $this->addErrors([self::getStatusValue(self::STATUS_ONE_HOTEL_PER_ORDER)]);
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

    public function requestSendMail($server_output)
    {
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

        return false;
    }

    public function getShows($ids = null): ?array
    {
        $ids = !empty($ids) ? implode(',', $ids) : null;
        $res = $this->request('/shows?' . http_build_query(['status' => 'all', 'ids' => $ids]));
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res ? $res['results'] : [];
        }
        return null;
    }

    public function getAttractions($ids = null)
    {
        $ids = !empty($ids) ? implode(',', $ids) : null;
        $res = $this->request('/attractions?' . http_build_query(['status' => 'all', 'ids' => $ids]));
        return $res ? $res['results'] : [];
    }

    public function getCategories(): ?array
    {
        $res = $this->request('/provider/category');
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return $res ? $res['results'] : [];
        }
        return null;
    }

    public function getShowsPrice($params): ?array
    {
        $start = !empty($params['start']) ? $params['start'] : date('m/d/Y');
        $end = !empty($params['end']) ? $params['end'] : date('m/d/Y', time() + 3600 * 24 * 60);
        $res = $this->request(
            "/shows/price?start=" . $start . "&end=" . $end . (!empty($params['ids']) ? '&ids=' . implode(
                    ',',
                    $params['ids']
                ) : '')
        );
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : [];
        }
        return null;
    }

    public function getAttractionsPrice($params)
    {
        $start = !empty($params['start']) ? $params['start'] : date('m/d/Y');
        $end = !empty($params['end']) ? $params['end'] : date("m/d/Y", time() + 3600 * 24 * 60);
        $res = $this->request(
            "/attractions/price?start=" . $start . "&end=" . $end . (!empty($params['ids']) ? '&ids=' . implode(
                    ',',
                    $params['ids']
                ) : '')
        );
        return !empty($res['results']) ? $res['results'] : [];
    }

    public function getAttractionsAvailability($params)
    {
        $start = $params['start'] ? $params['start'] : date("m/d/Y");
        $end = $params['end'] ? $params['end'] : date("m/d/Y", time() + 3600 * 24 * 360);
        return $this->request("/attractions/availability?start=" . $start . "&end=" . $end)["results"];
    }

    public function getShowslocation(): ?array
    {
        $res = $this->request('/provider/location');
        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return !empty($res['results']) ? $res['results'] : [];
        }
        return null;
    }

    /**
     * @param $category
     * @param $vendorId
     * @param $types
     * @param $tags
     * @return array
     */
    function getContent($category, $vendorId, $types = null, $tags = null)
    {
        $url = "/content/vendor/{$category}/{$vendorId}";
        $url .= '?' . http_build_query(['types' => $types, 'tags' => $tags]);
        $res = $this->request($url);
        return !empty($res) ? $res : [];
    }

    /**
     * Gets hotels
     * @param null $ids
     * @param mixed $status
     * @return array
     */
    function getPosHotels($ids = null, $status = 'all')
    {
        $ids = !empty($ids) ? implode(',', $ids) : null;
        $res = $this->request("/hotel?" . http_build_query(['status' => $status, 'ids' => $ids]));
        return !empty($res["results"]) ? $res["results"] : [];
    }

    /**
     * @param $params
     *
     * @return array
     */
    function getPosHotelsPrice($params)
    {
        $start = !empty($params['start']) ? $params['start'] : date("m/d/Y");
        $end = !empty($params['end']) ? $params['end'] : date("m/d/Y",time()+3600*24*60);
        $res = $this->request("/hotel/price?start=".$start."&end=".$end.(!empty($params['ids']) ? '&ids='.implode(',',$params['ids']) : ''));
        return !empty($res["results"]) ? $res["results"] : [];
    }

    /**
     * @param array $query
     * @param bool  $useParams
     *
     * @return null
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
    public function getPLHotelPrice(string $ppnBundle): ?array
    {
        $query = http_build_query(['ppn_bundle' => $ppnBundle]);

        $res = $this->request('/hotels/price?' . $query);

        if ($this->statusCode === self::STATUS_CODE_SUCCESS) {
            return is_array($res['results']) && !empty($res['results']) ? $res['results'][0] : null;
        }
        return null;
    }
}

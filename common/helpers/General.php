<?php

namespace common\helpers;

use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Response;

class General
{
	const PERIOD_MOBILE = 1;
	const PERIOD_DESKTOP = 4;

    public static function formatDateUrlTicket($date)
    {
        if (is_int($date)) {
            return date('Y-m-d_H:i:s', $date);
        }

        return date('Y-m-d_H:i:s', strtotime($date));
    }

	public static function formatPhoneNumber($phone)
	{
		$phone = str_replace(["(",")","-"," "], "", $phone);
		if(strlen($phone) === 10) {
            return substr($phone, -10, 3) . "-" . substr($phone, -7, 3) . "-" . substr($phone, -4, 4);
        }
		if(strlen($phone) === 11) {
            return substr($phone, -11, 1) . "-" . substr($phone, -10, 3) . "-" . substr($phone, -7, 3) . "-" . substr(
                    $phone,
                    -4,
                    4
                );
        }

		return $phone;
	}

	public static function handleVideoLink($links): array
    {
	    $tmp = [];
    	foreach ($links as $link) {
    	    $_link = Media::prepareUrl($link);
    	    $tmp[$_link] = $_link;
    	}
    	return $tmp;
	}

    /**
     * @param string $from
     * @param string $to
     *
     * @return array
     * @throws Exception
     * @deprecated use General::getDatePeriod()
     */
    public static function getRange($from = null, $to = null): array
    {
        $datePeriod = self::getDatePeriod($from, $to);

        return ['from' => $datePeriod->start->format('U'), 'to' => $datePeriod->end->format('U')];
    }

    /**
     * Return period
     *
     * @param string $from
     * @param string $to
     * @param string $interval_spec
     *
     * @return DatePeriod
     * @throws Exception
     */
    public static function getDatePeriod($from = null, $to = null, $interval_spec = 'P1D'): DatePeriod
    {
        $interval = new DateInterval($interval_spec);

        $from = $from ?: Yii::$app->session->get('select-date-from');
        $to = $to ?: Yii::$app->session->get('select-date-to');

        if ($from === null) {
            try {
                $siteSettings = Yii::$app->get('siteSettings')->data;
                $defaultDate = $siteSettings->getDefaultDate();
                if ($defaultDate && $defaultDate > new DateTime()) {
                    $from = $defaultDate->format('m/d/Y');
                }
            } catch (InvalidConfigException $e) {
            }
        }

        try {
            $dateFrom = new DateTime($from);
            $dateTo = new DateTime($to);
        } catch (Exception $e) {
            $dateFrom = (new DateTime())->add(new DateInterval('P1D'));
            $dateTo = (new DateTime())->add(new DateInterval('P2D'));
        }
        $dateCurrent = new DateTime((new DateTime())->format('m/d/Y'));

        if ($dateFrom < $dateCurrent) {
            $dateFrom = $dateCurrent;
        }

        if ($dateTo <= $dateFrom) {
            $dateTo = clone $dateFrom;
            $dateTo->add(new DateInterval('P6D'));
        }

        $range = new DatePeriod($dateFrom, $interval, $dateTo);
        $range->start;
        return $range;
    }

    public static function setDatePeriod(DateTime $from, DateTime $to): void
    {
        if ($from) {
            Yii::$app->session->set('select-date-from', $from->format('m/d/Y'));
        }

        if ($to) {
            Yii::$app->session->set('select-date-to', $to->format('m/d/Y'));
        }
    }

    public static function setRange($from, $to): void
    {
        try {
            if (is_string($from)) {
                $from = new DateTime($from);
            }
            if (is_string($to)) {
                $to = new DateTime($to);
            }
        } catch (Exception $e) {
            $from = (new DateTime())->add(new DateInterval('P1D'));
            $to = (new DateTime())->add(new DateInterval('P2D'));
        }

        self::setDatePeriod($from, $to);
    }

    /**
     * Function to geocode address, it will return false if unable to geocode address
     *
     * @see https://developers.google.com/maps/documentation/geocoding/start
     * @see https://developers.google.com/maps/documentation/geocoding/get-api-key
     *
     * @param string $address
     *
     * @return array
     */
	public static function geocode($address)
	{
	    $result = [
	        "lat" => null,
	        "lng" => null,
	        "formatted_address" => null,
	        "error_message" => null,
	    ];

	    if (isset(Yii::$app->siteSettings) && !empty(Yii::$app->siteSettings->data->google_map_server_key)) {
	        $googleMapKey = Yii::$app->siteSettings->data->google_map_server_key;
	    } else if (!empty(Yii::$app->params['googleApiGeneral']['apiKeyGeocode'])) {
	        $googleMapKey = Yii::$app->params['googleApiGeneral']['apiKeyGeocode'];
	    } else {
	        return $result;
	    }

	    try {
	        $address = urlencode($address);
	        $url = "https://maps.google.com/maps/api/geocode/json?key={$googleMapKey}&address={$address}";

	        $resp_json = file_get_contents($url);

	        $resp = json_decode($resp_json, true);

	        // response status will be 'OK', if able to geocode given address
	        if ($resp['status'] === 'OK') {

	            $lat = $resp['results'][0]['geometry']['location']['lat'];
	            $lng = $resp['results'][0]['geometry']['location']['lng'];
	            $formatted_address = $resp['results'][0]['formatted_address'];

	            // verify if data is complete
	            if ($lat && $lng && $formatted_address) {
	                $result["lat"] = $lat;
	                $result["lng"] = $lng;
	                $result["formatted_address"] = $formatted_address;
	            } else {
	                $result['error_message'] = 'Result is empty';
	            }
	        } else {
	            $result['error_message'] = $resp['error_message'];
	        }
	    } catch (Exception $e) {}

	    return $result;
	}

    /**
     * Return coordinates of searching place, using aproximly coordinates and name
     *
     * @param array $params
     *
     * @return bool|array
     */
	public static function placeNearBySearch($params)
	{
	    if (isset(Yii::$app->siteSettings) && !empty(Yii::$app->siteSettings->data->google_map_server_key)) {
	        $googleMapKey = Yii::$app->siteSettings->data->google_map_server_key;
	    } else if (!empty(Yii::$app->params['googleApiGeneral']['apiKeyGeocode'])) {
	        $googleMapKey = Yii::$app->params['googleApiGeneral']['apiKeyGeocode'];
	    } else {
	        return false;
	    }

	    $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?';
	    $url .= 'rankby=distance&key=' . $googleMapKey.'&';

	    foreach ($params as $pName => $pValue) {
	        $url .= urlencode($pName) . '=' . urlencode($pValue) . '&';
	    }

	    $resp_json = file_get_contents($url);
	    $resp = json_decode($resp_json, true);

	    if ($resp['status'] !== 'OK') {
	        return false;
	    }

	    $lat = $resp['results'][0]['geometry']['location']['lat'];
	    $lng = $resp['results'][0]['geometry']['location']['lng'];

	    if (!$lat || !$lng) {
	        return false;
	    }

	    return [
	        "lat" => $lat,
	        "lng" => $lng,
	    ];
	}

    /**
     * Get img as data
     *
     * @param string $url
     *
     * @return string
     */
	public static function getImageAsData(string $url): string
    {
		$type = pathinfo($url, PATHINFO_EXTENSION);
		$data = @file_get_contents($url);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
	}

    /**
     * Sends a file to the browser by url
     *
     * @param string $url
     * @param string $attachmentName
     * @param array  $options
     */
	public static function sendFile($url, $attachmentName = null, $options = [])
	{
	    $path = Yii::getAlias('@root').'/upload/tmp/'.rand (0,9999999).'-'.$attachmentName;
	    @file_put_contents($path, @file_get_contents($url));
	    if (file_exists($path)) {
    	    Yii::$app->response->sendFile($path, $attachmentName, $options)->on(
                Response::EVENT_AFTER_SEND, function($event) {
    	        unlink($event->data);
    	    }, $path);
	    }
	}

    public static function getConfigPhoneNumber(): string
    {
        return self::formatPhoneNumber(Template::getProperty('phoneMain'));
    }
}

<?php
namespace common\helpers;

use Yii;
use yii\helpers\BaseJson;
use yii\helpers\ArrayHelper;

class Media
{
    const VIMEO_DOMAIN = 'vimeo.com';
    const VIMEO_URL_API = 'https://vimeo.com/api/oembed.json';
    
    const STATUS_CODE_SUCCESS = 200;
    
    public static function prepareUrl($url)
	{
	    $startUrl = $url;
	    $resultUrl = $url;
	    
	    if (self::isIframe($url)) {
			$url = self::getIframeUrl($url);
		}
	    $url = $url.'&sd=1';
	    if (self::isYoutube($url)) {
	        if (self::isYoutubeWatchLink($url)) {
	            $id = self::getYoutubeVideoID($url);
	            if ($id) {
	                return 'https://youtube.com/embed/'.self::getYoutubeVideoID($url).'/';
	            }
	        }
	        
	        $url = str_replace("youtu.be", "youtube.com/embed", $url);
	        
	        $src = $url.(strpos($url, "?") ? "&" : "?")."wmode=transparent";
    		$src = substr($src, strpos($url, ":")+1);
    		$resultUrl = $src;
	    }
	    
	    if (self::isVimeo($url)) {
	        if (strstr($url, self::VIMEO_DOMAIN) !== false) {
    	        $data = self::getVimeoData($url);
    	        if (!empty($data['html']) && self::isIframe($data['html'])) {
    	            $url = self::getIframeUrl($data['html']);
    	        }
    	        $resultUrl = $url;
    		}
	    }
	    
	    return $resultUrl;
	}
	
	public static function getYoutubeVideoID($url)
	{
	    $id = null;
        if (!empty(parse_url($url)['query']) && $query = parse_url($url)['query']) {
            $ar = explode('&', $query);
            foreach ($ar as $p) {
                $arParams = explode('=', $p);
                if (!empty($arParams[0]) && !empty($arParams[1]) && $arParams[0] == 'v') {
                    $id = $arParams[1];
                    break;
                }
            }
        }
        return $id;
	}
	
	public static function getIframeUrl($url)
	{
	    preg_match_all('/src="([^"]+)/i',$url, $result);
		return $result[1][0];
	}
	
	public static function isIframe($str)
	{
	    return strpos($str, "iframe") !== false;
	}
	
	public static function isVimeo($str)
	{
	    return strpos($str, self::VIMEO_DOMAIN) !== false;
	}
	
	public static function isYoutube($str)
	{
	    foreach (["youtube.", "youtu."] as $s)
	        if (strpos($str, $s) !== false)
	            return true;
	    return false;
	}
	
	public static function isYoutubeWatchLink($str)
	{
	    foreach (["/watch?"] as $s)
	        if (strpos($str, $s) !== false)
	            return true;
	    return false;
	}
	
	public static function getVimeoData($url)
	{
	    $url = self::VIMEO_URL_API.'?url='.$url;
	    $data = self::requestJSON($url);
	    return $data;
	}
	
    public static function requestJSON($url)
    {
    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		
		$server_output = curl_exec ($ch);
		$statusCode = curl_getinfo($ch)["http_code"];
		curl_close ($ch);
		
		if ($statusCode == self::STATUS_CODE_SUCCESS) {
			$res = BaseJson::decode($server_output);
			
			return $res;
		} else {
		    return [];
		}
    }
    
    /**
     * Return final url after redirects
     * @param string $url
     * @return string
     */
    public static function getRealUrl($url): string
    {
        if (empty($url)) {
            return '';
        }

        if (strpos($url, 'http') === false)
        {
            $url = 'https:'.$url;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FILETIME, true);
        
        $allowRedirs = 3;
        $httpCode = 0;
        
        $returnRedirectUrl = $redirectUrl = $url;
        do {
            curl_setopt($ch, CURLOPT_URL, $redirectUrl);
            curl_exec($ch);
            
            $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
            if ($redirectUrl) {
                $returnRedirectUrl = $redirectUrl;
            }
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
        } while ($httpCode != 200 && $redirectUrl && --$allowRedirs > 0);
        
        curl_close($ch);

        return $returnRedirectUrl;
    }
    
    /**
     * Return created time of file
     * @param string $url
     * @return int
     */
    public static function getFileTime($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FILETIME, true);
        
        $allowRedirs = 5;
        $httpCode = 0;
        
        $redirectUrl = $url;
        
        do {
            curl_setopt($ch, CURLOPT_URL, $redirectUrl);
            curl_exec($ch);
            
            $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
        } while ($httpCode != 200 && $redirectUrl && --$allowRedirs > 0);
        
        if ($httpCode != 200) {
            //continue;
        }
        
        $fileTime = curl_getinfo($ch, CURLINFO_FILETIME);
        curl_close($ch);
        
        return $fileTime;
    }
    
    /**
     * Return youtube preview
     */
    public static function getYoutubePreview($youtubeId, $size = 0)
    {
        return "https://img.youtube.com/vi/$youtubeId/$size.jpg";
    }
}
?>
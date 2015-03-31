<?php
class FrontEnd_Helper_ClickoutFunctions
{
    public $shopId;
    public $offerId;
    public $shopInfo;
    public $network;
    public $shopRefUrl;
    public $shopSubRefUrl;
    public $shopActualUrl;
    public $shopPermalink;

    public function __construct($offerId, $shopId)
    {
        if (isset($offerId)) {
            $this->offerId = $offerId;
            $shopInfo = Offer::getShopInfoByOfferId($this->offerId);
            $shopId = isset($shopInfo['shop']['id']) ? $shopInfo['shop']['id'] : '';
            $this->shopRefUrl = isset($shopInfo['refURL']) ? $shopInfo['refURL'] : '';
            $this->shopSubRefUrl = isset($shopInfo['shop']['refUrl']) ? $shopInfo['shop']['refUrl'] : '';
            $this->shopActualUrl = isset($shopInfo['shop']['actualUrl']) ? $shopInfo['shop']['actualUrl'] : '';
            $this->shopPermalink = isset($shopInfo['shop']['permalink']) ? $shopInfo['shop']['permalink'] : '';
            $this->shopInfo = $shopInfo;
        } else {
            $shopInfo = Shop::getShopInfoByShopId($shopId);
            $this->shopInfo = $shopInfo;
            $this->shopRefUrl = isset($shopInfo['refUrl']) ? $shopInfo['refUrl'] : '';
            $this->shopActualUrl = isset($shopInfo['actualUrl']) ? $shopInfo['actualUrl'] : '';
            $this->shopPermalink = isset($shopInfo['permalink']) ? $shopInfo['permalink'] : '';
        }
        
        $this->network = Shop::getAffliateNetworkDetail($shopId);
        $this->shopId = $shopId;
    }
    
    public function checkIfShopHasAffliateNetwork()
    {
        if (!isset($this->network['affliatenetwork'])) {
            return false;
        }
        if (isset($this->shopInfo['deepLink']) && $this->shopInfo['deepLink'] != null) {
            return false;
        } else {
            return true;
        }
    }

    public function getCloakLink($clickoutType, $conversionId = '')
    {
        $networkInfo = self::getSubidWithStringPattern($this->network, $this->shopInfo, $clickoutType, $conversionId);
        $clickoutUrl = self::getUrlForCloakLink(
            $this->shopRefUrl,
            $this->shopSubRefUrl,
            $this->shopActualUrl,
            $this->shopPermalink,
            $networkInfo['subid'],
            $networkInfo['stringPattern']
        );
        return $clickoutUrl;
    }

    // If the affliate network subid contains a regex pattern, then the subid will be splitted by the pipe and regex string pattern will be extracted from the subid.
    public static function getSubidWithStringPattern($network, $shopInfo, $clickoutType, $conversionId)
    {
        $subid = "" ;
        $stringPattern = "";

        if (isset($network['affliatenetwork'])) {
            if (!empty($network['subid'])) {
                $networkInformation = self::getExplodedSubidWithPattern($network);
                $stringPattern = $networkInformation['stringPattern'];
                $gaCookie = isset($_COOKIE['_ga']) ? $_COOKIE['_ga'] : 'notAvailable';

                $subid = str_replace('A2ASUBID', $conversionId, $networkInformation['subid']);
                $subid = str_replace('GOOGLEANALYTICSTRACKINCID', $gaCookie, $subid);
            }
        }

        $networkInfo = array(
            "subid" => $subid,
            "stringPattern" => $stringPattern
        );
        return $networkInfo;
    }

    public static function getExplodedSubidWithPattern($network)
    {
        $subid = "" ;
        $stringPattern = "";

        if (!empty($network['subid'])) {
            if (strpos($network['subid'], "|") !== false) {
                $explodedNetworkSubid = explode("|", $network['subid']);
                $stringPattern = isset($explodedNetworkSubid[0]) ? $explodedNetworkSubid[0] : '';
                $subid = isset($explodedNetworkSubid[1]) ? $explodedNetworkSubid[1] : '';
            } else {
                $subid = "&" . $network['subid'];
            }
        }
        $networkInfo = array(
            "subid" => $subid,
            "stringPattern" => $stringPattern
        );
        return $networkInfo;
    }

    public static function getUrlForCloakLink(
        $shopRefUrl,
        $shopSubRefUrl,
        $shopActualUrl,
        $shopPermalink,
        $subid,
        $stringPattern
    ) {
        if (isset($shopRefUrl) && $shopRefUrl!=null) {
            $clickoutUrl = self::replaceSubidByStringPattern($shopRefUrl, $subid, $stringPattern);
        } else if (isset($shopSubRefUrl) && $shopSubRefUrl!=null) {
            $clickoutUrl = self::replaceSubidByStringPattern($shopSubRefUrl, $subid, $stringPattern);
        } else if (isset($shopActualUrl) && $shopActualUrl!=null) {
            $clickoutUrl = $shopActualUrl;
        } else {
            $clickoutUrl = HTTP_PATH_LOCALE.$shopPermalink;
        }
        return $clickoutUrl;
    }

    public static function replaceSubidByStringPattern($refUrl, $subid, $stringPattern)
    {
        if (isset($stringPattern) && $stringPattern != "") {
            $clickoutUrl = preg_replace("/".$stringPattern."/", $subid, $refUrl);
            if ($clickoutUrl === null) {
                $clickoutUrl = $refUrl;
            }
        } else {
            $clickoutUrl = $refUrl;
            $clickoutUrl .= $subid;
        }
        return $clickoutUrl;
    }
}

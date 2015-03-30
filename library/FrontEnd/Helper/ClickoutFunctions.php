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
            $this->$offerId = $offerId;
            $shopInfo = Offer::getShopInfoByOfferId($this->$offerId);
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
        $this->$shopId = $shopId;
    }
    
    public function checkIfShopHasAffliateNetwork()
    {
        return (!isset($this->network['affliatenetwork']))
            || (isset($this->shopInfo['deepLink']) && $this->shopInfo['deepLink'] != null)
            ? false
            : true;
    }

    public function getCloakLink($clickoutType)
    {
        $networkInfo = self::getSubidWithStringPattern($this->network, $this->shopInfo, $clickoutType);
        $clickoutUrl = self::getUrlForCloakLink(
            $this->shopRefUrl,
            $this->shopSubRefUrl,
            $this->shopActualUrl,
            $this->shopPermalink,
            $networkInfo['subidFlag'],
            $networkInfo['subid'],
            $networkInfo['stringPattern']
        );
        return $clickoutUrl;
    }

    public static function getSubidWithStringPattern($network, $shopInfo, $clickoutType)
    {
        $subid = "" ;
        $stringPattern = "";
        $subidFlag = "";
        if (isset($network['affliatenetwork'])) {
            if (!empty($network['subid'])) {
                $subidInfo = self::getExplodedSubidWithPattern($network);
                $stringPattern = $subidInfo['stringPattern'];
                $subidFlag = $subidInfo['subidFlag'];
                $clientIP = FrontEnd_Helper_viewHelper::getRealIpAddress();
                $clientProperAddress = ip2long($clientIP);
                $conversion = Conversions::getConversionId($shopInfo['id'], $clientProperAddress, $clickoutType);
                $subid = str_replace('A2ASUBID', $conversion['id'], $subidInfo['subid']);
                $subid = FrontEnd_Helper_viewHelper::setClientIdForTracking($subid);
            }
        }

        $networkInfo = array(
            "subid" => $subid,
            "stringPattern" => $stringPattern,
            "subidFlag" => $subidFlag
        );
        return $networkInfo;
    }

    public static function getExplodedSubidWithPattern($network)
    {
        $subid = "" ;
        $stringPattern = "";
        $subidFlag = "";

        if (!empty($network['subid'])) {
            if (strpos($network['subid'], "|") !== false) {
                $explodedNetworkSubid = explode("|", $network['subid']);
                $stringPattern = isset($explodedNetworkSubid[0]) ? $explodedNetworkSubid[0] : '';
                $subid = isset($explodedNetworkSubid[1]) ? $explodedNetworkSubid[1] : '';
                $subidFlag = true;
            } else {
                $subid = "&". $network['subid'];
                $subidFlag = false;
            }
        }
        $networkInfo = array(
            "subid" => $subid,
            "stringPattern" => $stringPattern,
            "subidFlag" => $subidFlag
        );
        return $networkInfo;
    }

    public static function getUrlForCloakLink(
        $shopRefUrl,
        $shopSubRefUrl,
        $shopActualUrl,
        $shopPermalink,
        $subidFlag,
        $subid,
        $stringPattern
    ) {
        if (isset($shopRefUrl) && $shopRefUrl!=null) {
            $clickoutUrl = self::replaceSubidByStringPattern($shopRefUrl, $subidFlag, $subid, $stringPattern);
        } else if (isset($shopSubRefUrl) && $shopSubRefUrl!=null) {
            $clickoutUrl = self::replaceSubidByStringPattern($shopSubRefUrl, $subidFlag, $subid, $stringPattern);
        } else if (isset($shopActualUrl) && $shopActualUrl!=null) {
            $clickoutUrl = $shopActualUrl;
        } else {
            $clickoutUrl = HTTP_PATH_LOCALE.$shopPermalink;
        }
        return $clickoutUrl;
    }

    public static function replaceSubidByStringPattern($refUrl, $subidFlag, $subid, $stringPattern)
    {
        if ($subidFlag == true && $stringPattern != "") {
            $clickoutUrl = preg_replace("/".$stringPattern."/", $subid, $refUrl);
            if ($clickoutUrl == null) {
                $clickoutUrl = $refUrl;
            }
        } else {
            $clickoutUrl = $refUrl;
            $clickoutUrl .= $subid;
        }
        return $clickoutUrl;
    }
}

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

    public function __construct($offerId, $shopId, $overWriteRefUrl = null)
    {
        if (isset($offerId)) {
            $this->offerId = $offerId;
            $shopInfo = \KC\Repository\Offer::getShopInfoByOfferId($this->offerId);
            $shopId = isset($shopInfo['shopOffers']['id']) ? $shopInfo['shopOffers']['id'] : '';
            $this->shopRefUrl = isset($shopInfo['refURL']) ? $shopInfo['refURL'] : '';
            $this->shopSubRefUrl = isset($shopInfo['shopOffers']['refUrl']) ? $shopInfo['shopOffers']['refUrl'] : '';
            $this->shopActualUrl = isset($shopInfo['shopOffers']['actualUrl']) ? $shopInfo['shopOffers']['actualUrl'] : '';
            $this->shopPermalink = isset($shopInfo['shopOffers']['permalink']) ? $shopInfo['shopOffers']['permalink'] : '';
            $this->shopInfo = $shopInfo;
        } else {
            $shopInfo = \KC\Repository\Shop::getShopInfoByShopId($shopId);
            $this->shopInfo = $shopInfo;
            $this->shopRefUrl = isset($shopInfo['refUrl']) ? $shopInfo['refUrl'] : '';
            $this->shopActualUrl = isset($shopInfo['actualUrl']) ? $shopInfo['actualUrl'] : '';
            $this->shopPermalink = isset($shopInfo['permalink']) ? $shopInfo['permalink'] : '';
        }
        if(false === is_null($overWriteRefUrl)) {
            $this->shopRefUrl = $overWriteRefUrl;
        }
        $this->network = \KC\Repository\Shop::getAffliateNetworkDetail($shopId);
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
            $networkInfo
        );
        return $clickoutUrl;
    }

    // If the affliate network subid contains a regex pattern, then the subid will be splitted by the pipe and regex string pattern will be extracted from the subid.
    public static function getSubidWithStringPattern($network, $shopInfo, $clickoutType, $conversionId)
    {
        $networkInfo = array();
        if (isset($network['affliatenetwork'])) {
            $gaCookie = isset($_COOKIE['_ga']) ? $_COOKIE['_ga'] : 'notAvailable';
            if (!empty($network['affliatenetwork']['subId'])) {
                $networkInformation = self::getExplodedSubidWithPattern($network['affliatenetwork']['subId']);
                $subId = str_replace('A2ASUBID', $conversionId, $networkInformation['subid']);
                $subId = str_replace('GOOGLEANALYTICSTRACKINCID', $gaCookie, $subId);
                $networkInfo['subid'] = $subId;
                $networkInfo['subidStringPattern'] = $networkInformation['stringPattern'];
            }
            if (!empty($network['affliatenetwork']['extendedSubid'])) {
                $networkInformation = self::getExplodedSubidWithPattern($network['affliatenetwork']['extendedSubid']);
                $extendedSubId = str_replace('A2ASUBID', $conversionId, $networkInformation['subid']);
                $extendedSubId = str_replace('GOOGLEANALYTICSTRACKINCID', $gaCookie, $extendedSubId);
                $networkInfo['extendedSubId'] = $extendedSubId;
                $networkInfo['extendedSubIdStringPattern'] = $networkInformation['stringPattern'];
            }
        }
        return $networkInfo;
    }

    public static function getExplodedSubidWithPattern($networkSubId)
    {
        $subid = "" ;
        $stringPattern = "";
        if (!empty($networkSubId)) {
            if (strpos($networkSubId, "|") !== false) {
                $explodedNetworkSubid = explode("|", $networkSubId);
                $stringPattern = isset($explodedNetworkSubid[0]) ? $explodedNetworkSubid[0] : '';
                $subid = isset($explodedNetworkSubid[1]) ? $explodedNetworkSubid[1] : '';
            } else {
                $subid = "&" . $networkSubId;
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
        $networkInfo
    ) {
        if (!empty($shopRefUrl)) {
            $clickoutUrl = self::replaceSubidByStringPattern($shopRefUrl, $networkInfo);
        } else if (!empty($shopSubRefUrl)) {
            $clickoutUrl = self::replaceSubidByStringPattern($shopSubRefUrl, $networkInfo);
        } else if (!empty($shopActualUrl)) {
            $clickoutUrl = $shopActualUrl;
        } else {
            $clickoutUrl = HTTP_PATH_LOCALE.$shopPermalink;
        }
        return $clickoutUrl;
    }

    public static function replaceSubidByStringPattern($refUrl, $networkInfo)
    {
        if ((isset($networkInfo['subidStringPattern']) && $networkInfo['subidStringPattern']) || (isset($networkInfo['extendedSubIdStringPattern']) && $networkInfo['extendedSubIdStringPattern'])) {
            $count = 0;

            if (isset($networkInfo['subid']) && isset($networkInfo['subidStringPattern'])) {
                $clickoutUrl = preg_replace("/" . $networkInfo['subidStringPattern'] . "/", $networkInfo['subid'], $refUrl, -1, $count);
            }

            if (!$count && isset($networkInfo['extendedSubId']) && isset($networkInfo['extendedSubIdStringPattern'])) {
                $clickoutUrl = preg_replace("/".$networkInfo['extendedSubIdStringPattern']."/", $networkInfo['extendedSubId'], $refUrl);
            }
            if ($clickoutUrl === null) {
                $clickoutUrl = $refUrl;
            }
        } else {
            $clickoutUrl = $refUrl;
            if (isset($networkInfo['subid'])) {
                $clickoutUrl .= $networkInfo['subid'];
            } elseif (isset($networkInfo['extendedSubId'])) {
                $clickoutUrl .= $networkInfo['extendedSubId'];
            }
        }
        return $clickoutUrl;
    }
}

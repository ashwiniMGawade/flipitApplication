<?php
class FrontEnd_Helper_ClickoutFunctions
{
    public static function getCloakLink($offerId, $checkRefUrl = false)
    {
        $shopInfo = Offer::getShopInfoByOfferId($offerId);
        $network = Shop::getAffliateNetworkDetail($shopInfo['shop']['id']);
        if ($checkRefUrl) {
            if (!isset($network['affliatenetwork'])) {
                return false;
            }
            if ($shopInfo['refURL'] != "") {
                return true;
            } else if ($shopInfo['shop']['refUrl'] != "") {
                return true;
            } else {
                return true;
            }
        }
        $networkInfo = self::getSubidWithStringPattern($network, $shopInfo, 'offer');
        $clickoutUrl = self::getUrlForCloakLink(
            $shopInfo['refURL'],
            $shopInfo['shop']['refUrl'],
            $shopInfo['shop']['actualUrl'],
            $shopInfo['shop']['permalink'],
            $networkInfo['subidFlag'],
            $networkInfo['subid'],
            $networkInfo['stringPattern']
        );
        return $clickoutUrl;
    }

    public static function getStoreLinks($shopId, $checkRefUrl = false)
    {
        $shopInfo = Shop::getShopInfoByShopId($shopId);
        $network = Shop::getAffliateNetworkDetail($shopId);

        if ($checkRefUrl) {
            if (!isset($network['affliatenetwork'])) {
                return false;
            }
            if (isset($shopInfo['deepLink']) && $shopInfo['deepLink']!=null) {
                return false;
            } elseif (isset($shopInfo['refUrl']) && $shopInfo['refUrl']!=null) {
                return true;
            } else {
                return true;
            }
        }

        $networkInfo = self::getSubidWithStringPattern($network, $shopInfo, 'shop');
        $clickoutUrl = self::getUrlForCloakLink(
            $shopInfo['refUrl'],
            "",
            $shopInfo['actualUrl'],
            $shopInfo['permaLink'],
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
        $subidWithCid = "";
        if (isset($network['affliatenetwork'])) {
            if (!empty($network['subid'])) {
                if (strpos($network['subid'], "|") !== false) {
                    $explodedNetworkSubid = explode("|", $network['subid']);
                    if (isset($explodedNetworkSubid[0])) {
                        $stringPattern = $explodedNetworkSubid[0];
                    }
                    if (isset($explodedNetworkSubid[1])) {
                        $subidWithCid = $explodedNetworkSubid[1];
                    }

                    $subid = $subidWithCid;
                    $subidFlag = true;
                } else {
                    $subid = "&". $network['subid'];
                    $subidFlag = false;
                }

                $clientIP = FrontEnd_Helper_viewHelper::getRealIpAddress();
                $clientProperAddress = ip2long($clientIP);
                $conversion = Conversions::getConversionId($shopInfo['id'], $clientProperAddress, $clickoutType);
                $subid = str_replace('A2ASUBID', $conversion['id'], $subid);
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

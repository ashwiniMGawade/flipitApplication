<?php
class FrontEnd_Helper_ClickoutFunctions
{
    public static function getCloakLink($offerId, $checkRefUrl = false)
    {
        $shopInfo = self::getShopInfoByOfferId($offerId);
        $network = Shop::getAffliateNetworkDetail($shopInfo['shop']['id']);
        self::checkRefUrl($checkRefUrl, $shopInfo, $network);
        $networkInfo = self::getSubidWithStringPattern($network, $shopInfo, 'offer');
        $url = self::getUrlForCloakLink(
            $shopInfo['refURL'],
            $shopInfo['shop']['refUrl'],
            $shopInfo['shop']['actualUrl'],
            $shopInfo['shop']['permalink'],
            $networkInfo['subidFlag'],
            $networkInfo['subid'],
            $networkInfo['stringPattern']
        );
        echo $url; die;
        return $url;
    }

    public static function getShopInfoByOfferId($offerId)
    {
        $shopInfo = Doctrine_Query::create()
            ->select(
                's.permaLink as permalink, s.deepLink, s.deepLinkStatus, s.refUrl, s.actualUrl, o.refOfferUrl, o.refUrl'
            )
            ->from('Offer o')
            ->leftJoin('o.shop s')
            ->where('o.id = "'.$offerId.'"')
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        return $shopInfo;
    }

    public static function getStoreLinks($shopId, $checkRefUrl = false)
    {
        $shopInfo = self::getShopInfoByShopId($shopId);
        $network = Shop::getAffliateNetworkDetail($shopId);
        self::checkRefUrlForShop($checkRefUrl, $shopInfo, $network);
        $networkInfo = self::getSubidWithStringPattern($network, $shopInfo);
        $url = self::getUrlForCloakLink(
            $shopInfo['refUrl'],
            "",
            $shopInfo['actualUrl'],
            $shopInfo['permaLink'],
            $networkInfo['subidFlag'],
            $networkInfo['subid'],
            $networkInfo['stringPattern']
        );
        echo $url; die;
        return $url;
    }

    public static function getShopInfoByShopId($shopId)
    {
        $shopInfo = Doctrine_Query::create()
            ->select(
                's.permaLink as permalink, s.deepLink, s.deepLinkStatus, s.refUrl, s.actualUrl'
            )
            ->from('Shop s')
            ->where('s.id='.$shopId)
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
        return $shopInfo;
    }

    public static function checkRefUrl($checkRefUrl, $shopInfo, $network)
    {
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
    }

    public static function checkRefUrlForShop($checkRefUrl, $shopInfo, $network)
    {
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
    }

    public static function getSubidWithStringPattern($network, $shopInfo, $clickoutType)
    {
        $subid = "" ;
        $stringPattern = "";
        $subidWithCid = "";
        if (isset($network['affliatenetwork'])) {
            if (!empty($network['subid'])) {
                if (strpos($network['subid'], "|") !== false) {
                    $splitSubid = explode("|", $network['subid']);
                    if (isset($splitSubid[0])) {
                        $stringPattern = $splitSubid[0];
                    }
                    if (isset($splitSubid[1])) {
                        $subidWithCid = $splitSubid[1];
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
        $shopArrayRefUrl,
        $shopActualUrl,
        $shopPermalink,
        $subidFlag,
        $subid,
        $stringPattern
    ) {
        if (isset($shopRefUrl) && $shopRefUrl!=null) {
            $url = self::replaceSubidByStringPattern($shopRefUrl, $subidFlag, $subid, $stringPattern);
        } else if (isset($shopArrayRefUrl) && $shopArrayRefUrl!=null) {
            $url = self::replaceSubidByStringPattern($shopArrayRefUrl, $subidFlag, $subid, $stringPattern);
        } else if (isset($shopActualUrl) && $shopActualUrl!=null) {
            $url = $shopActualUrl;
        } else {
            $url = HTTP_PATH_LOCALE.$shopPermalink;
        }
        return $url;
    }

    public static function replaceSubidByStringPattern($refUrl, $subidFlag, $subid, $stringPattern)
    {
        if ($subidFlag == true && $stringPattern != "") {
            $url = preg_replace("/".$stringPattern."/", $subid, $refUrl);
            if ($url == null) {
                $url = $refUrl;
            }
        } else {
            $url = $refUrl;
            $url .= $subid;
        }
        return $url;
    }
}

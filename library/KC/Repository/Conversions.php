<?php
namespace KC\Repository;

class Conversions extends \KC\Entity\Conversions
{
    public static function getConversionInformationById($id)
    {
        $conversionInfo = array();
        
        if (is_numeric($id)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $offerOrShopId = self::getConversionOfferOrShopId($id);
            if (empty($offerOrShopId)) {
                return $conversionInfo;
            }

            if (!empty($offerOrShopId) && $offerOrShopId[0]['offerId'] != '') {
                $conversionInfo = $queryBuilder->select('c,o,s,cat,category')
                    ->from("KC\Entity\Conversions", "c")
                    ->leftJoin("c.offer", "o")
                    ->leftJoin("o.shopOffers", "s")
                    ->leftJoin("o.categoryoffres", "cat")
                    ->leftJoin('cat.categories', 'category');
            } else {
                $conversionInfo = $queryBuilder->select('c,s,cat,category')
                    ->from("KC\Entity\Conversions", "c")
                    ->leftJoin("c.shop", "s")
                    ->leftJoin("s.categoryshops", "cat")
                    ->leftJoin("cat.shop", "category");
            }
            
            $conversionInfo = $conversionInfo->where('c.id = '. $id)
                ->getQuery()
                ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }

        if (!empty($conversionInfo['offer'])) {
            $conversionInfo = self::traverseConversionInfoForOffers($conversionInfo);
        } else {
            $conversionInfo = self::traverseConversionInfoForShops($conversionInfo);
        }

        return $conversionInfo;
    }

    public static function traverseConversionInfoForOffers($conversionInfo)
    {
        $conversionDetails = array();
        if (!empty($conversionInfo['offer'])) {
            $conversionCategory = array();

            if (isset($conversionInfo['offer']['categoryoffres'])) {
                foreach ($conversionInfo['offer']['categoryoffres'] as $key => $category) {
                    $conversionCategory[$key]['id'] = $category['categories']['id'];
                    $conversionCategory[$key]['categoryName'] = $category['categories']['name'];
                }
            }

            if (!empty($conversionInfo)) {
                $conversionDetails = array(
                    "shopName" => $conversionInfo['offer']['shopOffers']['name'],
                    "offerTitle" => $conversionInfo['offer']['title'],
                    "category" => $conversionCategory
                );
            }
        }
        return $conversionDetails;
    }

    public static function traverseConversionInfoForShops($conversionInfo)
    {
        $conversionDetails = array();
        if (!empty($conversionInfo['shop'])) {
            $conversionCategory = array();

            if (isset($conversionInfo['shop']['categoryshops'])) {
                foreach ($conversionInfo['shop']['categoryshops'] as $key => $category) {
                    $conversionCategory[$key]['id'] = $category['shop']['id'];
                    $conversionCategory[$key]['categoryName'] = $category['shop']['name'];
                }
            }

            if (!empty($conversionInfo)) {
                $conversionDetails = array(
                    "shopName" => $conversionInfo['shop']['name'],
                    "category" => $conversionCategory
                );
            }
        }
        return $conversionDetails;
    }

    public static function getConversionOfferOrShopId($id)
    {
        $conversionInfo = array();
        
        if (is_numeric($id)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $conversionInfo = $queryBuilder->select('IDENTITY(c.shop) as shopId, IDENTITY(c.offer) as offerId')
                ->from("KC\Entity\Conversions", "c")
                ->where('c.id = '. $id)
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $conversionInfo;
    }


    public static function addConversion($id, $clickoutType)
    {
        $conversionId = '';
        $clientIP = ip2long(\FrontEnd_Helper_viewHelper::getRealIpAddress());

        if ($clickoutType === 'offer') {
            $clickout = new \FrontEnd_Helper_ClickoutFunctions($id, null);
        } else {
            $clickout = new \FrontEnd_Helper_ClickoutFunctions(null, $id);
        }

        $hasNetwork = $clickout->checkIfShopHasAffliateNetwork();
        if ($hasNetwork) {
            $conversionInfo = self::checkIfConversionExists($id, $clientIP, $clickoutType);
            $conversionId = $conversionInfo['id'];

            if (!isset($conversionInfo['exists'])) {
                $conversionId = self::addNewConversion($id, $clientIP, $clickoutType);
            }
        }
        return $conversionId;
    }

    private static function checkIfConversionExists($id, $clientIP, $clickoutType)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $conversionInfo = $queryBuilder
            ->select('count(c.id) as exists, c.id')
            ->from("KC\Entity\Conversions", "c");
        if ($clickoutType === 'offer') {
            $conversionInfo = $conversionInfo->where('c.offerId="'.$id.'"');
        } else {
            $conversionInfo = $conversionInfo->where('c.shopId="'.$id.'"');
        }
    
        $conversionInfo = $conversionInfo
            ->andWhere('c.IP="'.$clientIP.'"')
            ->andWhere("c.converted=0")
            ->groupBy('c.id')->getQuery()->getSingleResult(null, Doctrine::HYDRATE_ARRAY);
        return $conversionInfo;
    }

    private static function addNewConversion($id, $clientIP, $clickoutType)
    {
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $conversion = new KC\Entity\Conversions();

        if ($clickoutType === 'offer') {
            $conversion->offerId = $id;
        } else {
            $conversion->shopId = $id;
        }
        
        $conversion->IP = $clientIP;
        $entityManagerLocale->persist($conversion);
        $entityManagerLocale->flush();
        return $conversion->id;
    }
}

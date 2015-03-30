<?php
namespace KC\Repository;

class Conversions extends \KC\Entity\Conversions
{
    public static function getConversionId($id, $ip, $type = 'offer')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select("c.id, c.subid")
            ->from("KC\Entity\Conversions", "c")
            ->where("c.IP = ". $ip);
        if ($type == 'offer') {
            $query->andWhere("c.offer= ". $id);
        } else {
            $query->andWhere("c.shop= ". $id);
        }
        return $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public static function updateConverted($subId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $queryBuilder
            ->update('KC\Entity\Conversions', 'c')
            ->set('c.converted', 1)
            ->where('c.subid = '. $subId)
            ->getQuery()
            ->execute();
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_conversion_details');
        return true;
    }

    public static function getConversionDetail($subId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        return  $queryBuilder->select('c.subid,c.utma,c.utmz,o.id,s.id,n')
            ->from("KC\Entity\Conversions", "c")
            ->leftJoin("c.offer", "o")
            ->leftJoin("o.shop", "s")
            ->leftJoin("s.affliatenetwork", "n")
            ->where('subid = '. $subId)
            ->getQuery()
            ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

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
}

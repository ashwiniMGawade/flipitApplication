<?php
namespace KC\Repository;

class Shop extends \KC\Entity\Shop
{

     ##################################################################################
     ################## REFACTORED CODE ###############################################
     ##################################################################################

    public function __contruct($connectionName = "")
    {
        if (!$connectionName) {
            $connectionName = "doctrine_site";
        }
        Doctrine_Manager::getInstance()->bindComponent($connectionName, $connectionName);
    }

    public static function checkShop($shopName)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopName = mysqli_real_escape_string(\FrontEnd_Helper_viewHelper::getDbConnectionDetails(), $shopName);
        $shopName = html_entity_decode($shopName);
        $shopExist = $queryBuilder->select('s.id')
            ->from('KC\Entity\Shop', 's')
            ->where($queryBuilder->expr()->like("s.name", $queryBuilder->expr()->literal('%'.$shopName)))
            ->andWhere("s.deleted = 0")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return isset($shopExist[0]['id']) ? $shopExist[0]['id'] : '';
    }

    public static function getShopData($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s')
            ->from('KC\Entity\Shop', 's')
            ->setParameter(1, $id)
            ->where('s.id = ?1');
        $shopDataExistOrNot = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopDataExistOrNot;
    }

    public static function returnShopCategories($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDateAndTime = date('Y-m-d H:i:s');
        $query = $queryBuilder->select('r')
            ->from('KC\Entity\RefShopCategory', 'r')
            ->setParameter(1, $shopId)
            ->where('r.category = ?1');

        $categoryData = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $categories = array();

        foreach ($categoryData as $category) {
            $categories[] = $category['categoryId'];
        }
        return $categories;
    }

    public static function getPopularStoresForMemeberPortal($limit, $shopId = null)
    {
        $currentDate = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('s.id, s.name, s.permaLink, img.path as imgpath, img.name as imgname')
        ->from('KC\Entity\PopularShop', 'p')
        ->addSelect(
            "(SELECT COUNT(active.id) FROM KC\Entity\Offer active WHERE
            (active.shopOffers = s.id AND active.endDate >= '$currentDate' 
                AND active.deleted = 0
            )
            ) as activeCount"
        )
        ->leftJoin('p.popularshops', 's')
        ->leftJoin('s.logo', 'img')
        ->where('s.deleted = 0')
        ->andWhere('s.status = 1')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults($limit);
        $popularStoreData = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularStoreData;
    }

    public static function getSimilarShops($shopId, $numberOfShops = 12)
    {
        $similarShops = self::getSimilarShopsByShopId($shopId, $numberOfShops);
        if (count($similarShops) <= $numberOfShops) {
            $topCategoryShops = self::getSimilarShopsBySimilarCategories($shopId, $numberOfShops);
            $similarShops = self::removeDuplicateShops($similarShops, $topCategoryShops, $numberOfShops);
        }
        return $similarShops;
    }

    public static function getSimilarShopsByShopId($shopId, $numberOfShops = 12)
    {
        $similarShops = self::getRelatedShop($shopId);
        return self::setDataAsPerView($similarShops, $numberOfShops);
    }

    protected static function setDataAsPerView($similarShops, $numberOfShops)
    {
        $similarShopsWithoutDuplicate = array();
        if (!empty($similarShops['refShopRelatedshop'])) {
            foreach ($similarShops['refShopRelatedshop'] as $similarShop) {
                if (count($similarShopsWithoutDuplicate) <= $numberOfShops) {
                    $similarShopsWithoutDuplicate[$similarShop['relatedshops']['id']] = $similarShop['relatedshops'];
                }
            }
        }
        return $similarShopsWithoutDuplicate;
    }

    public static function getSimilarShopsBySimilarCategories($shopId, $numberOfShops)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select("cc.id as categoryId, cc.name as categoryName, cc.permaLink as categoryPermalink")
            ->from('KC\Entity\Shop', 's')
            ->where("s.id = ".$shopId)
            ->leftJoin('s.categoryshops', 'c')
            ->leftJoin('c.shop', 'cc');
        $similarShopsBySimilarCategories = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $topCategoryShops = self::getTopCategorySimilarShops($similarShopsBySimilarCategories);
        return $topCategoryShops;
    }

    public static function getTopCategorySimilarShops($similarShopsBySimilarCategories)
    {
        $topCategory = array_slice($similarShopsBySimilarCategories, 0, 1);
        $topCategoryId = $topCategory[0]['categoryId'];
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select("rsc, s, img")
            ->from('KC\Entity\RefShopCategory', 'rsc')
            ->leftJoin('rsc.shop', 'c')
            ->leftJoin('rsc.category', 's')
            ->leftJoin('s.logo', 'img')
            ->where('c.id ='.$topCategoryId)
            ->andWhere('s.status = 1')
            ->andWhere('s.deleted = 0');
        $relatedCategoryShops = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $relatedCategoryShops;
    }

    protected static function removeDuplicateShops($similarShops, $topCategoryShops, $numberOfShops)
    {
        foreach ($topCategoryShops as $relatedCategoryShop) {
            if (count($similarShops) <= $numberOfShops &&
                    !in_array($relatedCategoryShop['shopId'], $similarShops)) {
                $similarShops[$relatedCategoryShop['shopId']] = $relatedCategoryShop;
            }
        }
        return $similarShops;
    }

    public static function getPopularStores($limit, $shopId = null)
    {
        $currentDate = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p, s, img')
            ->from('KC\Entity\PopularShop', 'p')
            /*->addSelect(
                "(SELECT COUNT(exclusive) FROM KC\Entity\Offer exclusive WHERE exclusive.shopOffers = s.id AND
                (o.exclusiveCode =1 AND exclusive.endDate > '$currentDate')) as exclusiveCount"
            )
            ->addSelect("(SELECT COUNT(pc) FROM KC\Entity\PopularCode pc WHERE pc.popularcode = o.id ) as popularCount")
            ->addSelect(
                "(SELECT COUNT(active) FROM KC\Entity\Offer active WHERE
                (active.shopOffers = s.id AND active.endDate >= '$currentDate' 
                    AND active.deleted = 0
                )
                ) as activeCount"
            )*/
            ->leftJoin('p.popularshops', 's')
            //->leftJoin('s.offer', 'o')
            ->leftJoin('s.logo', 'img')
            ->where('s.deleted= 0')
            ->andWhere('s.status= 1')
            ->orderBy('p.position', 'ASC');
        
        if ($shopId) {
            $query = $query->andWhere("s.id =".$shopId);
        } else {
            $query = $query->setMaxResults($limit);
        }
        $popularStoreData = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularStoreData;
    }

    public static function getPopularStoresForHomePage($limit)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $popularStoresForHomePage = $queryBuilder->select('p.id,s.name,s.permaLink, img.path as imgpath, img.name as imgname')
            ->from('KC\Entity\PopularShop', 'p')
            ->leftJoin('p.popularshops', 's')
            ->leftJoin('s.logo', 'img')
            ->where('s.deleted=0')
            ->andWhere('s.status=1')
            ->orderBy('p.position', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularStoresForHomePage;
    }

    public static function getPopularStoresForDropDown($limit)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $popularStoreData = $queryBuilder->select('p.id, s.name, s.permaLink')
        ->from('KC\Entity\PopularShop', 'p')
        ->leftJoin('p.popularshops', 's')
        ->where('s.deleted=0')
        ->andWhere('s.status=1')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults($limit);
        $popularStoreData = $popularStoreData->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularStoreData;
    }

    public static function getStoreDetails($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s,img,small,big')
        ->from('KC\Entity\Shop', 's')
        ->leftJoin('s.logo', 'img')
        ->leftJoin('s.howtousesmallimage', 'small')
        ->leftJoin('s.howtousebigimage', 'big')
        ->leftJoin('s.affliatenetwork', 'aff')
        //->leftJoin('s.screenshot', 'scr')
        ->setParameter(1, $shopId)
        ->where('s.id= ?1')
        ->setParameter(2, '0')
        ->andWhere('s.deleted= ?2')
        ->setParameter(3, '1')
        ->andWhere('s.status= ?3');
        $allStoresDetail = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $allStoresDetail;
    }

    public static function getStoreDetailsForStorePage($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $storeDetail = $queryBuilder
            ->select('s, img')
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.logo', 'img')
            ->where('s.id='.$shopId)
            ->andWhere('s.deleted=0')
            ->andWhere('s.status=1');
        $allStoresDetail = $storeDetail->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $allStoresDetail;
    }

    public static function getallStoresForFrontEnd($startCharacter, $endCharacter)
    {
        $endingCharacter = $endCharacter;
        $nextCharacter = ++$endingCharacter;
        if (strlen($nextCharacter) > 1) {
            $nextCharacter = $nextCharacter[0];
            if ($endCharacter=='z') {
                $nextCharacter = $endCharacter;
            }
        }
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $query = $queryBuilder->select('o.id,s.id, s.name, s.permaLink')
        ->from('KC\Entity\Shop', 's')
        ->addSelect(
            "(SELECT COUNT(exclusive) FROM KC\Entity\Offer exclusive WHERE exclusive.shopOffers = s.id AND
                (o.exclusiveCode=1 AND o.endDate > '$currentDateAndTime')) as exclusiveCount"
        )
        ->addSelect("(SELECT COUNT(p.id) FROM KC\Entity\PopularCode p WHERE p.popularcode = o.id ) as popularCount")
        ->leftJoin('s.offer', 'o')
        ->leftJoin('s.logo', 'img')
        ->where('s.deleted= 0')
        ->andWhere('s.status= 1')
        ->andWhere(
            $queryBuilder->expr()->between(
                "s.name",
                $queryBuilder->expr()->literal($startCharacter),
                $queryBuilder->expr()->literal($nextCharacter)
            )
        );
        
        $query->orderBy('s.name');
        $storeInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
 
        $storesForFrontend =array();
        foreach ($storeInformation as $store) {
            if ($store['name']!='' && $store['name']!=null) {
                $FirstCharacter = strtoupper(self::filterFirstCharacter($store['name']));
                if (preg_match_all('/[0-9]/', $FirstCharacter, $characterMatch)) {
                    if (intval($characterMatch[0][0]) >=0) {
                        $FirstCharacter = 'abc';
                    }
                }

                $storesForFrontend[$FirstCharacter][$store['id']] =
                array("id"=>$store['id'],
                    "permaLink"=>$store['permaLink'],
                    "name"=>$store['name'],
                    "exclusive"=>$store['exclusiveCount'],
                    "inpopular"=>$store['popularCount']);
            }
        }
        return $storesForFrontend;
    }

    public static function getAllPopularStores($limit)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p, s, img')
        ->from('KC\Entity\PopularShop', 'p')
        ->leftJoin('p.popularshops', 's')
        ->leftJoin('s.logo', 'img')
        ->setParameter(1, '0')
        ->where('s.deleted= ?1')
        ->setParameter(2, '1')
        ->andWhere('s.status= ?2')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults($limit);
        $popularStores = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularStores;
    }

    public static function getAllPopularStoresForSidebarWidget($limit)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $popularStores = $queryBuilder->select('p.id,s.name,s.permaLink, s.deepLink, s.refUrl, s.actualUrl')
        ->from('KC\Entity\PopularShop', 'p')
        ->leftJoin('p.popularshops', 's')
        ->where('s.deleted=0')
        ->andWhere('s.status=1')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularStores;
    }

    public static function getshopDetails($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('s,img.name,img.path,chptr')
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('s.howtochapter', 'chptr')
            ->Where("s.id= ".$shopId)
            ->andWhere('s.status = 1');
        $shopDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopDetails;
    }

    public static function getShopsByShopIds($shopIds)
    {
        $shopsInformation = '';
        if (!empty($shopIds)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('s.id, s.name,s.permaLink, img.path as imgpath, img.name as imgname')
                ->from('KC\Entity\Shop', 's')
                ->leftJoin('s.logo', 'img')
                ->where('s.deleted=0')
                ->andWhere($queryBuilder->expr()->in('s.id', $shopIds))
                ->orderBy("s.name", "ASC");
            $shopsInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $shopsInformation;
    }

    public static function getStoresForSearchByKeyword($searchedKeyword, $limit, $fromPage = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDate = date('Y-m-d 00:00:00');
        $query = $queryBuilder->select(
            "s.name as name,s.permaLink,s.id as id,
            l.path as imgpath, l.name as imgname"
        )
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.logo', 'l')
            ->where('s.deleted=0')
            ->andWhere('s.status=1')
            ->andWhere($queryBuilder->expr()->like("s.name", $queryBuilder->expr()->literal("%". $searchedKeyword."%")));
        if ($fromPage!='') {
            $query = $query->addSelect(
                "(SELECT COUNT(active.id) FROM KC\Entity\Offer active WHERE
                (active.shopOffers = s.id AND active.endDate >= '$currentDate' 
                    AND active.deleted = 0 AND active.discountType = 'CD'
                )
                ) as activeCount"
            )
            ->leftJoin('s.offer', 'o');
        }
        $query = $query->setMaxResults($limit);
        $stores = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $stores;
    }

    public static function shopAddInFavourite($visitorId, $shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $addedStatus = 0;
        if ($shopId!='') {
            $favouriteShops  = $queryBuilder->select('s')
                ->from('KC\Entity\FavoriteShop', 's')
                ->where('s.visitor ='.$visitorId)
                ->andWhere('s.shop ='.$shopId)
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if ($favouriteShops) {
                $queryBuilder->delete('KC\Entity\FavoriteShop', 'fs')
                    ->where("fs.shop =" . $shopId)
                    ->andWhere('fs.visitor ='.$visitorId)
                    ->getQuery()
                    ->execute();
                $addedStatus = 1;
            } else {
                $favouriteShops = new \KC\Entity\FavoriteShop();
                $favouriteShops->visitor = \Zend_Registry::get('emLocale')->find('KC\Entity\Visitor', $visitorId);
                $favouriteShops->shop = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $shopId);
                $favouriteShops->deleted = 0;
                $favouriteShops->created_at = new \DateTime('now');
                $favouriteShops->updated_at = new \DateTime('now');
                \Zend_Registry::get('emLocale')->persist($favouriteShops);
                \Zend_Registry::get('emLocale')->flush();
            }
        
            $shopName = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $shopId);
            $cacheKeyShopDetails = 'shopDetails_'  . $shopId . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKeyShopDetails);
            $cacheKeyOfferDetails = 'offerDetails_'  . $shopId . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKeyOfferDetails);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_'.$visitorId.'_favouriteShops');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('visitor_'.$visitorId.'_favouriteShopOffers');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('visitor_'.$visitorId.'_details');
            $key = 'shop_similarShopsAndSimilarCategoriesOffers'. $shopId . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            return array('shop' => $shopName->name, 'flag' => $addedStatus);
        }
        return;
    }

    public static function getActiveOffersCount($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDate = date('Y-m-d 00:00:00');
        $acitveOfferCount = $queryBuilder->select('count(o.id) as activeCount')
            ->from("KC\Entity\Shop", "s")
            ->leftJoin('s.offer', 'o')
            ->where('s.id='.$shopId)
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.deleted=0')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $acitveOfferCount;
    }

    public static function getShopName($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shop = $queryBuilder->select('s.name')
            ->from("KC\Entity\Shop", "s")
            ->where('s.id='.$shopId)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return isset($shop[0]['name']) ? $shop[0]['name'] : '';
    }

    public static function getShopLightBoxText($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shop = $queryBuilder
            ->select('s.lightboxfirsttext, s.lightboxsecondtext')
            ->from('KC\Entity\Shop', 's')
            ->where('s.id='.$shopId)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shop[0];

    }

    public static function getShopLogoByShopId($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopsInformation = $queryBuilder
            ->select('s.permaLink, img.path, img.name')
            ->from("KC\Entity\Shop", "s")
            ->leftJoin("s.logo", "img")
            ->where('s.deleted = 0')
            ->andWhere("s.id =".$shopId)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return !empty($shopsInformation) ? $shopsInformation[0] : '';
    }

    public static function getShopInformation($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shop = $queryBuilder
            ->select('s.permaLink,s.name')
            ->from('KC\Entity\Shop', 's')
            ->where('s.id='.$shopId)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shop;
    }

    public static function getShopIdByPermalink($permalink)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shop = $queryBuilder
            ->select('s.id')
            ->from("KC\Entity\Shop", "s")
            ->where('s.permaLink='."'$permalink'")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return isset($shop[0]) ? $shop[0]['id'] : '';
    }

    public static function getRelatedShop($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $relatedShops = $queryBuilder
            ->select('s.id, rf.id as rid, rl.permaLink, rl.id as rld, rl.name')
            ->from("KC\Entity\Shop", "s")
            ->leftJoin("s.relatedshops", "rf")
            ->leftJoin("rf.shop", "rl")
            ->leftJoin("s.logo", "logo")
            ->where("s.id =".$id)
            ->andWhere('rl.status = 1')
            ->andWhere('rl.deleted = 0')
            ->orderBy("rf.position", "ASC")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $relatedShops;
    }

    public static function getAllActiveShopDetails ()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopIds = $queryBuilder
            ->select('s.id')
            ->from("KC\Entity\Shop", "s")
            ->where('s.deleted = 0')
            ->andWhere('s.status = 1')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopIds;
    }

    public static function updateSimilarShopsViewedIds()
    {
        $shopIds = self::getAllActiveShopDetails();
        $similarShopIds = '';
        foreach ($shopIds as $shopId) {
            $topFiveSimilarShopsViewed = '';
            $commaSepratedShopIds = '';
            $topFiveSimilarShopsViewed = self::getSimilarShopsForAlsoViewedWidget($shopId['id']);
            $commaSepratedShopIds = implode(',', $topFiveSimilarShopsViewed);
            self::updateShopViewedIds($commaSepratedShopIds, $shopId['id']);
        }
        return true;
    }

    public static function getSimilarShopsForAlsoViewedWidget($shopId)
    {
        $similarShops = array();
        $similarShopsBySimilarCategories[] = self::getSimilarShopsBySimilarCategories($shopId, 5);
        if (isset($similarShopsBySimilarCategories[0][0]['category'])) {
            foreach ($similarShopsBySimilarCategories[0][0]['category'] as $category) {
                foreach ($category['shop'] as $relatedCategoryShop) {
                    if ($relatedCategoryShop['id'] != $shopId) {
                        $similarShops[$relatedCategoryShop['id']] = $relatedCategoryShop['id'];
                    }
                }
            }
        }
        $topFiveSimilarShopsViewed = array_slice($similarShops, 0, 5);
        return $topFiveSimilarShopsViewed;
    }

    public static function updateShopViewedIds($commaSepratedShopIds, $shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $queryBuilder->update('KC\Entity\Shop', 's')
        ->set('s.shopsViewedIds', "'$commaSepratedShopIds'")
        ->where('s.id ='.$shopId)
        ->getQuery()->execute();
    }

    public static function getShopsAlsoViewed($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopAlsoViewedIds = $queryBuilder
            ->select('s.shopsViewedIds')
            ->from("KC\Entity\Shop", "s")
            ->where('s.id ='.$shopId)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopAlsoViewedIds;
    }


    ##################################################################################
    ################## END REFACTORED CODE ###########################################
    ##################################################################################

    public static function addChain($chainItemId, $shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $queryBuilder->update('KC\Entity\Shop', 's')
        ->set('s.chainItemId', $chainItemId)
        ->where('s.id ='.$shopId)
        ->getQuery()->execute();
    }

    public function getAllShopNames($keyword)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        return $queryBuilder->select('s.name,s.permaLink,s.id')
            ->from("KC\Entity\Shop", "s")
            ->where('s.deleted ='. 0)
            ->andWhere("s.status=1")
            ->andWhere($queryBuilder->expr()->like("s.name", $queryBuilder->expr()->literal($keyword."%")))
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public static function getshopList($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $srh = $params["searchText"]=='undefined' ? '' : $params["searchText"];
        $flag = @$params['flag'];

        $shopList = $queryBuilder
            ->from("KC\Entity\Shop", "s")
            ->leftJoin('s.affliatenetwork', 'a')
            ->where('s.deleted = '. $flag);
        if (!empty($srh)) {
            $shopList->andWhere($queryBuilder->expr()->like("s.name", $queryBuilder->expr()->literal("%".$srh."%")));
        }
            
        $request = \DataTable_Helper::createSearchRequest(
            $params,
            array('s.id', 's.name', 's.permaLink', 's.affliateProgram', 's.created_at',
                's.lastSevendayClickouts', 's.shopAndOfferClickouts','a.name',
                's.discussions', 's.showSignupOption', 's.status',
                's.offlineSicne'
            )
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($shopList)
            ->add('number', 's.id')
            ->add('text', 's.name')
            ->add('text', 's.permaLink')
            ->add('text', 's.affliateProgram')
            ->add('number', 's.created_at')
            ->add('number', 's.lastSevendayClickouts')
            ->add('number', 's.shopAndOfferClickouts')
            ->add('text', 'a.name')
            ->add('text', 's.discussions')
            ->add('text', 's.showSignupOption')
            ->add('text', 's.status')
            ->add('text', 's.offlineSicne');
        $result = $builder->getTable()->getResponseArray();
        return $result;
    }

    public static function gettrashshopList($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $srh = $params["searchText"]=='undefined' ? '' : $params["searchText"];
        $flag = @$params['flag'];

        $shopList = $queryBuilder
            ->from("KC\Entity\Shop", "s")
            ->leftJoin('s.affliatenetwork', 'a')
            ->where('s.deleted = '. $flag);
        if (!empty($srh)) {
            $shopList->andWhere($queryBuilder->expr()->like("s.name", $queryBuilder->expr()->literal("%".$srh."%")));
        }
            
        $request = \DataTable_Helper::createSearchRequest(
            $params,
            array('s.id', 's.name', 's.affliateProgram', 's.created_at', 'a.name')
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($shopList)
            ->add('number', 's.id')
            ->add('text', 's.name')
            ->add('text', 's.affliateProgram')
            ->add('number', 's.created_at')
            ->add('text', 'a.name');
        $result = $builder->getTable()->getResponseArray();
        return $result;
    }

    public static function moveToTrash($id)
    {
        if ($id) {
            $u =  \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $id);
            $u->deleted = 1;
            \Zend_Registry::get('emLocale')->persist($u);
            \Zend_Registry::get('emLocale')->flush();
        } else {
            $id = null;
        }
        //call cache function
        $key = 'shop_similar_shops';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvouchercode_list_shoppage');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allCategoriesOf_shoppage_'. $id);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');
        return $id;
    }

    public static function permanentDeleteShop($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $shopInfo = $queryBuilder->select('shp')
                ->from('KC\Entity\Shop', 'shp')
                ->where("shp.id=" . $id)
                ->getQuery()
                ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $shopInfo = (object) $shopInfo;
            if ($shopInfo->deleted == 1) {
                $refShopCategoryQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $refShopCategoryQueryBuilder->delete('KC\Entity\refShopCategory', 'r')
                    ->where("r.category=" . $id)
                    ->getQuery()->execute();

                $PopularShopQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $PopularShopQueryBuilder->delete('KC\Entity\PopularShop', 'p')
                    ->where("p.popularshops=" . $id)
                    ->getQuery()->execute();

                $RefArticleStoreQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $RefArticleStoreQueryBuilder->delete('KC\Entity\RefArticleStore', 'rp')
                    ->where("rp.articleshops=" . $id)
                    ->getQuery()->execute();

                $OfferQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $offer = $OfferQueryBuilder->select('o')
                    ->from('KC\Entity\Offer', 'o')
                    ->where("o.shopOffers=" . $id)
                    ->getQuery()
                    ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                foreach ($offer as $off) {
                    \KC\Repository\Offer::deleteOffer($off['id']);
                }

                $OfferNewsQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $OfferNewsQueryBuilder->delete('KC\Entity\OfferNews', 'ofn')
                    ->where("ofn.shop=" . $id)
                    ->getQuery()->execute();

                $RoutePermalinkQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $RoutePermalinkQueryBuilder->delete('KC\Entity\RoutePermalink', 'route')
                    ->where($queryBuilder->expr()->eq("route.permalink", $queryBuilder->expr()->literal($shopInfo->permaLink)))
                    ->getQuery()->execute();
               
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allCategoriesOf_shoppage_'. $id);
                $key = 'shop_similarShopsAndSimilarCategoriesOffers'. $id . '_list';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                //call cache function
                $key = 'shop_similar_shops';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                $key = 'store_'.$id.'_howToGuide';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_storesHeader_image');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('12_popularShops_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');

                # update chain if shop is associated with chain
                if ($shopInfo->chainItemId) {
                    $RoutePermalinkQueryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
                    $query = $RoutePermalinkQueryBuilder->delete('KC\Entity\ChainItem', 'ct')
                        ->where("ct.chainItem=" . $shopInfo->chainId)
                        ->where("ct.shopId=" . $shopInfo->id)
                        ->getQuery()->execute();
                }
                
                //call cache function
                $key = 'shop_similar_shops';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                $cacheKey = \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($shopInfo->permaLink);
                $key = 'store_'.$cacheKey.'_howToGuide';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('12_popularShops_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');
            }

            $shopQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $shopQueryBuilder->delete('KC\Entity\Shop', 's')
                ->where("s.id=" . $id)
                ->getQuery()->execute();
            return true;
        }
        return false;
    }

    public static function restoreShop($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();

        if ($id) {
            $shop =  \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $id);
            $shop->deleted = 0;
            \Zend_Registry::get('emLocale')->persist($shop);
            \Zend_Registry::get('emLocale')->flush();
            return $id;
        } else {
            return null;
        }
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categories_of_shoppage');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('12_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');
        return $id;
    }

    public static function searchKeyword($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder->select('s.name as name')
            ->from('KC\Entity\Shop', 's')
            ->where('s.deleted= '. $flag)
            ->andWhere($queryBuilder->expr()->like("s.name", $queryBuilder->expr()->literal("%".$keyword."%")))
            ->orderBy("s.name", "ASC")
            ->setMaxResults(5)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function searchsimilarStore($keyword, $flag, $selctedshop)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder->select('s.name as name,s.id as id')
            ->from('KC\Entity\Shop', 's')
            ->where('s.deleted= '. $flag)
            ->andWhere($queryBuilder->expr()->like("s.name", $queryBuilder->expr()->literal($keyword."%")))
            ->andWhere("s.id NOT IN ($selctedshop)")
            ->orderBy("s.name", "ASC")
            ->setMaxResults(10)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public function CreateNewShop($shopDetail)
    {//echo "<pre>";print_r($shopDetail);die;
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        if (!empty($shopDetail['id'])) {
            $shopInfo = \Zend_Registry::get('emLocale')
                ->getRepository('KC\Entity\Shop')
                ->find($shopDetail['id']);
            $shopInfo->created_at = $shopInfo->created_at;
        } else {
            $shopInfo = new \Kc\Entity\Shop();
            $shopInfo->created_at = new \DateTime('now');
        }
        $shopInfo->deleted = 0;
        $shopInfo->updated_at = new \DateTime('now');
        $shopInfo->addtosearch = 0;
        $shopInfo->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopName']);
        $shopInfo->permaLink = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopNavUrl']);
        $shopInfo->metaDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopMetaDescription']);
        $shopInfo->notes =\BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopNotes']);
    #   $shopInfo->deepLink =\BackEnd_Helper_viewHelper::stripSlashesFromString (@$shopDetail['shopDeepLinkUrl']);
    #   $shopInfo->deepLinkStatus =\BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['deepLinkStatus']);
        $shopInfo->refUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopRefUrl']);
        $shopInfo->actualUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopActualUrl']);
        $shopInfo->affliateProgram = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['affiliateProgStatus']);
        $shopInfo->title =\BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopTitle']);
        $shopInfo->subTitle =\BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopSubTitle']);
        $shopInfo->overriteTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopOverwriteTitle']);
        $shopInfo->shopText = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopDescription']);
        $shopInfo->customtext = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopCustomText']);
        $shopInfo->moretextforshop = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['moretextforshop']);
        $shopViewCount = isset($shopDetail['shopViewCount']) ? $shopDetail['shopViewCount'] : '0';
        $shopInfo->views = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopViewCount);
        $shopInfo->howtoTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pageTitle']);
        $shopInfo->howtoSubtitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pageSubTitle']);
        $shopInfo->howtoSubSubTitle = \FrontEnd_Helper_viewHelper::sanitize(
            \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pageSubSubTitle'])
        );
        $shopInfo->howtoguideslug = \FrontEnd_Helper_viewHelper::sanitize($shopDetail['howToPageSlug']);
        $shopInfo->howtoMetaTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pagemetaTitle']);
        $shopInfo->howtoMetaDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pagemetaDesc']);
        $shopInfo->customHeader = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopCustomHeader']);
        $shopInfo->howToIntroductionText = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['howToIntroductionText']);
        $shopInfo->showSimliarShops = \BackEnd_Helper_viewHelper::stripSlashesFromString(
            !empty($shopDetail['similarShops']) ? $shopDetail['similarShops'] : '0'
        );
        $showChains = !empty($shopDetail['showChains']) ? $shopDetail['showChains'] : '0';
        $shopInfo->showChains = \BackEnd_Helper_viewHelper::stripSlashesFromString($showChains);
        $strictConfirmation = !empty($shopDetail['strictConfirmation']) ? $shopDetail['strictConfirmation'] : '0';
        $shopInfo->strictConfirmation = \BackEnd_Helper_viewHelper::stripSlashesFromString($strictConfirmation);
        // shop extra properties
        $displayExtraProperties = !empty($shopDetail['displayExtraProperties']) ? $shopDetail['displayExtraProperties'] : '0';
        $shopInfo->displayExtraProperties = \BackEnd_Helper_viewHelper::stripSlashesFromString($displayExtraProperties);
        # display signup option on store detail page
        $shopInfo->showSignupOption = \BackEnd_Helper_viewHelper::stripSlashesFromString(
            !empty($shopDetail['signupOption']) ? $shopDetail['signupOption'] : '0'
        );

        $shopInfo->lightboxfirsttext = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['lightboxfirsttext']);
        $shopInfo->lightboxsecondtext = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['lightboxsecondtext']);

        $shopInfo->futurecode = \BackEnd_Helper_viewHelper::stripSlashesFromString(
            !empty($shopDetail['futurecode']) ? $shopDetail['futurecode'] : '0'
        );

        if (\BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['displayExtraProperties'])) {
            $shopInfo->ideal = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['ideal']) ? $shopDetail['ideal'] : 0
            );
            $shopInfo->qShops = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['qShops']) ? $shopDetail['qShops'] : 0
            );
            $shopInfo->freeReturns = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['freeReturns']) ? $shopDetail['freeReturns'] : 0
            );
            $shopInfo->pickupPoints = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['pickupPoints']) ? $shopDetail['pickupPoints'] : 0
            );
            $shopInfo->mobileShop = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['mobileShop']) ? $shopDetail['mobileShop'] : 0
            );
            $shopInfo->service = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['service']) ? $shopDetail['service'] : 0
            );

            if (\BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['service'])) {
                $shopInfo->serviceNumber = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['serviceNumber']);
            }
        }

        $shopInfo->discussions = '0';

        if (isset($shopDetail['discussions'])) {
            $shopInfo->discussions = '1';
        }

        if ($shopDetail['customtextposition'] != '') {
            $shopInfo->customtextposition = $shopDetail['customtextposition'];
        }

        $shopInfo->showcustomtext = 0;
        if ($shopDetail['showcustomtext'] != '') {
            $shopInfo->showcustomtext = $shopDetail['showcustomtext'];
        }

        $shopInfo->usergenratedcontent = '0';

        if (isset($shopDetail['usergenratedchk'])) {
            $shopInfo->usergenratedcontent = '1';
        }

        if (isset($shopDetail['keywordlink'])) {
            $shopInfo->keywordlink = $shopDetail['keywordlink'];
        }


        if (isset($shopDetail['onlineStatus'])) {
            if ($shopDetail['onlineStatus'] == 1) {
                $shopInfo->status = 1;
                $shopInfo->offlineSicne = null;
            } else {
                $shopInfo->status = 0;
                if (strlen($shopDetail['offlineSince']) > 18) {
                    $shopInfo->offlineSicne = $shopDetail['offlineSince'];
                } else {
                    $shopInfo->offlineSicne = new \DateTime('now');
                }
            }
        } else {
            $shopInfo->status = 1 ;
        }

        $selectAccountManagers = isset($shopDetail['selectaccountmanagers']) ? $shopDetail['selectaccountmanagers'] : '0';
        $shopInfo->accoutManagerId = \BackEnd_Helper_viewHelper::stripSlashesFromString($selectAccountManagers);
        $shopInfo->accountManagerName = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['accountManagerName']);
        $shopInfo->contentManagerId = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['selecteditors']);
        $shopInfo->contentManagerName = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['editorName']);

        $shopInfo->affliateNetworkId = null;

        if ($shopDetail['shopAffiliateNetwork'] != 0) {
            $shopInfo->affliatenetwork = \Zend_Registry::get('emLocale')
                ->getRepository('KC\Entity\AffliateNetwork')
                ->find(
                    \BackEnd_Helper_viewHelper::stripSlashesFromString(
                        $shopDetail['shopAffiliateNetwork']
                    )
                );
        }

        $shopInfo->howToUse = $shopDetail['howTouseStatus'];

        if (intval($shopDetail['howTouseStatus']) > 0) {
            if (isset($shopDetail['shopHowToUsePageId'])) {
                $shopInfo->howtoUsepageId = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopHowToUsePageId']);
            }

            //  upload small logo image for how to use page
            if (isset($_FILES['smallLogoFile']['name']) && $_FILES['smallLogoFile']['name'] != '') {
                $uploadPath = UPLOAD_IMG_PATH . "shop/";
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0776, true);
                }

                $result = self::uploadImage('smallLogoFile', $uploadPath);
                if ($result['status'] == '200') {
                    $ext = \BackEnd_Helper_viewHelper::getImageExtension(
                        $result['fileName']
                    );
                    $howtousesmallimage =  new \KC\Entity\ImageHowToUseSmallImage();
                    $howtousesmallimage->ext = $ext;
                    $howtousesmallimage->path = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['path']);
                    $howtousesmallimage->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);
                    $howtousesmallimage->deleted = 0;
                    $howtousesmallimage->created_at = new \DateTime('now');
                    $howtousesmallimage->updated_at = new \DateTime('now');
                    \Zend_Registry::get('emLocale')->persist($howtousesmallimage);
                    \Zend_Registry::get('emLocale')->flush();
                    $shopInfo->howtousesmallimage = \Zend_Registry::get('emLocale')
                        ->getRepository('KC\Entity\ImageHowToUseSmallImage')
                        ->find($howtousesmallimage->id);
                } else {
                    return false;
                }
            }

            //  upload big logo image for how to use page
            if (isset($_FILES['bigLogoFile']['name']) && $_FILES['bigLogoFile']['name'] != '') {
                $uploadPath = UPLOAD_IMG_PATH . "shop/";
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0776, true);
                }
                $result = self::uploadImage('bigLogoFile', $uploadPath);

                if ($result['status'] == '200') {
                    $ext = \BackEnd_Helper_viewHelper::getImageExtension(
                        $result['fileName']
                    );
                    $howtousebigimage =  new \KC\Entity\ImageHowToUseBigImage();
                    $howtousebigimage->ext = $ext;
                    $howtousebigimage->path = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['path']);
                    $howtousebigimage->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);
                    $howtousebigimage->deleted = 0;
                    $howtousebigimage->created_at = new \DateTime('now');
                    $howtousebigimage->updated_at = new \DateTime('now');
                    \Zend_Registry::get('emLocale')->persist($howtousebigimage);
                    \Zend_Registry::get('emLocale')->flush();
                    $shopInfo->howtousebigimage = \Zend_Registry::get('emLocale')
                        ->getRepository('KC\Entity\ImageHowToUseBigImage')
                        ->find($howtousebigimage->id);
                } else {
                    return false;
                }
            }
        }
 
        if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {
            $uploadPath = UPLOAD_IMG_PATH . "shop/";
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0776, true);
            }
            $result = self::uploadImage('logoFile', $uploadPath);

            if ($result['status'] == '200') {
                $ext = \BackEnd_Helper_viewHelper::getImageExtension(
                    $result['fileName']
                );
                $logo =  new \KC\Entity\Logo();
                $logo->ext = $ext;
                $logo->path = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['path']);
                $logo->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);
                $logo->deleted = 0;
                $logo->created_at = new \DateTime('now');
                $logo->updated_at = new \DateTime('now');
                \Zend_Registry::get('emLocale')->persist($logo);
                \Zend_Registry::get('emLocale')->flush();
                $shopInfo->logo = \Zend_Registry::get('emLocale')
                    ->getRepository('KC\Entity\Logo')
                    ->find($logo->id);
            } else {
                return false;
            }
        }

        $shopInfo->screenshotId = 1;
        //call cache function
        $key = 'shop_similarShopsAndSimilarCategoriesOffers'. $shopInfo->id . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = '6_topOffers'  . $shopInfo->id . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shopDetails_'  . $shopInfo->id . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $cacheKeyOfferDetails = 'offerDetails_'  . $shopInfo->id . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKeyOfferDetails);
        $key = 'shop_similar_shops';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'store_'.$shopInfo->id.'_howToGuide';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allCategoriesOf_shoppage_'. $shopInfo->id);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('12_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');

        if (!empty($shopDetail['id'])) {
            $getcategory = $queryBuilder->select('s.permaLink, s.howtoguideslug')
                ->from('KC\Entity\Shop', 's')
                ->where('s.id = '.$shopDetail['id'])
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
               
        }

        if (!empty($getcategory[0]['permaLink'])) {
            $validatedShopRoute = \KC\Repository\RoutePermalink::validatePermalink($getcategory[0]['permaLink']);
            $categoryPermalink = !empty($getcategory[0]['permaLink']) ? $getcategory[0]['permaLink'] : '';
            $howToGuideSlug = !empty($getcategory[0]['howtoguideslug']) ? $getcategory[0]['howtoguideslug'] :'';
            $howToGuideValidatedLink = $categoryPermalink .'/'.$howToGuideSlug;
            $validatedHowToGuideRoute = \KC\Repository\RoutePermalink::validatePermalink($howToGuideValidatedLink);
            if (empty($validatedHowToGuideRoute)) {
                $howToGuideValidatedLink = 'how-to/'.$categoryPermalink;
                $validatedHowToGuideRoute = \KC\Repository\RoutePermalink::validatePermalink($howToGuideValidatedLink);
            }
        }

        try {
            \Zend_Registry::get('emLocale')->persist($shopInfo);
            \Zend_Registry::get('emLocale')->flush();

            if (!empty($shopDetail['selectedCategoryies'])) {
                if (!empty($shopDetail['id'])) {
                    $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                    $query = $queryBuilder->delete('KC\Entity\RefShopCategory', 'rf')
                        ->where("rf.shopId=" . $shopDetail['id'])
                        ->getQuery()->execute();
                }
                foreach ($shopDetail['selectedCategoryies'] as $key => $categories) {
                    $refShopCategory = new \KC\Entity\RefShopCategory();
                    $refShopCategory->created_at = new \DateTime('now');
                    $refShopCategory->updated_at = new \DateTime('now');
                    $refShopCategory->category = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $shopInfo->id);
                    $refShopCategory->shop = \Zend_Registry::get('emLocale')->find('KC\Entity\Category', $categories);
                    \Zend_Registry::get('emLocale')->persist($refShopCategory);
                    \Zend_Registry::get('emLocale')->flush();
                }
            }

            if (!empty($shopDetail['ballontextcontent'])) {
                if (isset($shopDetail['id']) && $shopDetail['id'] != '') {
                    $type = 'update';
                    $shopId = $shopDetail['id'];
                } else {
                    $type = 'add';
                    $shopId = $shopInfo->id;
                }

                self::saveEditorBallonText($shopDetail, $shopId, $type);
            }

            if (!empty($shopDetail['reasontitle1'])
                || !empty($shopDetail['reasontitle2'])
                || !empty($shopDetail['reasontitle3'])
                || !empty($shopDetail['reasontitle4'])
                || !empty($shopDetail['reasontitle5'])
                || !empty($shopDetail['reasontitle6'])
                ) {
                $shopReasons = array();
                $shopReasons['reasontitle1'] = !empty($shopDetail['reasontitle1'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasontitle1']) : '';
                $shopReasons['reasonsubtitle1'] = !empty($shopDetail['reasonsubtitle1'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasonsubtitle1']) : '';
                $shopReasons['reasontitle2'] = !empty($shopDetail['reasontitle2'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasontitle2']) : '';
                $shopReasons['reasonsubtitle2'] = !empty($shopDetail['reasonsubtitle2'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasonsubtitle2']) : '';
                $shopReasons['reasontitle3'] = !empty($shopDetail['reasontitle3'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasontitle3']) : '';
                $shopReasons['reasonsubtitle3'] = !empty($shopDetail['reasonsubtitle3'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasonsubtitle3']) : '';
                $shopReasons['reasontitle4'] = !empty($shopDetail['reasontitle4'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasontitle4']) : '';
                $shopReasons['reasonsubtitle4'] = !empty($shopDetail['reasonsubtitle4'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasonsubtitle4']) : '';
                $shopReasons['reasontitle5'] = !empty($shopDetail['reasontitle5'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasontitle5']) : '';
                $shopReasons['reasonsubtitle5'] = !empty($shopDetail['reasonsubtitle5'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasonsubtitle5']) : '';
                $shopReasons['reasontitle6'] = !empty($shopDetail['reasontitle6'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasontitle6']) : '';
                $shopReasons['reasonsubtitle6'] = !empty($shopDetail['reasonsubtitle6'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasonsubtitle6']) : '';
                \KC\Repository\ShopReasons::saveReasons($shopReasons, $shopInfo->id);
            }

            $key = 'shop_similar_shops';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $howToGuideExactLink = 'store/howtoguide/shopid/'.$shopInfo->id;
            $shopExactLink = 'store/storedetail/id/'.$shopInfo->id;
            $shopPermalink = \FrontEnd_Helper_viewHelper::sanitize(
                \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopNavUrl'])
            );
            if (!empty($shopDetail['howToPageSlug'])) {
                $howToGuidePermalink = $shopPermalink. "/".\FrontEnd_Helper_viewHelper::sanitize($shopDetail['howToPageSlug']);
            } else {
                $howToGuidePermalink = 'how-to/' . $shopPermalink;
            }
            if (!empty($validatedShopRoute)) {
                \KC\Repository\RoutePermalink::updateRoutePermalink($shopPermalink, $shopExactLink, $validatedShopRoute[0]['permalink']);
                if (!empty($validatedHowToGuideRoute)) {
                    \KC\Repository\RoutePermalink::updateRoutePermalink(
                        $howToGuidePermalink,
                        $howToGuideExactLink,
                        $validatedHowToGuideRoute[0]['permalink']
                    );
                } else {
                    \KC\Repository\RoutePermalink::saveRoutePermalink($howToGuidePermalink, $howToGuideExactLink);
                }
            } else {
                \KC\Repository\RoutePermalink::saveRoutePermalink($shopPermalink, $shopExactLink);
                \KC\Repository\RoutePermalink::saveRoutePermalink($howToGuidePermalink, $howToGuideExactLink);
            }

            if (isset($shopDetail['similarstoreord'])) {
                $similarstoreordArray = explode(',', $shopDetail['similarstoreord']);
                $i = 1;
                if (!empty($shopDetail['id'])) {
                    $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                    $query = $queryBuilder->delete('KC\Entity\RefShopRelatedshop', 'rsrs')
                        ->where("rsrs.shop=" . $shopDetail['id'])
                        ->getQuery()->execute();
                }
                foreach ($similarstoreordArray as $shop) {
                    if ($shop!='') {
                        $relateshopObj = new \KC\Entity\RefShopRelatedshop();
                        $relateshopObj->shop = \Zend_Registry::get('emLocale')
                            ->getRepository('KC\Entity\Shop')
                            ->find($shopInfo->id);
                        $relateshopObj->relatedshopId = $shop;
                        $relateshopObj->position = $i;
                        $relateshopObj->created_at = new \DateTime('now');
                        $relateshopObj->updated_at = new \DateTime('now');
                        \Zend_Registry::get('emLocale')->persist($relateshopObj);
                        \Zend_Registry::get('emLocale')->flush();
                        ++$i;
                    }
                }
            }
            if (!empty($shopDetail['title']) && !empty($shopDetail['content'])) {
                $delChapters = $queryBuilder
                    ->delete("KC\Entity\ShopHowToChapter", "sh")
                    ->where("sh.shop = ".$shopInfo->id)
                    ->getQuery()
                    ->execute();
                foreach ($shopDetail['title'] as $key => $title) {
                    if (!empty($shopDetail['title'][$key]) && !empty($shopDetail['content'][$key])) {
                        $chapter = new \KC\Entity\ShopHowToChapter();
                        $chapter->shop = \Zend_Registry::get('emLocale')
                            ->getRepository('KC\Entity\Shop')
                            ->find($shopInfo->id);
                        $chapter->chapterTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['title'][$key]);
                        $chapter->chapterDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['content'][$key]) ;
                        $chapter->created_at = new \DateTime('now');
                        $chapter->updated_at = new \DateTime('now');
                        \Zend_Registry::get('emLocale')->persist($chapter);
                        \Zend_Registry::get('emLocale')->flush();
                    }
                }
            }

            if (!empty($shopDetail['id'])) {
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');
                $key = 'shop_similarShopsAndSimilarCategoriesOffers'. $shopInfo->id . '_list';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                $key = 'shopDetails_'. $shopInfo->id.'_list';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                $cacheKeyOfferDetails = 'offerDetails_'  . $shopInfo->id . '_list';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKeyOfferDetails);
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allCategoriesOf_shoppage_'. $shopInfo->id);
                if ($shopInfo->chainItemId) {
                    $queryBuilder = \Zend_Registry::get('emUser')->createQueryBuilder();
                    $query = $queryBuilder->update('KC\Entity\ChainItem', 'ct')
                        ->set('ct.shopName', $queryBuilder->expr()->literal($shopInfo->name))
                        ->set('ct.permalink', $queryBuilder->expr()->literal($shopInfo->permaLink))
                        ->set('ct.status', $queryBuilder->expr()->literal($shopInfo->status))
                        ->where("ct.id=" . $shopInfo->chainItemId)
                        ->andWhere("ct.shopId=" . $shopInfo->id)
                        ->getQuery()->execute();
                }
            }
            return $shopInfo->id;
        } catch (Exception $e) {
            return false;
        }
    }

    public function uploadImage($file, $path)
    {
        $uploadPath = $path;
        if (!file_exists(UPLOAD_IMG_PATH)) {
            mkdir($uploadPath, 0776, true);
        }

        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $rootPath = ROOT_PATH . $uploadPath;
        $files = $adapter->getFileInfo($file);

        if (!file_exists($rootPath)) {
            mkdir($rootPath, 776, true);
        }

        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));
        $adapter->addValidator('Size', false, array('max' => '2MB'));
        $name = $adapter->getFileName($file, false);
        $newName = time() . "_" . $name;
        $cp = $rootPath . $newName;
        $path = ROOT_PATH . $uploadPath . "thum_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 132, 95, $path);

        if ($file == 'bigLogoFile') {
            $path = ROOT_PATH . $uploadPath . "thum_bigLogoFile_" . $newName;
            \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 280, 197, $path);
        }
        if ($file == 'smallLogoFile') {
            $path = ROOT_PATH . $uploadPath . "thum_smallLogoFile_" . $newName;
            \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 280, 100, $path);
        }


        if ($file == "logoFile") {
            $path = ROOT_PATH . $uploadPath . "thum_large_" . $newName;
            \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 200, 150, $path);
            $path = ROOT_PATH . $uploadPath . "thum_small_" . $newName;
            \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 84, 42, $path);
            $path = ROOT_PATH . $uploadPath . "thum_medium_" . $newName;
            \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 100, 50, $path);
            $path = ROOT_PATH . $uploadPath . "thum_big_" . $newName;
            \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 236, 118, $path);
            $path = ROOT_PATH . $uploadPath . "thum_expired_" . $newName;
            \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 100, 50, $path);
            $path = ROOT_PATH . $uploadPath . "thum_medium_store_" . $newName;
            \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 200, 100, $path);
        }

        $adapter->addFilter(
            new \Zend_Filter_File_Rename(
                array(
                    'target' => $cp,
                    'overwrite' => true
                )
            ),
            null,
            $file
        );

        $adapter->receive($file);
        if ($adapter->isValid($file)) {
            return array("fileName" => $newName, "status" => "200",
                    "msg" => "File uploaded successfully",
                    "path" => $uploadPath);
        } else {
            return array("status" => "-1",
                    "msg" => "Please upload the valid file");
        }
    }

    //limit need to be checked
    public static function exportShopsList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopList = $queryBuilder->select('s,a.name as affname,c.name,rs.name')
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.affliatenetwork', 'a')
            ->leftJoin("s.categoryshops", "c")
            ->addSelect("(SELECT con.updated_at FROM KC\Entity\OfferNews  con  WHERE con.shopId = s.id order by con.updated_at Desc LIMIT 1) as newsTickerTime")
            ->addSelect("(SELECT o.updated_at FROM KC\Entity\Offer o WHERE o.shopId = s.id and o.deleted = 0 order by o.updated_at Desc LIMIT 1) as offerTime")
            ->leftJoin("s.relatedshops", "rs")
            ->where("s.deleted=0")
            ->orderBy("s.id", "DESC")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopList;
    }

    public static function getOfferShopList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopList = $queryBuilder->select('s.name,l.id as logoId,l.name as logoName,l.path, s.id')
            ->from('KC\Entity\Shop', 's')
            ->leftJoin("s.logo", "l")
            ->where('s.deleted=0')
            ->andWhere('s.status=1')
            ->orderBy("s.name")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopList;
    }

    public static function getShopDetail($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopDetail = $queryBuilder
            ->select(
                's.notes,s.accountManagerName,s.deepLink,s.deepLinkStatus,s.strictConfirmation,a.name as affname,
                cat.id as categoryId'
            )
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.affliatenetwork', 'a')
            ->leftJoin('s.categoryshops', 'c')
            ->leftJoin('c.shop', 'cat')
            ->where('s.deleted=0')
            ->andWhere("s.id =$shopId")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopDetail;
    }

    public static function getShopPermalinks()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $permalinks = $queryBuilder->select('s.permaLink as permalink, s.howToUse, s.howtoguideslug')
            ->from('KC\Entity\Shop', 's')
            ->where('s.deleted=0')
            ->andWhere('s.status=1')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $permalinks;
    }

    public static function getAllShopPermalinks()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $permalinks = $queryBuilder->select('s.permaLink as permalink')
            ->from('KC\Entity\Shop', 's')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $permalinks;
    }

    public static function updateTotalViewCount()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $nowDate = date('Y-m-d 00:00:00');
        $data = $queryBuilder->select('s.id')
            ->from('KC\Entity\Shop', 's')
            ->addSelect("(SELECT sum(v.onclick) as pop FROM KC\Entity\ShopViewCount v WHERE v.shop = s.id ) as clicks")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        foreach ($data as $value) {
            if ($value['clicks']) {
                $shopList = $queryBuilder->update('KC\Entity\Shop', 's')
                    ->set('s.totalViewcount', $queryBuilder->expr()->literal($value['clicks']))
                    ->where('s.id = ?1')
                    ->setParameter(1, $value['id'])
                    ->getQuery()->execute();
            }
        }
    }

    public static function filterFirstCharacter($var)
    {
        $filteredCharacter = substr($var, 0, 1);
        return $filteredCharacter;
    }

    public static function getStoreExclusiveDeal($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $nowDate = date('Y-m-d 00:00:00');
        $Q = $queryBuilder->select("o")
            ->from('KC\Entity\Offer', 'o')
            ->where('o.shopOffers='.$shopId)
            ->andWhere('o.exclusiveCode=1 AND o.endDate >'."'$nowDate'")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $Q;
    }

    public static function getStoreOfferInPopularOrNot($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $Q = $queryBuilder->select("o")
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.offer', 'p')
            ->where('o.shopOffers='.$shopId)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $Q;
    }

    public static function getStoreHasActiveCode($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $nowDate = date('Y-m-d 00:00:00');
        $Q = $queryBuilder->select("o")
            ->from('KC\Entity\Offer', 'o')
            ->where('o.shopOffers='.$shopId)
            ->andWhere('o.endDate >'."'$nowDate'")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $Q;
    }

    public static function getrecentstores($flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shops = $queryBuilder->select('s.id, s.name, s.permaLink, img.path as imgpath, img.name as imgname')
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.logo', 'img')
            ->where('s.deleted=0')
            ->orderBy('s.id', 'DESC')
            ->setMaxResults($flag)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shops;
    }

    public static function getAllStores()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder->select('s.name as name,s.id as id,s.permaLink as permalink')
            ->from('KC\Entity\Shop', 's')
            ->where('s.deleted= 0')
            ->andWhere('s.status=1')
            ->orderBy("s.name", "ASC")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $data;
    }

    public static function commonSearchStoreForUserGenerated($keyword, $limit)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder->select('s.name as name,s.id as id,s.permaLink as permalink')
            ->from('KC\Entity\Shop', 's')
            ->where('s.deleted=0')
            ->andWhere($queryBuilder->expr()->like("s.name", $queryBuilder->expr()->literal($keyword."%")))
            ->andWhere("s.usergenratedcontent=1")
            ->andWhere('s.status=1')
            ->orderBy("s.name", "ASC")
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getAllShopDetails()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopList = $queryBuilder->select('s.name')
            ->from('KC\Entity\Shop', 's')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopList;

    }

    public static function deletechapters($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder->delete('KC\Entity\ShopHowToChapter', 's')
            ->where('s.id ='.$id)
            ->getQuery()
            ->execute();
        return $data;
    }

    public static function getShopInfoByShopId($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopInfo = $queryBuilder->select(
            's'
        )
            ->from('KC\Entity\Shop', 's')
            ->where('s.id='.$shopId)
            ->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopInfo;
    }

    public static function getAffliateNetworkDetail($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        return $queryBuilder->select('s,a')
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.affliatenetwork', 'a')
            ->where('s.deleted=0')
            ->andWhere('s.id ='. $shopId)
            ->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
    //to be checked again
    public static function getAllUrls($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select(
            "s.id, s.permaLink,s.contentManagerId,c.id as test, s.howToUse, s.howtoguideslug, o.extendedOffer, o.extendedUrl")
            ->from('KC\Entity\Shop', 's')
            ->leftJoin("s.offer", "o")
            ->leftJoin("s.categoryshops", "c")
            ->where("s.id= ". $id);
        $shop = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        # redactie permalink
        $redactie =  \KC\Repository\User::returnEditorUrl($shop[0]['contentManagerId']);

        $urlsArray = array();

        # check for related shop permalink
        if (isset($shop[0]['permaLink'])) {
            $urlsArray[] = $shop[0]['permaLink'];
        }

        # check for ho to use guide
        if ($shop[0]['howToUse']) {
            # check for extende offer url
            if (isset($shop[0]['permaLink']) && strlen($shop[0]['permaLink']) > 0) {
                if (!empty($shop['howtoguideslug'])) {
                    $urlsArray[] = $shop['permaLink']. '/'. $shop['howtoguideslug'];
                } else {
                    $urlsArray[] = \FrontEnd_Helper_viewHelper::__link('link_how-to'). '/'. $shop['permaLink'];
                }
            }
        }

        # check if an editor  has permalink then add it into array
        if (isset($redactie['permalink']) && strlen($redactie['permalink']) > 0) {
            $urlsArray[] = $redactie['permalink'] ;
        }
 
        # check an offerr has one or more categories
        if (isset($shop['category']) && count($shop['category']) > 0) {

            $categoriesPage = \FrontEnd_Helper_viewHelper::__link('link_categorieen') .'/' ;

            # traverse through all catgories
            foreach ($shop['category'] as $value) {
                # check if a category has permalink then add it into array
                if (isset($value['permaLink']) && strlen($value['permaLink']) > 0) {
                    $urlsArray[] = $categoriesPage . $value['permaLink'];
                    $urlsArray[] = $categoriesPage . $value['permaLink'] .'/2';
                    $urlsArray[] = $categoriesPage . $value['permaLink'] .'/3';
                }
            }
        }

        # check extended offer of this shop
        if (isset($shop['offer']) && count($shop['offer']) > 0) {
            # traverse through all offer
            foreach ($shop['offer'] as $value) {
                # check the offer is extended or not
                if (isset($value['extendedOffer']) && $value['extendedOffer']) {
                    $urlsArray[] = \FrontEnd_Helper_viewHelper::__link('link_deals') .'/'. $value['extendedUrl'] ;
                }
            }
        }
        return $urlsArray ;
    }

    public static function changeStatus($params)
    {
        $status = $params['status'] == 'offline' ? '0' : '1';
        if ($params['status'] == 'offline') {
            $date = new \DateTime('now');
            $status = 0 ;
        } else {
            $status = 1 ;
            $date = null;
        }
        $queryBuilderShop = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilderShop
        ->select('s.offlineSicne, s.howToUse')
        ->from('KC\Entity\Shop', 's')
        ->where('s.id='.$params['id']);
        $shopDetail = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $shop = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $params['id']);
        $shop->status = $status;
        $shop->offlineSicne = $date;
        $entityManagerLocale->persist($shop);
        $entityManagerLocale->flush();
        $key = 'shop_similar_shops';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shopDetails_'.$params['id'].'_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shop_similarShopsAndSimilarCategoriesOffers'. $params['id'] . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        return array('offlineSince'=>$shop->offlineSicne, 'howToUse'=>$shopDetail[0]['howToUse']);
    }

    public static function getAmountShopsCreatedLastWeek()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        // - 7 days from today
        $past7Days = date($format, strtotime('-7 day' . $date));
        $nowDate = $date;

        $query = $queryBuilder->select("count(s) as amountshops")
            ->from('KC\Entity\Shop', 's')
            ->where('s.deleted=0')
            ->andWhere("s.created_at BETWEEN '".$past7Days."' AND '".$date."'");
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getTotalAmountOfShops()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        $query = $queryBuilder->select("count(s) as amountshops")
            ->from('KC\Entity\Shop', 's')
            ->where('s.deleted = 0')
            ->andWhere("s.status = '1'");
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getTotalAmountOfShopsCodeOnline()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        $query = $queryBuilder->select("count(s) as amountshops")
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.offer', 'o')
            ->where('s.deleted = 0')
            ->andWhere("s.status = '1'")
            ->andWhere("o.endDate > '".$date."'")
            ->andWhere("o.startDate <= '".$date."'")
            ->andWhere("o.discountType='CD'")
            ->andWhere('o.deleted = 0');
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getTotalAmountOfShopsCodeOnlineThisWeek()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        // - 7 days from today
        $past7Days = date($format, strtotime('-7 day' . $date));

        $query = $queryBuilder->select("count(s) as amountshops")
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.offer', 'o')
            ->where('s.deleted=0')
            ->andWhere("o.endDate > '".$past7Days."'")
            ->andWhere("o.startDate <= '".$past7Days."'")
            ->andWhere("o.discountType='CD'")
            ->andWhere('o.deleted = 0')
            ->andWhere("s.status = 1 OR (s.offlineSicne > '".$past7Days."' AND s.offlineSicne < '".$date."')");
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getTotalAmountOfShopsCodeOnlineLastWeek()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        // - 7 days from today
        $past7Days = date($format, strtotime('-7 day' . $date));
        // - 14 days from today
        $past14Days = date($format, strtotime('-14 day' . $date));
        $query = $queryBuilder->select("count(s) as amountshops")
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.offer', 'o')
            ->where('s.deleted=0')
            ->andWhere("o.endDate > '".$past14Days."'")
            ->andWhere("o.startDate <= '".$past14Days."'")
            ->andWhere("o.discountType='CD'")
            ->andWhere('o.deleted = 0')
            ->andWhere("s.status = 1 OR (s.offlineSicne > '".$past14Days."' AND s.offlineSicne < '".$past7Days."')");
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getDaysSinceShopWithoutOnlneOffers($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        // find whether this shop has any code
        $query = $queryBuilder->select("s.id, o.id")
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.offer', 'o')
            ->where($queryBuilder->expr()->eq('s.id', $shopId))
            ->andWhere("o.discountType = 'CD'");
        $anyOffer = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $Days = 0;
        $noOfDays = $Days;
        if (empty($anyOffer)) {
            $noOfDays = $Days;
        } else {
            // find whether this shop has any code online
            $query = \Zend_Registry::get('emLocale')->createQueryBuilder()->select("s.id, o.id")
                ->from('KC\Entity\Shop', 's')
                ->leftJoin('s.offer', 'o')
                ->where($queryBuilder->expr()->eq('s.id', $shopId))
                ->andWhere("o.endDate >= '".$date."'")
                ->andWhere("o.startDate <= '".$date."'")
                ->andWhere("o.discountType='CD'")
                ->andWhere('o.deleted = 0');
            $isOnline = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            
            //since how many days the shop has no code online
            if (empty($isOnline)) {
                $query = \Zend_Registry::get('emLocale')->createQueryBuilder()
                    ->select("(DATE_DIFF('".$date."', o.endDate)) as diffdays")
                    ->from('KC\Entity\Shop', 's')
                    ->leftJoin('s.offer', 'o')
                    ->where(\Zend_Registry::get('emLocale')->createQueryBuilder()->expr()->eq('s.id', $shopId))
                    ->andWhere("o.endDate < '".$date."'")
                    ->andWhere("o.discountType='CD'")
                    ->andWhere('o.deleted = 0')
                    ->orderBy('o.endDate', 'DESC')
                    ->setMaxResults(1);
                $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                if (!empty($data)) {
                    $noOfDays = $data[0]['diffdays'];
                }
            } else {
                $noOfDays = 0;
            }

        }

        return $noOfDays;
    }

    public static function getFavouriteCountOfShop($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select("count(s.id) as countFavourite")
                ->from('KC\Entity\Shop', 's')
                ->leftJoin('s.visitors', 'v')
                ->where('s.id='.$shopId);
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data['countFavourite'];
    }

    public static function getshopStatus($shopId)
    {
        if ($shopId!='') {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('s.id')
                ->from("KC\Entity\Shop", "s")
                ->where('s.id='.$shopId)
                ->andWhere('s.status = 1');
            $Q = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (empty($Q)) {
                $online = true;
                return $online;
            } else {
                $online = false;
                return $online;
            }
        } else {
            $online = false;
            return $online;
        }
    }

    public static function getShopBranding($shopID)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s.brandingcss')
            ->from("KC\Entity\Shop", "s")
            ->where('s.id='.$shopID);
        $brandingCss = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return (!empty($brandingCss[0]['brandingcss'])) ? unserialize($brandingCss[0]['brandingcss']) : null;
    }

    public static function saveEditorBallonText($params, $shopId, $type)
    {
        if ($type == 'update') {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
            ->delete("KC\Entity\EditorBallonText", "e")
            ->where("e.shop = ".$shopId)
            ->getQuery()
            ->execute();
        }
        $contentInfo = array_map('trim', $params['ballontextcontent']);
        foreach ($contentInfo as $key => $content) {
            if (isset($content) && $content != '') {
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $ballonText = new \KC\Entity\EditorBallonText();
                $ballonText->shop = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $shopId);
                $ballonText->ballontext = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['ballontextcontent'][$key]);
                $ballonText->deleted = 0;
                $entityManagerLocale->persist($ballonText);
                $entityManagerLocale->flush();
            }
        }
        return true;
    }

    public static function getTotalNumberOfMoneyShops()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select("count(s.id) as moneyShops")
        ->from('KC\Entity\Shop', 's')
        ->where('s.deleted = 0')
        ->andWhere("s.status = '1'")
        ->andWhere("s.affliateProgram = 1");
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function moneyShopRatio()
    {
        $totalNumberOfShops = self::getTotalAmountOfShops();
        $numberOfMoneyShops = self::getTotalNumberOfMoneyShops();
        $shopRatio = ($numberOfMoneyShops['moneyShops'] / $totalNumberOfShops['amountshops']) * 100;
        return round($shopRatio);
    }

    public static function getRelatedShops($relatedShopsInfo)
    {
        $relatedShopName = array();
        if (!empty($relatedShopsInfo)) {
            foreach ($relatedShopsInfo as $relatedShop) {
                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder->select("s.name")
                    ->from('KC\Entity\Shop', 's')
                    ->where('s.id = '.$relatedShop['relatedshopId']);
                $relatedShopName[] = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            }
        }
        return $relatedShopName;
    }

    public static function getShopIdByShopName($shopName)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('s.id')
            ->from('KC\Entity\Shop', 's')
            ->where('s.name='.$queryBuilder->expr()->literal(ucfirst($shopName)));
        $shopId = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return isset($shopId[0]) ? $shopId[0]['id'] : '';
    }
}

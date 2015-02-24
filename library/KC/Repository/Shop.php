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
        ->select('p.id, s.name, s.permaLink, img.path as imgpath, img.name as imgname')
        ->from('KC\Entity\PopularShop', 'p')
        ->addSelect(
            "(SELECT COUNT(*) FROM KC\Entity\Offer active WHERE
            (active.shopOffers = s.id AND active.endDate >= '$currentDate' 
                AND active.deleted = 0
            )
            ) as activeCount"
        )
        ->leftJoin('p.popularshops', 's')
        ->leftJoin('s.logo', 'img')
        ->where('s.deleted = 0')
        ->addWhere('s.status = 1')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults($limit);
        $popularStoreData = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularStoreData;
    }

     public static function getSimilarShops($shopId, $numberOfShops = 12)
    {
        $similarShops = self::getSimilarShopsByShopId($shopId, $numberOfShops);
        if (count($similarShops) <= $numberOfShops) {
            $similarShopsBySimilarCategories = self::getSimilarShopsBySimilarCategories($shopId, $numberOfShops);
            $similarShops = self::removeDuplicateShops($similarShops, $similarShopsBySimilarCategories, $numberOfShops);
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
            ->select(
                "s.name, s.permaLink, img.path, img.name,
                c.id,ss.name, ss.permaLink"
            )
            ->from('KC\Entity\Shop', 's')
            ->where("s.id = ".$shopId)
            ->leftJoin('s.categoryshops', 'c')
            ->leftJoin('c.category', 'ss')
            ->andWhere("ss.status = 1")
            ->andWhere("ss.deleted = 0")
            ->leftJoin('ss.logo', 'img');
        $relatedShops = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $relatedShops;
    }

    protected static function removeDuplicateShops($similarShops, $similarShopsBySimilarCategories, $numberOfShops)
    {
        foreach ($similarShopsBySimilarCategories[0]['category'] as $category) {
            foreach ($category['shop'] as $relatedCategoryShop) {
                if (count($similarShops) <= $numberOfShops &&
                        !in_array($relatedCategoryShop['id'], $similarShops)) {
                    $similarShops[$relatedCategoryShop['id']] = $relatedCategoryShop;
                }
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

    public static function getshopDetails($permalink)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s,img.name,img.path,chptr')
        ->from('KC\Entity\Shop', 's')
        ->leftJoin('s.logo', 'img')
        ->leftJoin('s.howtochapter', 'chptr')
        ->Where("s.permaLink= '".$permalink."'")
        ->andWhere('s.status = 1');
        $shopDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopDetails;
    }

    public static function getShopsByShopIds($shopIds)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s.id, s.name,s.permaLink, img.path as imgpath, img.name as imgname')
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.logo', 'img')
            ->where('s.deleted=0')
            ->andWhere($queryBuilder->expr()->in('s.id', $shopIds))
            ->orderBy("s.name");
        $shopsInformation = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopsInformation;
    }

    public static function getStoresForSearchByKeyword($searchedKeyword, $limit, $fromPage = '')
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDate = date('Y-m-d 00:00:00');
        $query = $queryBuilder->select('s.id,s.name,s.permaLink, img.path as imgpath, img.name as imgname')
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.logo', 'img')
            ->where('s.deleted=0')
            ->andWhere('s.status=1')
            ->andWhere($queryBuilder->expr()->like("s.name", $queryBuilder->expr()->literal("%". $searchedKeyword."%")));
        if ($fromPage!='') {
            $query = $query->addSelect(
                "(SELECT COUNT(active) FROM KC\Entity\Offer active WHERE
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
    // favourite shop model to be checked
    public static function shopAddInFavourite($visitorId, $shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $addedStatus = 0;
        if ($shopId!='') {
            $favouriteShops  = $queryBuilder->select('s')
                ->from('KC\Entity\FavoriteShop', 's')
                ->where('s.visitorId='.$visitorId)
                ->andWhere('s.shopId='.$shopId)
                ->getQuery()
                ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if ($favouriteShops) {
                Doctrine_Query::create()->delete()
                    ->from('FavoriteShop fs')
                    ->where("fs.shopId=" . $shopId)
                    ->andWhere('fs.visitorId='.$visitorId)
                    ->execute();
                $addedStatus = 1;
            } else {
                $favouriteShops = new FavoriteShop();
            }
            $favouriteShops->visitorId = $visitorId;
            $favouriteShops->shopId = $shopId;
            $favouriteShops->save();
            $shopName = Doctrine_Core::getTable("Shop")->findOneBy('id', $shopId);
            $cacheKeyShopDetails = 'shopDetails_'  . $shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKeyShopDetails);
            $cacheKeyOfferDetails = 'offerDetails_'  . $shopId . '_list';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKeyOfferDetails);
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_'.$visitorId.'_favouriteShops');
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
            ->where('s.deleted = '. $flag)
            ->andWhere($queryBuilder->expr()->like("s.name", $queryBuilder->expr()->literal("%".$srh."%")));
        $request = \DataTable_Helper::createSearchRequest($params, array());
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($shopList)
            ->add('number', 's.id')
            ->add('text', 's.name')
            ->add('text', 's.updated_at')
            ->add('text', 's.created_at')
            ->add('text', 's.permaLink')
            ->add('text', 's.affliateProgram')
            ->add('text', 'a.name')
            ->add('text', 's.discussions')
            ->add('text', 's.showSignupOption')
            ->add('text', 's.status')
            ->add('text', 's.offlineSicne');
        $data = $builder->getTable()->getResultQueryBuilder()->getQuery()->getArrayResult();
        $result = \DataTable_Helper::getResponse($data, $request);
        return $result;
    }

    public static function moveToTrash($id)
    {
        if ($id) {
            //find record by id and apply sofdeleted (change status 0 to 1)
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
            $query = $queryBuilder->delete('KC\Entity\Shop', 's')
                ->where("s.id=" . $id)
                ->getQuery()->execute();
            return true;
        }
        return false ;
    }

    public function preDelete($event)
    {
        $id = $this->id;
        # check if shop is deleted permanently
        if ($this->deleted == 1) {
            $dela = Doctrine_Query::create()->delete()
             ->from('refShopCategory r')->where('r.shopid=' . $id)
            ->execute();

            $delb = Doctrine_Query::create()->delete()->from('PopularShop p')
            ->where('p.shopId=' . $id)->execute();
            $del2 = Doctrine_Query::create()->delete()->from('RefArticleStore p')
            ->where('p.storeid=' . $id)->execute();
            $offer  = Doctrine_Query::create()->select('o.id')->from('Offer o')->where('o.shopId='.$id)->fetchArray();
            
            foreach ($offer as $off) {
                Offer::deleteOffer($off['id']);
            }

            $del2 = Doctrine_Query::create()->delete()->from('OfferNews p')
            ->where('p.shopId=' . $id)->execute();
            $del2 = Doctrine_Query::create()->delete()->from('RefArticleStore p')
            ->where('p.storeid=' . $id)->execute();
            $del = Doctrine_Query::create()->delete()->from('Shop s')
            ->where("s.id=" . $id)->execute();
            $delPermalink = Doctrine_Query::create()->delete()->from('RoutePermalink p')
            ->where("permalink=". "'.$this->permaLink.'")->execute();
            $delPermalink = Doctrine_Query::create()->delete()->from('RoutePermalink p')
            ->where("permalink=". "'$this->permaLink'")->execute();
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allCategoriesOf_shoppage_'. $id);
            //call cache function
            $key = 'shop_similar_shops';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $cacheKey = FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($this->permaLink);
            $key = 'store_'.$cacheKey.'_howToGuide';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_storesHeader_image');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('12_popularShops_list');
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');

            # update chain if shop is associated with chain
            if ($this->chainItemId) {
                $chainItem = Doctrine_Core::getTable("ChainItem") ->findBySql(
                        'shopId = ? AND chainId = ?',
                        array($this->id,$this->chainId),
                        Doctrine::HYDRATE_RECORD)->getData();

                $itemId = $chainItem[0]->id;
                $chainItem[0]->deleteChainItem($itemId);
            }
        }
        //call cache function
        $key = 'shop_similar_shops';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $cacheKey = FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($this->permaLink);
        $key = 'store_'.$cacheKey.'_howToGuide';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('12_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');
        return $id;

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
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        if (!empty($shopDetail['id'])) {
            $shopInfo = \Zend_Registry::get('emLocale')
                ->getRepository('KC\Entity\Shop')
                ->find($shopDetail['id']);
        } else {
            $shopInfo = new \Kc\Entity\Shop();
        }
        $shopInfo->deleted = 0;
        $shopInfo->created_at = new \DateTime('now');
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
        $this->customtext = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopCustomText']);
        $this->moretextforshop = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['moretextforshop']);
        $shopViewCount = isset($shopDetail['shopViewCount']) ? $shopDetail['shopViewCount'] : '0';
        $shopInfo->views = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopViewCount);
        $shopInfo->howtoTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pageTitle']);
        $shopInfo->howtoSubtitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pageSubTitle']);
        $this->howtoSubSubTitle = \FrontEnd_Helper_viewHelper::sanitize(
            \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pageSubSubTitle'])
        );
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
            $this->customtextposition = $shopDetail['customtextposition'];
        }

        $this->showcustomtext = 0;
        if ($shopDetail['showcustomtext'] != '') {
            $this->showcustomtext = $shopDetail['showcustomtext'];
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
                    $shopInfo->offlineSicne = $shopDetail['offlineSince'] ;
                } else {
                    $shopInfo->offlineSicne = date("Y-m-d h:m:s");
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
            $shopInfo->affliateNetworkId = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                $shopDetail['shopAffiliateNetwork']
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
        $key = 'shopDetails_'  . $shopInfo->id . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $cacheKeyOfferDetails = 'offerDetails_'  . $shopInfo->id . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKeyOfferDetails);
        $key = 'shop_similar_shops';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $cacheKey = \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($shopDetail['shopNavUrl']);
        $key = 'store_'.$cacheKey.'_howToGuide';
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
            $getcategory = $queryBuilder->select('s.permaLink')
                ->from('KC\Entity\Shop', 's')
                ->where('s.id = '.$shopDetail['id'])
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
               
        }
        if (!empty($getcategory[0]['permaLink'])) {
            $getRouteLink = $queryBuilder->select('routep.permalink')
                ->from('KC\Entity\RoutePermalink', 'routep')
                ->where("routep.permalink = '".$getcategory[0]['permaLink']."'")
                ->andWhere("routep.type = 'SHP'")
                ->setMaxResults(1)
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $howToguideRoute = $queryBuilder->select('rpermalink.id')
                ->from('KC\Entity\RoutePermalink', 'rpermalink')
                ->where("rpermalink.permalink = 'how-to/".$getRouteLink[0]['permalink']."'")
                ->andWhere("rpermalink.type = 'SHP'")
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            
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
                    $shopId = $this->id;
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
                $shopReasons['reasontitle5'] = !empty($shopDetail['reasontitle4'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasontitle5']) : '';
                $shopReasons['reasonsubtitle5'] = !empty($shopDetail['reasonsubtitle5'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasonsubtitle5']) : '';
                $shopReasons['reasontitle6'] = !empty($shopDetail['reasontitle6'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasontitle6']) : '';
                $shopReasons['reasonsubtitle6'] = !empty($shopDetail['reasonsubtitle6'])
                    ? \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['reasonsubtitle6']) : '';
                \KC\Repository\ShopReasons::saveReasons($shopReasons, $this->id);
            }

            $key = 'shop_similar_shops';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            if (!empty($getRouteLink)) {
                $exactLink = 'store/storedetail/id/'.$shopInfo->id;
                $howtoguide = 'store/howtoguide/shopid/'.$shopInfo->id;
                $updateRouteLink = $queryBuilder->update('KC\Entity\RoutePermalink', 'rpm')
                    ->set(
                        'rpm.permalink',
                        "'".\BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopNavUrl'])."'"
                    )
                    ->set('rpm.type', "'SHP'")
                    ->set('rpm.exactlink', "'". $exactLink."'");
                $updateRouteLink->where("rpm.type = 'SHP'")
                    ->andWhere($queryBuilder->expr()->eq('rpm.permalink', $queryBuilder->expr()->literal($getRouteLink[0]['permalink'])))
                    ->getQuery()
                    ->execute();

                if (!empty($howToguideRoute)) {
                    $updateRouteHow = \Zend_Registry::get('emLocale')->createQueryBuilder()->update('KC\Entity\RoutePermalink', 'rpl')
                    ->set('rpl.permalink', "'how-to/".\BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopNavUrl'])."'")
                    ->set('rpl.type', "'SHP'")
                    ->set('rpl.exactlink', "'".$howtoguide."'");
                    $updateRouteHow->where("rpl.type = 'SHP'")
                        ->andWhere($queryBuilder->expr()->eq('rpl.permalink', $queryBuilder->expr()->literal('how-to/'.$getRouteLink[0]['permalink'])))
                        ->getQuery()
                        ->execute();
                } else {
                    $route = new \KC\Entity\RoutePermalink();
                    $route->permalink = "how-to/" . \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopNavUrl']);
                    $route->type = 'SHP';
                    $route->exactlink = 'store/howtoguide/shopid/'.$shopInfo->id;
                    $route->deleted = 0;
                    $route->created_at = new \DateTime('now');
                    $route->updated_at = new \DateTime('now');
                    \Zend_Registry::get('emLocale')->persist($route);
                    \Zend_Registry::get('emLocale')->flush();
                }
                 
            } else {
                $route = new \KC\Entity\RoutePermalink();
                $route->permalink = \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopNavUrl']);
                $route->type = 'SHP';
                $route->exactlink = 'store/storedetail/id/'.$shopInfo->id;
                $route->deleted = 0;
                $route->created_at = new \DateTime('now');
                $route->updated_at = new \DateTime('now');
                \Zend_Registry::get('emLocale')->persist($route);
                \Zend_Registry::get('emLocale')->flush();

                $route = new \KC\Entity\RoutePermalink();
                $route->permalink = "how-to/" . \BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopNavUrl']);
                $route->type = 'SHP';
                $route->exactlink = 'store/howtoguide/shopid/'.$shopInfo->id;
                $route->deleted = 0;
                $route->created_at = new \DateTime('now');
                $route->updated_at = new \DateTime('now');
                \Zend_Registry::get('emLocale')->persist($route);
                \Zend_Registry::get('emLocale')->flush();
            }

            if (isset($shopDetail['similarstoreord'])) {
                $similarstoreordArray = explode(',', $shopDetail['similarstoreord']);
                $i = 1;
                foreach ($similarstoreordArray as $shop) {
                    if ($shop!='') {
                        if (!empty($shopDetail['id'])) {
                            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                            $query = $queryBuilder->delete('KC\Entity\RefShopRelatedshop', 'rsrs')
                                ->where("rsrs.shop=" . $shopDetail['id'])
                                ->getQuery()->execute();
                        }
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
            return $shopInfo->id;
            //to be refactored when chain item model is done
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
                $key = 'shopDetails_'. $shopInfo->id.'_list';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                $cacheKeyOfferDetails = 'offerDetails_'  . $shopInfo->id . '_list';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKeyOfferDetails);
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allCategoriesOf_shoppage_'. $shopInfo->id);
                   /* if($shopInfo->chainItemId) {
                        $chainItem = Doctrine_Core::getTable("ChainItem") ->findBySql(
                                            'shopId = ? AND id = ?',
                                            array($shopInfo->id,$shopInfo->chainItemId),
                                            Doctrine::HYDRATE_RECORD)->getData();
                        # verify a valid chain item exists
                        if(isset($chainItem[0])) {
                             $chainItem[0]->update($data,$shopInfo->toArray(false));
                        }
                    }*/
            }
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
        $shopList = $queryBuilder->select('s.name as shopName,l.id as logoId,l.name,l.path, s.id')
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
        $shopDetail = $queryBuilder->select('s.notes,s.accountManagerName,s.deepLink,s.deepLinkStatus,s.strictConfirmation,a.name as affname,cat.id as categoryId')
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.affliatenetwork', 'a')
            ->leftJoin('s.categoryshops', 'cat')
            ->where('s.deleted=0')
            ->andWhere("s.id =$shopId")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopDetail;
    }

    public static function getShopPermalinks()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $permalinks = $queryBuilder->select('s.permaLink as permalink, s.howToUse')
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


    public static function getStoreLinks($shopId, $checkRefUrl = false)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder->select('s.id, s.permaLink as permalink, s.deepLink, s.deepLinkStatus, s.refUrl, s.actualUrl')
        ->from('KC\Entity\Shop', 's')
        ->where('s.id='.$shopId)
        ->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $network = self::getAffliateNetworkDetail($shopId);

       if ($checkRefUrl) {
            # retur false if s shop is not associated with any network
            if (!isset($network['affliatenetwork'])) {
                return false ;
            }
            if (isset($data['deepLink']) && $data['deepLink']!= null) {
                # deeplink is now commetted for the time being, so we always @return false ;
                return false;
            } elseif (isset($data['refUrl']) && $data['refUrl']!=null) {
                return true ;
            } else {
                return true ;
            }
        }
        $subid = "" ;
        if (isset($network['affliatenetwork'])) {
            if (!empty($network['subid'])) {
                $subid = "&". $network['subid'] ;
                $clientIP = \FrontEnd_Helper_viewHelper::getRealIpAddress();
                $ip = ip2long($clientIP);
                # get click detail and replcae A2ASUBID click subid
                $conversion = \KC\Repository\Conversions::getConversionId($data['id'], $ip, 'shop');
                $subid = str_replace('A2ASUBID', $conversion['subid'], $subid);
                $subid = \FrontEnd_Helper_viewHelper::setClientIdForTracking($subid);
            }
        }

        if (isset($data['refUrl']) && $data['refUrl']!=null) {
            $url = $data['refUrl'];
            $url .= $subid;
        } elseif (isset($data['actualUrl']) && $data['actualUrl']!=null) {
            $url = $data['actualUrl'];
        } else {
            $url = HTTP_PATH_LOCALE.@$data['permaLink'];

        }
        return $url;
    }

    public static function addConversion($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $clientIP = \FrontEnd_Helper_viewHelper::getRealIpAddress();
        $ip = ip2long($clientIP);

        # save conversion detail if an offer is associated with a network
        if (self::getStoreLinks($id, true)) {
            # check for previous cnversion of same ip
            $data = $queryBuilder->select("count(c.id) as exist, c.id")
                ->from("KC\Entity\Conversions", "c")
                ->andWhere("c.shop= '".$id."'")
                ->andWhere("c.IP='".$ip."'")
                ->andWhere("c.converted=0")
                ->groupBy("c.id")->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if (!isset($data[0]['exist'])) {
                # save conversion detail if an offer is associated with a network
                $cnt = new \KC\Entity\Conversions();
                $cnt->shop = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $id);
                $cnt->IP = $ip;
                $cnt->utma = $_COOKIE["__utma"];
                $cnt->utmz = $_COOKIE["__utmz"];
                $time = time();
                $cnt->subid = md5(time()*rand(1, 999));
                \Zend_Registry::get('emLocale')->persist($cnt);
                \Zend_Registry::get('emLocale')->flush();
            } else {
                # update existing conversion detail
                $cnt = $queryBuilder->select("count(cs.id) as exist")
                    ->from("KC\Entity\Conversions", "cs")
                    ->andWhere("cs.id='".$data[0]['id']."'")
                    ->getQuery()
                    ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

                if (!empty($cnt)) {
                    $time = time();
                    $queryBuilder->update('KC\Entity\Conversions', 'cv')
                        ->set('cv.utma', $queryBuilder->expr()->literal($_COOKIE["__utma"]))
                        ->set('cv.utmz', $queryBuilder->expr()->literal($_COOKIE["__utmz"]))
                        ->set('cv.subid', $queryBuilder->expr()->literal(md5(time()*rand(1, 999))))
                        ->where('cv.id = ?1')
                        ->setParameter(1, $data[0]['id'])
                        ->getQuery()->execute();
                }
            }
        }
    }

    public static function getAffliateNetworkDetail($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        return $queryBuilder->select('s.id,a.name as affname,a.subId as subid')
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.affliatenetwork', 'a')
            ->where('s.deleted=0')
            ->andWhere("s.id =".$shopId)->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
    //to be checked again
    public static function getAllUrls($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select("s.id, s.permaLink,s.contentManagerId,c.id as test, s.howToUse, o.extendedOffer, o.extendedUrl")
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
                $urlsArray[] = \FrontEnd_Helper_viewHelper::__link('link_how-to') .'/'.$shop[0]['permaLink'];
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
            $date = date('Y-m-d H:i:s');
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

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->update('KC\Entity\Shop', 's')
            ->set('s.status', $queryBuilder->expr()->literal($status))
            ->set('s.offlineSicne', $queryBuilder->expr()->literal($date))
            ->setParameter(1, $params['id'])
            ->where('s.id = ?1')
            ->getQuery();
        $shop = $query->execute();
        $key = 'shop_similar_shops';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shopDetails_'.$params['id'].'_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        return array('offlineSince'=>$shopDetail[0]['offlineSicne'], 'howToUse'=>$shopDetail[0]['howToUse']);
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
            ->where("e.shopid = ".$shopId)
            ->execute();
        }
        $contentInfo = array_map('trim', $params['ballontextcontent']);
        foreach ($contentInfo as $key => $content) {
            if (isset($content) && $content != '') {
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $ballonText = new \KC\Entity\EditorBallonText();
                $ballonText->shopid = $shopId;
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
}

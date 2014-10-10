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

    public static function getSimilarShops($shopId, $numberOfShops = 12)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select(
            's.name, s.permaLink, img.path, img.name, logo.path, logo.name, rs.name, rs.permaLink,
            c.id,ss.name, ss.permaLink'
        )
            ->from('KC\Entity\Shop', 's')
            ->setParameter(1, $shopId)
            ->where('s.id = ?1')
            ->leftJoin("s.relatedshops", "rs")
            ->setParameter(2, '1')
            ->andWhere("rs.status = ?2")
            ->setParameter(3, '0')
            ->andWhere("rs.deleted = ?3")
            ->leftJoin("rs.logo", "logo")
            ->leftJoin('s.categoryshops', 'c')
            ->setParameter(4, '1')
            ->andWhere("c.status = ?4")
            ->setParameter(5, '0')
            ->andWhere("c.deleted = ?5")
            ->leftJoin('c.shop', 'ss')
            ->setParameter(6, '1')
            ->andWhere("ss.status = ?6")
            ->setParameter(7, '0')
            ->andWhere("ss.deleted = ?7")
            ->leftJoin('ss.logo', 'img');
        $relatedShops = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return self::removeDuplicateShops($relatedShops, $numberOfShops);
    }

    protected static function removeDuplicateShops($relatedShops, $numberOfShops)
    {
        $similarShopsWithoutDuplicate = array();
        foreach ($relatedShops[0]['relatedshops'] as $relatedShop) {
            if (count($similarShopsWithoutDuplicate) <= $numberOfShops) {
                $similarShopsWithoutDuplicate[$relatedShop['id']] = $relatedShop;
            }
        }

        if (count($similarShopsWithoutDuplicate) <= $numberOfShops) {
            // push shops related to same category which are not yet added
            foreach ($relatedShops[0]['category'] as $category) {
                foreach ($category['shop'] as $relatedCategoryShop) {
                    if (count($similarShopsWithoutDuplicate) <= $numberOfShops &&
                            !in_array($relatedCategoryShop['id'], $similarShopsWithoutDuplicate)) {
                        $similarShopsWithoutDuplicate[$relatedCategoryShop['id']] = $relatedCategoryShop;
                    }
                }
            }
        }
        return $similarShopsWithoutDuplicate;
    }

    public static function getPopularStores($limit, $shopId = null)
    {
        $currentDate = date('Y-m-d 00:00:00');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select(
            'o.id,o.exclusiveCode,p.id,s.name,s.permaLink,s.deepLink,s.deepLinkStatus,s.refUrl,s.actualUrl,
            s.Deliverytime, s.returnPolicy, s.freeDelivery, p.type,p.position,IDENTITY(p.popularshops) as shopId, img.path as imgpath, 
            img.name as imgname'
        )
            ->from('KC\Entity\PopularShop', 'p')
            ->addSelect(
                "(SELECT COUNT(exclusive) FROM KC\Entity\Offer exclusive WHERE exclusive.shopOffers = s.id AND
                (o.exclusiveCode=1 AND exclusive.endDate > '$currentDate')) as exclusiveCount"
            )
            ->addSelect("(SELECT COUNT(pc) FROM KC\Entity\PopularCode pc WHERE pc.popularcode = o.id ) as popularCount")
            ->addSelect(
                "(SELECT COUNT(active) FROM KC\Entity\Offer active WHERE
                (active.shopOffers = s.id AND active.endDate >= '$currentDate' 
                    AND active.deleted = 0
                )
                ) as activeCount"
            )
            ->leftJoin('p.popularshops', 's')
            ->leftJoin('s.offer', 'o')
            ->leftJoin('s.logo', 'img')
            ->setParameter(1, '0')
            ->where('s.deleted= ?1')
            ->setParameter(2, '1')
            ->andWhere('s.status= ?2')
            ->orderBy('p.position', 'ASC');
        
        if ($shopId) {
            $query = $query->setParameter(3, $shopId)->andWhere("s.id = ?3 ");
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

    public static function getallStoresForFrontEnd()
    {
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
        ->setParameter(1, '0')
        ->where('s.deleted= ?1')
        ->setParameter(2, '1')
        ->andWhere('s.status= ?2')
        ->orderBy('s.name');
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
        $query = $queryBuilder->select('p.id,s.name,s.permaLink,img.path as imgpath, img.name as imgname')
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
                    AND active.deleted = 0
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
    ##################################################################################
    ################## END REFACTORED CODE ###########################################
    ##################################################################################

    public function addChain($id)
    {
        $this->chainItemId = $id;
        $this->save();
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

        $shopList = $queryBuilder->select('s,a.name as affname')
            ->from("KC\Entity\Shop", "s")
            ->leftJoin('s.affliatenetwork', 'a')
            ->where('s.deleted = '. $flag)
            ->andWhere($queryBuilder->expr()->like("s.name", $queryBuilder->expr()->literal("%".$srh."%")))->getQuery();
        $result = \DataTable_Helper::generateDataTableResponse(
            $shopList,
            $params,
            array("__identifier" => 's.id,s.updated_at', 's.id','s.name','s.permaLink','s.affliateProgram','s.created_at','affname','s.discussions','s.showSignupOption','s.status','s.offlineSicne'),
            array(),
            array()
        );
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

    // to be migrated
    public static function permanentDeleteShop($id)
    {
        if ($id) {
            $shop = Doctrine_Core::getTable("Shop")->find($id);
            $shop->hardDelete(true);
            return true ;
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

    // to be migrated
    public function CreateNewShop($shopDetail)
    {
        //echo "<pre>"; print_r($shopDetail);die;
        $this->name = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopName']);
        $this->permaLink = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopNavUrl']);
        $this->metaDescription = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopMetaDescription']);
        $this->notes =BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['shopNotes']);

    #   $this->deepLink =BackEnd_Helper_viewHelper::stripSlashesFromString (@$shopDetail['shopDeepLinkUrl']);
    #   $this->deepLinkStatus =BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['deepLinkStatus']);

        $this->refUrl = BackEnd_Helper_viewHelper::stripSlashesFromString ($shopDetail['shopRefUrl']);
        $this->actualUrl = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopActualUrl']);
        $this->affliateProgram = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['affiliateProgStatus']);
        $this->title =BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['shopTitle']);
        $this->subTitle =BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['shopSubTitle']);
        $this->overriteTitle = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopOverwriteTitle']);
        $this->shopText = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopDescription']);
        $shopViewCount = isset($shopDetail['shopViewCount']) ? $shopDetail['shopViewCount'] : '0';
        $this->views = BackEnd_Helper_viewHelper::stripSlashesFromString($shopViewCount);

        $this->howtoTitle = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pageTitle']);
        $this->howtoSubtitle = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pageSubTitle']);
        $this->howtoMetaTitle = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pagemetaTitle']);
        $this->howtoMetaDescription = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['pagemetaDesc']);
        $this->customHeader = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopCustomHeader']);
        $this->howToIntroductionText = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['howToIntroductionText']);

        $this->showSimliarShops = BackEnd_Helper_viewHelper::stripSlashesFromString(
            !empty($shopDetail['similarShops']) ? $shopDetail['similarShops'] : '0');
        $showChains = !empty($shopDetail['showChains']) ? $shopDetail['showChains'] : '0';
        $this->showChains = BackEnd_Helper_viewHelper::stripSlashesFromString($showChains);
        $strictConfirmation = !empty($shopDetail['strictConfirmation']) ? $shopDetail['strictConfirmation'] : '0';
        $this->strictConfirmation = BackEnd_Helper_viewHelper::stripSlashesFromString($strictConfirmation);

        // shop extra properties
        $displayExtraProperties = !empty($shopDetail['displayExtraProperties']) ? $shopDetail['displayExtraProperties'] : '0';
        $this->displayExtraProperties = BackEnd_Helper_viewHelper::stripSlashesFromString($displayExtraProperties);

        # display signup option on store detail page
        $this->showSignupOption = BackEnd_Helper_viewHelper::stripSlashesFromString(
            !empty($shopDetail['signupOption']) ? $shopDetail['signupOption'] : '0');


        if( BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['displayExtraProperties']) ) {
            $this->ideal = BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['ideal']) ? $shopDetail['ideal'] : 0);
            $this->qShops = BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['qShops']) ? $shopDetail['qShops'] : 0);
            $this->freeReturns = BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['freeReturns']) ? $shopDetail['freeReturns'] : 0);
            $this->pickupPoints = BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['pickupPoints']) ? $shopDetail['pickupPoints'] : 0);
            $this->mobileShop = BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['mobileShop']) ? $shopDetail['mobileShop'] : 0);
            $this->service = BackEnd_Helper_viewHelper::stripSlashesFromString(
                !empty($shopDetail['service']) ? $shopDetail['service'] : 0);


            if( BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['service']) ) {

                $this->serviceNumber = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['serviceNumber']);

            }
        }

        $this->discussions = '0';

        if(isset($shopDetail['discussions'])){
            $this->discussions = '1';
        }

        $this->usergenratedcontent = '0';

        if(isset($shopDetail['usergenratedchk'])){
            $this->usergenratedcontent = '1';
        }

        //$this->keywordlink = '';
        if(isset($shopDetail['keywordlink'])){
            $this->keywordlink = $shopDetail['keywordlink'];
        }


        if( isset( $shopDetail['onlineStatus'] )) {

            if( $shopDetail['onlineStatus'] == 1) {
                $this->status = 1;
                $this->offlineSicne = null;
            } else {

                $this->status = 0;

                if( strlen($shopDetail['offlineSince'])  > 18  ) {
                    $this->offlineSicne = $shopDetail['offlineSince'] ;
                } else {
                $this->offlineSicne = date("Y-m-d h:m:s") ;

                }
            }

        } else  {

            $this->status = 1 ;
        }

        $this->discussions = '0';

        if(isset($shopDetail['discussions'])){
            $this->discussions = '1';
        }
        $selectAccountManagers = isset($shopDetail['selectaccountmanagers']) ? $shopDetail['selectaccountmanagers'] : '0';
        $this->accoutManagerId = BackEnd_Helper_viewHelper::stripSlashesFromString($selectAccountManagers);
        $this->accountManagerName = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['accountManagerName']);
        $this->contentManagerId = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['selecteditors']);
        $this->contentManagerName = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['editorName']);

        $this->affliateNetworkId = NULL;

        if($shopDetail['shopAffiliateNetwork']!= 0){

                $this->affliateNetworkId = BackEnd_Helper_viewHelper::stripSlashesFromString($shopDetail['shopAffiliateNetwork']);
        }

        $this->howToUse = $shopDetail['howTouseStatus'];

        if (intval($shopDetail['howTouseStatus']) > 0) {
            if (isset($shopDetail['shopHowToUsePageId'])) {
                $this->howtoUsepageId = BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['shopHowToUsePageId']);
            }

            //  upload small logo image for how to use page
            if (isset($_FILES['smallLogoFile']['name']) && $_FILES['smallLogoFile']['name'] != '') {

                $uploadPath = UPLOAD_IMG_PATH . "shop/";
                if (!file_exists($uploadPath))
                    mkdir($uploadPath, 0776, true);

                $result = self::uploadImage('smallLogoFile',$uploadPath);
                //print_r($result); die;
                if ($result['status'] == '200') {
                    $ext = BackEnd_Helper_viewHelper::getImageExtension(
                            $result['fileName']);

                    $this->howtousesmallimage->ext = $ext;
                    $this->howtousesmallimage->path = BackEnd_Helper_viewHelper::stripSlashesFromString($result['path']);
                    $this->howtousesmallimage->name = BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);
                }else{

                    return false;

                }

            }

            //  upload big logo image for how to use page
            if (isset($_FILES['bigLogoFile']['name']) && $_FILES['bigLogoFile']['name'] != '') {
                $uploadPath = UPLOAD_IMG_PATH . "shop/";

                if (!file_exists($uploadPath))
                    mkdir($uploadPath, 0776, true);

                $result = self::uploadImage('bigLogoFile',$uploadPath);

                if ($result['status'] == '200') {
                    $ext = BackEnd_Helper_viewHelper::getImageExtension(
                            $result['fileName']);

                    $this->howtousebigimage->ext = $ext;
                    $this->howtousebigimage->path = BackEnd_Helper_viewHelper::stripSlashesFromString($result['path']);
                    $this->howtousebigimage->name = BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);

                }else{

                    return false;

                }
            }

        }

        if (!empty($shopDetail['selectedCategoryies'])) {

            $this->refShopCategory->delete();
            foreach ($shopDetail['selectedCategoryies'] as $key =>$categories) {

                $this->refShopCategory[]->categoryId = $categories;
            }
        }
 
        if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {

            $uploadPath = UPLOAD_IMG_PATH . "shop/";
            if (!file_exists($uploadPath))
                mkdir($uploadPath, 0776, true);
            $result = self::uploadImage('logoFile',$uploadPath);

            if ($result['status'] == '200') {
                $ext = BackEnd_Helper_viewHelper::getImageExtension(
                        $result['fileName']);
                $this->logo->ext = $ext;
                $this->logo->path = BackEnd_Helper_viewHelper::stripSlashesFromString($result['path']);
                $this->logo->name = BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);

            } else {

                return false;

            }
        }

        if (isset($_FILES['websitescreenshot']['name']) && $_FILES['websitescreenshot']['name'] != '') {

            $uploadPath = UPLOAD_IMG_PATH . "screenshot/";
            if (!file_exists($uploadPath))
                mkdir($uploadPath, 0776, true);

            $result = self::uploadImage('websitescreenshot',$uploadPath);

            if ($result['status'] == '200') {

                $ext = BackEnd_Helper_viewHelper::getImageExtension(
                        $result['fileName']);
                $this->screenshot->ext = $ext;
                $this->screenshot->path = BackEnd_Helper_viewHelper::stripSlashesFromString($result['path']);
                $this->screenshot->name = BackEnd_Helper_viewHelper::stripSlashesFromString($result['fileName']);

            } else {

                return false;
            }

        }
        //call cache function
        $key = 'shopDetails_'  . $this->id . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $cacheKeyOfferDetails = 'offerDetails_'  . $this->id . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKeyOfferDetails); 

        
        $key = 'shop_similar_shops';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $cacheKey = FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($shopDetail['shopNavUrl']);
        $key = 'store_'.$cacheKey.'_howToGuide';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allCategoriesOf_shoppage_'. $this->id);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('12_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');

        if(!empty($shopDetail['id'])) {
            $getcategory = Doctrine_Query::create()->select()->from('Shop')->where('id = '.$shopDetail['id'] )->fetchArray();
        }
        if(!empty($getcategory[0]['permaLink'])){

            $getRouteLink = Doctrine_Query::create()->select()->from('RoutePermalink')->where("permalink = '".$getcategory[0]['permaLink']."'")->andWhere('type = "SHP"')->fetchArray();
            $howToguideRoute = Doctrine_Query::create()->select()->from('RoutePermalink')->where("permalink = 'how-to/".$getRouteLink[0]['permalink']."'")->andWhere('type = "SHP"')->fetchArray();
        }

        try {

            $this->refShopRelatedshop->delete();
            $this->save();

        

            $key = 'shop_similar_shops';
            FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            if(!empty($getRouteLink)){

                $exactLink = 'store/storedetail/id/'.$this->id;
                $howtoguide = 'store/howtoguide/shopid/'.$this->id;
                $updateRouteLink = Doctrine_Query::create()->update('RoutePermalink')
                ->set('permalink', "'".
                        BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['shopNavUrl'])
                        ."'")
                ->set('type',"'SHP'")
                ->set('exactlink', "'". $exactLink."'" );
                $updateRouteLink->where('type = "SHP"')->andWhere("permalink = '".$getRouteLink[0]['permalink']."'")->execute();


                if(!empty($howToguideRoute)){
                    $updateRouteHow = Doctrine_Query::create()->update('RoutePermalink')
                    ->set('permalink', "'how-to/".BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['shopNavUrl'])."'")
                    ->set('type',"'SHP'")
                    ->set('exactlink', "'".$howtoguide."'" );
                    $updateRouteHow->where('type = "SHP"')->andWhere("permalink = 'how-to/".$getRouteLink[0]['permalink']."'")->execute();

                }else{
                    $route = new RoutePermalink();
                    $route->permalink = "how-to/" . BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['shopNavUrl']);
                    $route->type = 'SHP';
                    $route->exactlink = 'store/howtoguide/shopid/'.$this->id;
                    $route->save();
                }

            }else{
                $route = new RoutePermalink();
                $route->permalink = BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['shopNavUrl']);
                $route->type = 'SHP';
                $route->exactlink = 'store/storedetail/id/'.$this->id;
                $route->save();

                $route = new RoutePermalink();
                $route->permalink = "how-to/" . BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['shopNavUrl']);
                $route->type = 'SHP';
                $route->exactlink = 'store/howtoguide/shopid/'.$this->id;
                $route->save();
            }


            if(isset($shopDetail['similarstoreord'])){

                $similarstoreordArray = explode(',',$shopDetail['similarstoreord']);
                $i = 1;
                foreach ($similarstoreordArray as $shop) {
                    if($shop!=''){
                        $relateshopObj = new refShopRelatedshop();
                        $relateshopObj->shopId = $this->id;
                        $relateshopObj->relatedshopId = $shop;
                        $relateshopObj->position = $i;
                        $relateshopObj->save();
                        ++$i;
                    }
                }
            }

            if(!empty($shopDetail['title']) && !empty($shopDetail['content'])) {

                $delChapters = Doctrine_Query::create()->delete("ShopHowToChapter")->where("shopId = ".$this->id)->execute();
                foreach ($shopDetail['title'] as $key => $title) {
                    if(!empty($shopDetail['title'][$key]) && !empty($shopDetail['content'][$key])){
                        $chapter = new ShopHowToChapter();
                        $chapter->shopId = $this->id;
                        $chapter->chapterTitle = BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['title'][$key] );
                        $chapter->chapterDescription = BackEnd_Helper_viewHelper::stripSlashesFromString( $shopDetail['content'][$key]) ;
                        $chapter->save();
                    }
                }
            }
            return $this->id ;

        } catch (Exception $e) {
            //print_r($e); die;
            return false;
        }

    }

    /**
     * upload image
     * @param $_FILES[index]  $file
     */
    public function uploadImage($file,$path)
    {
        // generate upload path for images related to shop
        $uploadPath = $path;
        if (!file_exists(UPLOAD_IMG_PATH))
            mkdir($uploadPath, 0776, true);

        $adapter = new Zend_File_Transfer_Adapter_Http();

        // generate real path for upload path

        $rootPath = ROOT_PATH . $uploadPath;
        // echo $rootPath;
        //die;
        // get upload file info
        $files = $adapter->getFileInfo($file);

        // check upload directory exists, if no then create upload directory
        if (!file_exists($rootPath))
            mkdir($rootPath ,776, true);

        // set destination path and apply validations
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, array('jpg,jpeg,png', true));
        $adapter->addValidator('Size', false, array('max' => '2MB'));
        // get file name
        $name = $adapter->getFileName($file, false);

        // rename file name to by prefixing current unix timestamp
        $newName = time() . "_" . $name;

        // generates complete path of image
        $cp = $rootPath . $newName;
        $path = ROOT_PATH . $uploadPath . "thum_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file] , $newName , 132, 95, $path);

        if($file=='bigLogoFile') {
            $path = ROOT_PATH . $uploadPath . "thum_bigLogoFile_" . $newName;
            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 280, 197, $path);
        }
        if($file=='smallLogoFile') {
            $path = ROOT_PATH . $uploadPath . "thum_smallLogoFile_" . $newName;
            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 280, 100, $path);
        }
        /**
         *   generating thumnails for upload logo if file in shop logo
         */
        if ($file == "logoFile") {

            $path = ROOT_PATH . $uploadPath . "thum_large_" . $newName;
            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 200, 150, $path);

            //espect ratio
            $path = ROOT_PATH . $uploadPath . "thum_small_" . $newName;
            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 84, 42, $path);

            $path = ROOT_PATH . $uploadPath . "thum_medium_" . $newName;
            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 100, 50, $path);

            $path = ROOT_PATH . $uploadPath . "thum_big_" . $newName;
            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 236, 118, $path);


            $path = ROOT_PATH . $uploadPath . "thum_expired_" . $newName;
            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 100, 50, $path);

            $path = ROOT_PATH . $uploadPath . "thum_medium_store_" . $newName;
            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 200, 100, $path);

        }
        if ($file == "websitescreenshot") {

            $path1 = ROOT_PATH . $uploadPath . "thum_large_" . $newName;
            BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 450,0, $path1);
            //die('Hello');

        }
        // apply filter to rename file name and set target
        $adapter
                ->addFilter(
                        new Zend_Filter_File_Rename(
                                array('target' => $cp, 'overwrite' => true)),
                        null, $file);

        // recieve file for upload
        $adapter->receive($file);

        // check is file is valid then
        //echo "<pre>"; print_r($adapter->isValid($file)); die;
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
        $shopList = $queryBuilder->select('s.name,l.id as logoId,l.name,l.path')
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
        $shopDetail = $queryBuilder->select('s.notes,s.accountManagerName,s.deepLink,s.deepLinkStatus,s.strictConfirmation,a.name as affname,cat.id')
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

        $network = Shop::getAffliateNetworkDetail($shopId);

        if ($checkRefUrl) {
            # retur false if s shop is not associated with any network
            if (! isset($network['affname'])) {
                return false;
            }
            if (isset($data['deepLink']) && $data['deepLink']!=null) {
                # deeplink is now commetted for the time being, so we always @return false ;
                return false;
            } elseif (isset($data['refUrl']) && $data['refUrl']!=null) {
                return true;
            } else {
                return true;
            }
        }
        $subid = "" ;
        if (isset($network['affname'])) {
            if (!empty($network['subid'])) {
                 $subid = "&". $network['subid'] ;
                 $clientIP = \FrontEnd_Helper_viewHelper::getRealIpAddress();
                 $ip = ip2long($clientIP);

                 # get click detail and replcae A2ASUBID click subid
                 $conversion = \KC\Repository\Conversions::getConversionId($data['id'], $ip, 'shop');
                 $subid = str_replace('A2ASUBID', $conversion['subid'], $subid);
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
        if (Shop::getStoreLinks($id, true)) {
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

    // to be checked and migrated
    public static function getAllUrls($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select("s.id, s.permaLink,s.contentManagerId,c.id as test, s.howToUse, o.extendedOffer, o.extendedUrl")
            ->from('KC\Entity\Shop', 's')
            ->leftJoin("s.offer", "o")
            ->leftJoin("s.categoryshops", "c")
            ->where("s.id= ". $id);
        $shop = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        print_r($shop); die;
        # redactie permalink
        $redactie =  User::returnEditorUrl($shop['contentManagerId']);

        $urlsArray = array();

        # check for related shop permalink
        if(isset($shop['permaLink'])) {
            $urlsArray[] = $shop['permaLink'];
        }

        # check for ho to use guide
        if($shop['howToUse']) {
            # check for extende offer url
            if( isset($shop['permaLink'])  && strlen( $shop['permaLink'] ) > 0 ) {
                $urlsArray[] = FrontEnd_Helper_viewHelper::__link('link_how-to') .'/'.$shop['permaLink'];
            }
        }

        # check if an editor  has permalink then add it into array
        if(isset($redactie['permalink']) && strlen($redactie['permalink']) > 0 ) {
            $urlsArray[] = $redactie['permalink'] ;
        }

        # check an offerr has one or more categories
        if(isset($shop['category']) && count($shop['category']) > 0) {

            $categoriesPage = FrontEnd_Helper_viewHelper::__link('link_categorieen') .'/' ;

            # traverse through all catgories
            foreach($shop['category'] as $value) {
                # check if a category has permalink then add it into array
                if (isset($value['permaLink']) && strlen($value['permaLink']) > 0) {
                    $urlsArray[] = $categoriesPage . $value['permaLink'];
                    $urlsArray[] = $categoriesPage . $value['permaLink'] .'/2';
                    $urlsArray[] = $categoriesPage . $value['permaLink'] .'/3';
                }
            }
        }

        # check extended offer of this shop
        if(isset($shop['offer']) && count($shop['offer']) > 0) {
            # traverse through all offer
            foreach( $shop['offer'] as $value) {
                # check the offer is extended or not
                if(isset($value['extendedOffer']) && $value['extendedOffer']  ) {
                    $urlsArray[] = FrontEnd_Helper_viewHelper::__link('link_deals') .'/'. $value['extendedUrl'] ;
                }
            }
        }
        return $urlsArray ;
    }

    public static function changeStatus($params)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $status = $params['status'] == 'offline' ? '0' : '1';

        if ($params['status'] == 'offline') {
            $date = date('Y-m-d H:i:s');
            $status = 0 ;
        } else {
            $status = 1 ;
            $date = null;
        }

        $query = $queryBuilder->update('KC\Entity\Shop', 's')
            ->set('s.status', $queryBuilder->expr()->literal($status))
            ->set('s.offlineSicne', $queryBuilder->expr()->literal($date))
            ->where('s.id = ?1')
            ->setParameter(1, $params['id'])
            ->getQuery();
        $shop = $query->execute();
        $key = 'shop_similar_shops';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shopDetails_'.$params['id'].'_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        return $date;
    }


    //to be refactored
    public function postUpdate($event)
    {


        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');


        $key = 'shopDetails_'. $this->id.'_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $cacheKeyOfferDetails = 'offerDetails_'  . $this->id . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($cacheKeyOfferDetails); 


        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allCategoriesOf_shoppage_'. $this->id);


        try {

            $data = $this->getLastModified();

            # update chain if shop is associated with chain
            if($this->chainItemId) {

                $chainItem = Doctrine_Core::getTable("ChainItem") ->findBySql(
                                    'shopId = ? AND id = ?',
                                    array($this->id,$this->chainItemId),
                                    Doctrine::HYDRATE_RECORD)->getData();

                # verify a valid chain item exists
                if(isset($chainItem[0])) {
                     $chainItem[0]->update($data,$this->toArray(false));
                }

            }



        } catch (Exception $e) {

            return false ;
        }


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
    //error to be migrated
    public static function getFavouriteCountOfShop($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select("count(s.id)")
                ->from('KC\Entity\Shop', 's')
                ->leftJoin('s.visitors', 'v')
                ->where('s.visitors='.$shopId);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data['count'];
    }

    public static function getshopStatus($shopId)
    {
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
}

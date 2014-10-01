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
        $relatedShops = Doctrine_Query::create()->from('Shop s')
            ->select(
                "s.name, s.permaLink, img.path, img.name, logo.path, logo.name, rs.name, rs.permaLink,
                c.id,ss.name, ss.permaLink"
            )
            ->where("s.id = ".$shopId)
            ->leftJoin("s.relatedshops rs")
            ->andWhere("rs.status = 1")
            ->andWhere("rs.deleted = 0")
            ->leftJoin("rs.logo as logo")
            ->leftJoin('s.category c')
            ->andWhere("c.status = 1")
            ->andWhere("c.deleted = 0")
            ->leftJoin('c.shop ss')
            ->andWhere("ss.status = 1")
            ->andWhere("ss.deleted = 0")
            ->leftJoin('ss.logo img')
            ->fetchArray(null, Doctrine::HYDRATE_ARRAY);
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
        $popularStoreData = Doctrine_Query::create()
        ->select(
            'o.id,o.exclusiveCode,p.id,s.name,s.permaLink,s.deepLink,s.deepLinkStatus,s.refUrl,s.actualUrl,
            s.Deliverytime, s.returnPolicy, s.freeDelivery, p.type,p.position,p.shopId, img.path as imgpath, 
            img.name as imgname'
        )
        ->from('PopularShop p')
        ->addSelect(
            "(SELECT COUNT(*) FROM Offer exclusive WHERE exclusive.shopId = s.id AND
            (o.exclusiveCode=1 AND exclusive.endDate > '$currentDate')) as exclusiveCount"
        )
        ->addSelect("(SELECT COUNT(*) FROM PopularCode WHERE offerId = o.id ) as popularCount")
        ->addSelect(
            "(SELECT COUNT(*) FROM Offer active WHERE
            (active.shopId = s.id AND active.endDate >= '$currentDate' 
                AND active.deleted = 0
            )
            ) as activeCount"
        )
        ->leftJoin('p.shop s')
        ->leftJoin('s.offer o')
        ->leftJoin('s.logo img')
        ->where('s.deleted=0')
        ->addWhere('s.status=1')
        ->orderBy('p.position ASC');

        if ($shopId) {
            $popularStoreData = $popularStoreData->andWhere("s.id = ? ", $shopId);
        } else {
            $popularStoreData = $popularStoreData->limit($limit);
        }

        $popularStoreData = $popularStoreData->fetchArray();
        return $popularStoreData;
    }

    public static function getStoreDetails($shopId)
    {
        $storeDetail = Doctrine_Query::create()->select('s.*,img.*,scr.*,small.*,big.*')
        ->from('Shop s')
        ->leftJoin('s.logo img')
        ->leftJoin('s.smallimage small')
        ->leftJoin('s.bigimage big')
        ->leftJoin('s.affliatenetwork aff')
        ->leftJoin('s.screenshot scr')
        ->where('s.id='.$shopId)
        ->andWhere('s.deleted=0')
        ->andWhere('s.status=1');
        $allStoresDetail = $storeDetail->fetchArray(array(), Doctrine_Core::HYDRATE_ARRAY);
        return $allStoresDetail;
    }

    public static function getallStoresForFrontEnd()
    {
        $currentDateAndTime = date('Y-m-d 00:00:00');
        $storeInformation = Doctrine_Query::create()
        ->select('o.id,s.id, s.name, s.permaLink as permalink')
        ->from('Shop s')
        ->addSelect(
            "(SELECT COUNT(*) FROM Offer exclusive WHERE exclusive.shopId = s.id AND
                (o.exclusiveCode=1 AND o.endDate > '$currentDateAndTime')) as exclusiveCount"
        )
        ->addSelect("(SELECT COUNT(*) FROM PopularCode WHERE offerId = o.id ) as popularCount")
        ->leftJoin('s.offer o')
        ->leftJoin('s.logo img')
        ->where('s.deleted=0')
        ->addWhere('s.status=1')
        ->orderBy('s.name')->fetchArray();

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
                    "permaLink"=>$store['permalink'],
                    "name"=>$store['name'],
                    "exclusive"=>$store['exclusiveCount'],
                    "inpopular"=>$store['popularCount']);
            }
        }
        return $storesForFrontend;
    }

    public static function getAllPopularStores($limit)
    {
        $popularStores = Doctrine_Query::create()
        ->select('p.id,s.name,s.permaLink,img.path as imgpath, img.name as imgname')
        ->from('PopularShop p')
        ->leftJoin('p.shop s')
        ->leftJoin('s.logo img')
        ->where('s.deleted=0')
        ->addWhere('s.status=1')
        ->orderBy('p.position ASC')
        ->limit($limit)->fetchArray();
        return $popularStores;
    }

    public static function getshopDetails($permalink)
    {
        $shopDetails = Doctrine_Query::create()
        ->select('s.*,img.name,img.path,chptr.*')
        ->from('shop s')
        ->leftJoin('s.logo img')
        ->leftJoin('s.howtochapter chptr')
        ->Where("s.permaLink='".$permalink."'")
        ->andWhere('s.status = 1')
        ->fetchArray();
        return $shopDetails;
    }

    public static function getShopsByShopIds($shopIds)
    {
        $shopsInformation = Doctrine_Query::create()
            ->select('s.id, s.name,s.permaLink, img.path as imgpath, img.name as imgname')
            ->from("Shop s")
            ->leftJoin("s.logo img")
            ->where('s.deleted=0')
            ->andWhereIn("s.id", $shopIds)
            ->orderBy("s.name")->fetchArray();
        return $shopsInformation;
    }

    public static function getStoresForSearchByKeyword($searchedKeyword, $limit, $fromPage='')
    {
        $currentDate = date('Y-m-d 00:00:00');
        $storesByKeyword = Doctrine_Query::create()
            ->select('s.id,s.name,s.permaLink, img.path as imgpath, img.name as imgname')
            ->from('shop s')
            ->leftJoin('s.logo img')
            ->where('s.deleted=0')
            ->addWhere('s.status=1')
            ->andWhere("s.name LIKE ?", "%". $searchedKeyword."%");
        if ($fromPage!='') {
            $storesByKeyword->addSelect(
                "(SELECT COUNT(*) FROM Offer active WHERE
                (active.shopId = s.id AND active.endDate >= '$currentDate' 
                    AND active.deleted = 0
                )
                ) as activeCount"
            )
            ->leftJoin('s.offer o');
        }
        $stores = $storesByKeyword->limit($limit)->fetchArray();
        return $stores;
    }

    public static function shopAddInFavourite($visitorId, $shopId)
    {
        $addedStatus = 0;
        if ($shopId!='') {
            $favouriteShops  = Doctrine_Query::create()->from('FavoriteShop s')
            ->where('s.visitorId='.$visitorId)
            ->andWhere('s.shopId='.$shopId)->fetchOne();
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

    public static function getActiveOffersCount($shopId) {
        $currentDate = date('Y-m-d 00:00:00');
        $acitveOfferCount = Doctrine_Query::create()->select('count(o.id) as activeCount')
            ->from('Shop s')
            ->leftJoin('s.offer o')
            ->where('s.id='.$shopId)
            ->andWhere('o.enddate >'."'".$currentDate."'")
            ->andWhere('o.deleted=0')->fetchArray();
        return $acitveOfferCount;
    }

    public static function getShopName($shopId)
    {
        $shop = Doctrine_Query::create()->select('s.name')
            ->from('Shop s')
            ->where('s.id='.$shopId)->fetchArray();
        return isset($shop[0]['name']) ? $shop[0]['name'] : '';  
    }
    ##################################################################################
    ################## END REFACTORED CODE ###########################################
    ##################################################################################
    /**
     * addChain
     *
     * it will add chain item id to the  shop
     *
     * @param integer $id chain item id
     */
    public function addChain($id)
    {
        $this->chainItemId = $id;
        $this->save();

    }
    /**
     * getAllShopNames
     *
     * fetch all shop names
     * @param string $keyword shop name for search
     * @return array
     * @author sp singh
     */
    public function getAllShopNames($keyword)
    {
        return   Doctrine_Query::create()
                    ->select('s.name,s.permaLink,s.id')
                    ->from("Shop s")
                    ->where('s.deleted = ?', 0)
                    ->andWhere("s.status=1")
                    ->andWhere("s.name LIKE ?", "$keyword%")
                    ->fetchArray();
    }


    /**
     * getshopList fetch all record from database table shop
     * also search according to keyword if present.
     * call this function for both trash list and normal list
     * based on flag(0 (not deleted ) 1 (deleted ))
     * @param $params
     * @return array
     * @author mkaur updated by kraj
     * @version 1.0
     */
 public static  function getshopList($params)
 {
    $srh =  $params["searchText"]=='undefined' ? '' : $params["searchText"];
    $flag = @$params['flag'] ;

    $shopList = Doctrine_Query::create()
        ->select('s.*,a.name as affname')
        //->select('a.name as affname,s.id,s.updated_at,s.id,s.name,s.affliateProgram,s.accountManagerName,s.created_at,affname,s.status,s.offlineSicne,s.updated_at')
        ->from("Shop s")
        ->leftJoin('s.affliatenetwork a')
        ->where('s.deleted = ?' , $flag )
        ->andWhere("s.name LIKE ?", "%$srh%");
        $result =   DataTable_Helper::generateDataTableResponse($shopList,
                    $params,
                    array("__identifier" => 's.id,s.updated_at', 's.id','s.name','s.permaLink','s.affliateProgram','s.created_at','affname','s.discussions','s.showSignupOption','s.status','s.offlineSicne'),
                    array(),
                    array());
    return $result;

 }
    /**
     * move record in trash.
     * @param $id
     * @author kraj
     * @version 1.0
     */
    public static function moveToTrash($id)
    {
        if ($id) {
            //find record by id and apply sofdeleted (change status 0 to 1)
            $u = Doctrine_Core::getTable("Shop")->find($id);
            $u->delete();

        } else {

            $id = null;
        }
        //call cache function
        $key = 'shop_similar_shops';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvouchercode_list_shoppage');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allCategoriesOf_shoppage_'. $id);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');

        return $id;
    }


    /**
     * deleted record parmanetly from database.
     * @param $id
     * @author mkaur update by kuldeep
     * @version 1.0
     */
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
        if($this->deleted == 1) {


            $dela = Doctrine_Query::create()->delete()
             ->from('refShopCategory r')->where('r.shopid=' . $id)
            ->execute();

            $delb = Doctrine_Query::create()->delete()->from('PopularShop p')
            ->where('p.shopId=' . $id)->execute();

            $del2 = Doctrine_Query::create()->delete()->from('RefArticleStore p')
            ->where('p.storeid=' . $id)->execute();

            $offer  = Doctrine_Query::create()->select('o.id')->from('Offer o')->where('o.shopId='.$id)->fetchArray();
            foreach ($offer as $off){

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
            if($this->chainItemId) {
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
    /**
     * restore shop by id
     * @param $id
     * @author kraj
     * @version 1.0
     */
    public static function restoreShop($id)
    {
        if ($id) {

            $shop =  Doctrine_Core::getTable('Shop')->find($id);
            $shop->deleted = 0;
            $shop->save();
            return $id;

        }  else {
            return null;
        }
        //call cache function
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_shops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('25_popularshop_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_categories_of_shoppage');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('12_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('offers_by_searchedkeywords');
        return $id;
    }
    /**
     * searchKeyword
     *
     * Search top five shops and shows in autocomplete
     * use in both trash and normal list base on flage (flag 0(not deleted)
     * 1(deleted ))
     * @param string $flag
     * @param $shopDetail
     * @author mkaur updateb by kraj
     * @version 1.0
     */
    public static function searchKeyword($keyword, $flag)
    {
        $data = Doctrine_Query::create()->select('s.name as name')
                ->from("Shop s")
                ->where('s.deleted= ?' , $flag )
                ->andWhere("s.name LIKE ?", "%$keyword%")
                ->orderBy("s.name ASC")
                ->limit(5)
                ->fetchArray();
        return $data;

    }

    /**
     * searchsimilarStore
     *
     * Search top top related shops and shows in autocomplete
     * use in both trash and normal list base on flage (flag 0(not deleted)
     * 1(deleted ))
     *
     * @param string $flag
     * @param $shopDetail
     * @author mkaur updateb by kraj
     * @version 1.0
     */
    public static function searchsimilarStore($keyword, $flag, $selctedshop)
    {
        $data = Doctrine_Query::create()->select('s.name as name,s.id as id')
        ->from("Shop s")->where('s.deleted= ?' , $flag )
        ->andWhere("s.name LIKE ?", "$keyword%")
        ->andWhere("s.id NOT IN ($selctedshop)")
        ->orderBy("s.name ASC")
        ->limit(10)->fetchArray();
        return $data;
    }

    /**
     * CreateNewShop
     *
     * create new shop
     *
     * @param posted form data
     * @author kkumar
     * @version 1.0
     */
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
    /**
     * get list of shop for export
     * @author kraj
     * @return array $shopList
     * @version 1.0
     */
    public static function exportShopsList()
    {
        $shopList = Doctrine_Query::create()->select('s.*,a.name as affname,c.name,rs.name')
                ->from("Shop s")
                ->leftJoin('s.affliatenetwork a')->leftJoin("s.category c")
                ->addSelect("(SELECT con.updated_at FROM OfferNews  con  WHERE con.shopId = s.id order by updated_at Desc LIMIT 1) as newsTickerTime")
                ->addSelect("(SELECT o.updated_at FROM Offer o WHERE o.shopId = s.id and o.deleted = 0 order by updated_at Desc LIMIT 1) as offerTime")
                ->leftJoin("s.relatedshops rs")
                ->where("s.deleted=0")
                ->orderBy("s.id DESC")
                ->fetchArray();
        return $shopList;


    }



/* Offer List function start here */
/**
 * get list of shop for offerList
 * @author kkumar
 * @return array $shopList
 * @version 1.0
 */

    public static function getOfferShopList()
    {
        $shopList = Doctrine_Query::create()
        ->select('s.name,s.logoId,l.name,l.path')
        ->from("Shop s")
        ->leftJoin("s.logo l")
        ->where('s.deleted=0')
        ->addWhere('s.status=1')
        ->orderBy("s.name")->fetchArray();
        return $shopList;
   }

/**
 * @author kkumar
 * @param $shopId
 * @return shopDetail
 */
public static function getShopDetail($shopId)
{
        $shopDetail = Doctrine_Query::create()
        ->select('s.notes,s.accountManagerName,s.deepLink,s.deeplinkstatus,s.strictConfirmation,a.name as affname,cat.*')
        ->from("Shop s")
        ->leftJoin('s.affliatenetwork a')
        ->leftJoin('s.category cat')
        ->where('s.deleted=0')
        ->andWhere("s.id =$shopId")->fetchArray();
        return $shopDetail;

 }



 /**
  * @author Raman
  * @return shopDetail
  */
 public static function getShopPermalinks()
 {
    $permalinks = Doctrine_Query::create()
    ->select('s.permalink, s.howToUse')
    ->from("Shop s")
    ->where('s.deleted=0')
    ->andWhere('s.status=1')
    ->fetchArray();
    return $permalinks;

 }


 /**
  * @author Raman
  * @return shops permalinks
  */
 public static function getAllShopPermalinks()
 {
    $permalinks = Doctrine_Query::create()
    ->select('s.permalink')
    ->from("Shop s")
    ->fetchArray();
    return $permalinks;

 }
 /* Offer List function end here */




/*============================Function for front-end ======================= */

 /**
  * get total view count for shop and update the total viewcount
  * and also update its offers viewcount
  *
  * @author sp singh
  * @version 1.0
  */
 public static function updateTotalViewCount()
 {
    $nowDate = date('Y-m-d 00:00:00');
    $data = Doctrine_Query::create()
    ->select('s.id')
    ->from('Shop s')
    ->addSelect("(SELECT sum(v.onclick) as pop FROM ShopViewCount v WHERE v.shopid = s.id ) as clicks")
    ->fetchArray();


    foreach ($data as $value) {
        if($value['clicks']) {
            $shopList = Doctrine_Query::create()
            ->update('Shop s')
            ->set('s.totalViewcount', $value['clicks'])
            ->where('s.id = ?', $value['id'])
            ->execute();

        }
    }

 }

 /**
  * get first character of the store name
  * @return character $var
  * @return $filteredCharacter
  */
 public static function filterFirstCharacter($var)
 {
    $filteredCharacter = substr($var, 0,1);
    return $filteredCharacter;
 }
 /**
  * get all store if has exclusive deal
  * @author kraj
  * @version 0.1
  * @param integer $shopId
  * @return Ambigous <multitype:, multitype:unknown number >
  */
 public static function getStoreExclusiveDeal($shopId)
 {
    $nowDate = date('Y-m-d 00:00:00');
    //echo $nowDate ;
    //die();
    $Q = Doctrine_Query::create()->from('Offer o')
    ->where('o.shopId='.$shopId)
    ->andWhere('o.exclusiveCode=1 AND o.endDate >'."'$nowDate'")
    ->fetchArray();
    return $Q;
 }
 /**
  * get store if has offer in popular
  * @author kraj
  * @param integer $shopId
  * @version 0.1
  * @return Ambigous <multitype:, multitype:unknown number >
  */
 public static function getStoreOfferInPopularOrNot($shopId)
 {
    $nowDate = date('Y-m-d 00:00:00');
    $Q = Doctrine_Query::create()->from('Offer o')
    ->leftJoin('o.popularcode p')
    ->where('o.shopId='.$shopId)
    ->fetchArray();
    //echo "<pre>";
    return $Q;
    //die();
    //return count($Q['popularcode']);
 }
 /**
  * get store if has offer in active stage
  * @author kraj
  * @param integer $shopId
  * @version 0.1
  * @return Ambigous <multitype:, multitype:unknown number >
  */
 public static function getStoreHasActiveCode($shopId)
 {
    $nowDate = date('Y-m-d 00:00:00');
    $Q = Doctrine_Query::create()->from('Offer o')
    ->where('o.shopId='.$shopId)
    ->andWhere('o.endDate >'."'$nowDate'")
    ->fetchArray();
    //echo "<pre>";
    return $Q;
    //die();
    //return count($Q['popularcode']);
 }

 /**
  * get recent stores (Shop) for front end
  * @author Er.Kundal
  * @version 1.0
  * @return array $data
  */
 public static function getrecentstores($flag)
 {
    $shops = Doctrine_Query::create()->select('s.id, s.name, s.permaLink, img.path as imgpath, img.name as imgname')
    ->from('Shop s')
    ->leftJoin('s.logo img')
    ->where('s.deleted=0')
    ->orderBy('s.id DESC')
    ->limit($flag)->fetchArray();

    return $shops;
 }
 /**
  * Search shop from shop table
  * @param string $keyword
  * @author kraj
  * @return array $data
  * @version 1.0
  */
 public static function commonSearchStore($keyword,$limit)
 {
        $redirectKeywods = ExcludedKeyword::getExRedirectKeywordsList();

        $data = Doctrine_Query::create()
        ->select('s.name as name,s.id as id,s.permaLink as permalink')
        ->from("Shop s")
        ->where('s.deleted= 0')
        ->andWhere("s.name LIKE ?", "$keyword%")
        ->andWhere('s.status=1')
        //->whereNotIn("s.name", $redirectKeywods)
        ->orderBy("s.name ASC")
        ->limit($limit)
        ->fetchArray();
        return $data;
 }


 /**
  * get all shops for the json file for frontend search
  * @author sp singh
  * @return array

  */

 public static function getAllStores()
 {
    $redirectKeywods = ExcludedKeyword::getExRedirectKeywordsList();

    $data = Doctrine_Query::create()
        ->select('s.name as name,s.id as id,s.permaLink as permalink')
        ->from("Shop s")
        ->where('s.deleted= 0')
        ->andWhere('s.status=1')
        ->orderBy("s.name ASC")
        ->fetchArray();

    return $data;
 }


 /**
  * Search shop from shop table
  * @param string $keyword
  * @author kraj
  * @return array $data
  * @version 1.0
  */
 public static function commonSearchStoreForUserGenerated($keyword,$limit)
 {
    $data = Doctrine_Query::create()
    ->select('s.name as name,s.id as id,s.permaLink as permalink')
    ->from("Shop s")
    ->where('s.deleted=0')
    ->andWhere("s.name LIKE ?", "$keyword%")
    ->andWhere("s.usergenratedcontent=1")
    ->andWhere('s.status=1')
    ->orderBy("s.name ASC")
    ->limit($limit)->fetchArray();
    return $data;
 }

    public static function getAllShopDetails()
    {
        $shopList = Doctrine_Query::create()
        ->select('s.name')
        ->from("Shop s")
        ->fetchArray();
        return $shopList;

    }

    public static function updateShop($name, $shop_text, $freDel, $delCost)
    {
        $shopList = Doctrine_Query::create()
        ->update('Shop s')
        ->set('s.shoptext', '?1')
        ->set('u.email', '?2')
        ->where('u.id = ?3')
        ->setParameter(1, $username)
        ->setParameter(2, $email)
        ->setParameter(3, $editId)
        ->execute();
        //$p = $q->execute();


    }

    public static function deletechapters($id)
    {
        $data = Doctrine_Query::create()
        ->delete('ShopHowToChapter s')
        ->where('s.id ='.$id)
        ->execute();

        return $data;
    }



    /**
     * getStoreLinks
     * get links for a shop for cloaking purpose
     *
     * @author Raman modified by Surindetrpal Singhj
     * @param integer $offerId id of an offer
     * @param boolean $checkRefUrl if true then check only the shop has ref url|deep link  or not
     *                                     if false then return the outgoing link
     * @return array|boolean $data
     * @version 1.0
     */
    public static function getStoreLinks($shopId , $checkRefUrl = false)
    {

        $data = Doctrine_Query::create()->select('s.permaLink as permalink, s.deepLink, s.deepLinkStatus, s.refUrl, s.actualUrl')
        ->from('Shop s')
        ->where('s.id='.$shopId)
        ->fetchOne(null,Doctrine::HYDRATE_ARRAY );

        $network = Shop::getAffliateNetworkDetail( $shopId );

        if($checkRefUrl) {
            # retur false if s shop is not associated with any network
            if(! isset($network['affliatenetwork'])) {
                return false ;
            }

            if(isset($data['deepLink']) && $data['deepLink']!=null){

                # deeplink is now commetted for the time being, so we always @return false ;
                return false;
            }elseif(isset($data['refUrl']) && $data['refUrl']!=null){
                return true ;
            }else{
                return true ;
            }
        }

        $subid = "" ;
        if( isset($network['affliatenetwork']) ) {
            if(!empty($network['subid']) ) {
                 $subid = "&". $network['subid'] ;


                 $clientIP = FrontEnd_Helper_viewHelper::getRealIpAddress();
                 $ip = ip2long($clientIP);

                 # get click detail and replcae A2ASUBID click subid
                 $conversion = Conversions::getConversionId( $data['id'] , $ip , 'shop') ;

                 $subid = str_replace('A2ASUBID',$conversion['subid'] , $subid );
            }
        }

        # deeplink is now commetted for the time being, so we always return false ;
        /*if(isset($data['deepLink']) && $data['deepLink']!=null){
            $url = $data['deepLink'];
            $url .=  $subid ;

        }else*/

        if(isset($data['refUrl']) && $data['refUrl']!=null){
            $url = $data['refUrl'];
            $url .=  $subid ;
        }elseif (isset($data['actualUrl']) && $data['actualUrl']!=null){
            $url = $data['actualUrl'];
        }else{
            $url = HTTP_PATH_LOCALE.@$data['permaLink'];

        }
        return $url ;
    }

    /**
     * addConversion
     * add a conversion to a shop which is associted with a network
     *
     * @param integeter $id shopId
     * @auther Surinderpal Singh
     */

    public static function addConversion($id)
    {
        $clientIP = FrontEnd_Helper_viewHelper::getRealIpAddress();
        $ip = ip2long($clientIP);

        # save conversion detail if an offer is associated with a network
        if(Shop::getStoreLinks($id , true )) {

            # check for previous cnversion of same ip
            $data = Doctrine_Query::create()
                ->select('count(c.id) as exists,c.id')
                ->from('Conversions c')
                ->andWhere('c.shopId="'.$id.'"')
                ->andWhere('c.IP="'.$ip.'"')
                ->andWhere("c.converted=0")
                ->groupBy('c.id')
                ->fetchOne(null, Doctrine::HYDRATE_ARRAY);

            if(! $data['exists']) {

                # save conversion detail if an offer is associated with a network
                $cnt  = new Conversions();
                $cnt->shopId = $id;
                $cnt->IP = $ip;
                $cnt->utma = $_COOKIE["__utma"];
                $cnt->utmz = $_COOKIE["__utmz"];
                $time = time();
                $cnt->subid = md5(time()*rand(1,999));
                $cnt->save();
            } else{


                # update existing conversion detail
                $cnt = Doctrine_Core::getTable("Conversions")->find($data['id']);
                if($cnt) {
                    $cnt->utma = $_COOKIE["__utma"];
                    $cnt->utmz = $_COOKIE["__utmz"];
                    $time = time();
                    $cnt->subid = md5(time()*rand(1,999));
                    $cnt->save();
                }
            }
        }
    }

    /**
     * getAffliateNetworkDetail
     *
     *  get the affliate network detail for the given shop
     *
     * @param integer $shopId
     * @retun array
     */
    public static function getAffliateNetworkDetail($shopId)
    {

            return  Doctrine_Query::create()
                        ->select('s.id,a.name as affname,a.subId as subid')
                        ->from("Shop s")
                        ->leftJoin('s.affliatenetwork a')
                        ->where('s.deleted=0')
                        ->andWhere("s.id =?" , $shopId)
                        ->fetchOne(null, Doctrine::HYDRATE_ARRAY);


    }

    /**
     * getAllUrls
     *
     * returns the all the urls related to a offer like  special list pages,
     * realted extended offer page, realted category pages, sreach pages, redactie pages,
     * pageRelated How to use etc
     * @param integer $id shop id
     * @author Surinderpal Singh
     * @return array array of urls
     */
    public static function getAllUrls($id)
    {
        $shop  = Doctrine_Query::create()
                    ->select("s.id, s.permaLink,s.contentManagerId, s.howToUse,c.permaLink, o.extendedOffer, o.extendedUrl,")
                    ->from('Shop s')
                    ->leftJoin("s.offer o")
                    ->leftJoin("s.category c")
                    ->where("s.id=? " , $id)
                    ->fetchOne(null, Doctrine::HYDRATE_ARRAY);

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

    /**
     * change status of shop
     * @param array $params
     * @author blal
     * @version 1.0
     */
    public static function changeStatus($params)
    {
        $status = $params['status'] == 'offline' ? '0' : '1';

        $shop = Doctrine_Core::getTable("Shop")->find($params['id']);

        if($params['status'] == 'offline') {
            $date = date('Y-m-d H:i:s')  ;
            $status = 0 ;
        } else {
            $status = 1 ;
            $date = NULL ;
        }

        $shop->status = $status;
        $shop->offlineSicne = $date;
        $shop->save();
        
        $key = 'shop_similar_shops';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shopDetails_'.$params['id'].'_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        return $shop->offlineSicne;


    }


    /*
     * execute when a shop is being updated. Based on modief
     * We update shop inforamation in chain table based on modified data
     *
     *  exmaple status,name,permalink and deleted to update chain
     *
     */
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



    /**
     * get No of Shops created in last 7 days for dashboard
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public static function getAmountShopsCreatedLastWeek()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        // - 7 days from today
        $past7Days = date($format, strtotime('-7 day' . $date));
        $nowDate = $date;

        $data = Doctrine_Query::create()
        ->select("count(*) as amountshops")
        ->from('Shop s')
        ->where('s.deleted=0')
        ->andWhere('s.created_at BETWEEN "'.$past7Days.'" AND "'.$date.'"')
        //->andWhere('s.status=1')
        ->fetchOne(null, Doctrine::HYDRATE_ARRAY) ;

        return $data;
    }


    /**
     * get total No of Shops
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public static function getTotalAmountOfShops()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        $data = Doctrine_Query::create()
        ->select("count(*) as amountshops")
        ->from('Shop s')
        ->where('s.deleted = 0')
        ->andWhere("s.status = '1'")
        ->fetchOne(null, Doctrine::HYDRATE_ARRAY) ;
        return $data;
    }

    /**
     * get No of Shops with atleast one code online
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public static function getTotalAmountOfShopsCodeOnline()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        $data = Doctrine_Query::create()
            ->select("count(*) as amountshops")
            ->from('Shop s')
            ->leftJoin('s.offer o')
            ->where('s.deleted = 0')
            ->andWhere("s.status = '1'")
            ->andWhere('o.enddate > "'.$date.'"')
            ->andWhere('o.startdate <= "'.$date.'"')
            ->andWhere('o.discounttype="CD"')
            ->andWhere('o.deleted = 0' )
            //->andWhere('s.created_at BETWEEN "'.$past7Days.'" AND "'.$date.'"')
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY) ;
        return $data;
    }
    /**
     * get No of Shops with atleast one code online this week
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public static function getTotalAmountOfShopsCodeOnlineThisWeek()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        // - 7 days from today
        $past7Days = date($format, strtotime('-7 day' . $date));

        $data = Doctrine_Query::create()
            ->select("count(*) as amountshops")
            ->from('Shop s')
            ->leftJoin('s.offer o')
            ->where('s.deleted=0')
            ->andWhere('o.enddate > "'.$past7Days.'"')
            ->andWhere('o.startdate <= "'.$past7Days.'"')
            ->andWhere('o.discounttype="CD"')
            ->andWhere('o.deleted = 0' )
            //->andWhere('s.updated_at BETWEEN "'.$past7Days.'" AND "'.$date.'"')
            ->andWhere('s.status = 1 OR (s.offlineSicne > "'.$past7Days.'" && s.offlineSicne < "'.$date.'")')
            ->fetchOne(null, Doctrine::HYDRATE_ARRAY) ;
        return $data;
    }

    /**
     * get No of Shops with atleast one code online of Last week
     * @author Raman
     * @return integer
     * @version 1.0
     */

    public static function getTotalAmountOfShopsCodeOnlineLastWeek()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        // - 7 days from today
        $past7Days = date($format, strtotime('-7 day' . $date));
        // - 14 days from today
        $past14Days = date($format, strtotime('-14 day' . $date));

        $data = Doctrine_Query::create()
        ->select("count(*) as amountshops")
        ->from('Shop s')
        ->leftJoin('s.offer o')
        ->where('s.deleted=0')
        ->andWhere('o.enddate > "'.$past14Days.'"')
        ->andWhere('o.startdate <= "'.$past14Days.'"')
        ->andWhere('o.discounttype="CD"')
        ->andWhere('o.deleted = 0' )
        //->andWhere('s.updated_at BETWEEN "'.$past14Days.'" AND "'.$past7Days.'"')
        ->andWhere('s.status = 1 OR (s.offlineSicne > "'.$past14Days.'" && s.offlineSicne < "'.$past7Days.'")')
        ->fetchOne(null, Doctrine::HYDRATE_ARRAY) ;
        return $data;
    }
    /**
     * get number of days since the store has no more coupons online.
     * @author Raman
     * @return integer
     * @version 1.0
     */
    public static function getDaysSinceShopWithoutOnlneOffers($shopId)
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);

        // find whether this shop has any code
        $anyOffer = Doctrine_Query::create()
        ->select("s.id, o.id")
        ->from('Shop s')
        ->leftJoin('s.offer o')
        ->where('s.id = '.$shopId)
        ->andWhere('o.discounttype="CD"')
        ->fetchArray();

        $Days = 0;
        $noOfDays = $Days;
        if(empty($anyOffer)){

            $noOfDays = $Days;
        } else {

            // find whether this shop has any code online
            $isOnline = Doctrine_Query::create()
                ->select("s.id, o.id")
                ->from('Shop s')
                ->leftJoin('s.offer o')
                ->where('s.id = '.$shopId)
                ->andWhere('o.enddate >= "'.$date.'"')
                ->andWhere('o.startdate <= "'.$date.'"')
                ->andWhere('o.discounttype="CD"')
                ->andWhere('o.deleted = 0' )
                ->fetchArray();

            //since how many days the shop has no code online
            if(empty($isOnline)){
                    $data = Doctrine_Query::create()
                    ->select("(DATEDIFF('".$date."', o.enddate)) as diffdays")
                    ->from('Shop s')
                    ->leftJoin('s.offer o')
                    ->where('s.id = '.$shopId)
                    ->andWhere('o.enddate < "'.$date.'"')
                    ->andWhere('o.discounttype="CD"')
                    ->andWhere('o.deleted = 0' )
                    ->orderBy('o.enddate DESC')
                    ->limit(1)
                    ->fetchArray() ;

                if(!empty($data)){
                    $noOfDays = $data[0]['diffdays'];
                }
            } else {
                $noOfDays = 0;
            }

        }

        return $noOfDays;
    }
    /**
     * get number of times the store has been added to favourite.
     * @author Raman
     * @return integer
     * @version 1.0
     */
    public static function getFavouriteCountOfShop($shopId)
    {
        $data = Doctrine_Query::create()
                ->select("count(*)")
                ->from('FavoriteShop s')
                ->where('s.shopId='.$shopId)
                ->fetchOne(null, Doctrine::HYDRATE_ARRAY) ;
        return $data['count'];
    }

    /*
     * Author Raman
     * find whether a shop is online or not
    * */
    public static function getshopStatus($shopId)
    {

        $Q = Doctrine_Query::create()->select('s.id')
        ->from('Shop s')
        ->where('s.id='.$shopId)
        ->andWhere('s.status = 1')
        ->fetchArray();

        if(empty($Q)){

            $online = true;
            return $online;

        } else {

            $online = false;
            return $online;

        }
    }

    /**
    * @author Daniel
    * @return getShopBranding
    */
    public static function getShopBranding($shopID)
    {
        $brandingCss = Doctrine_Query::create()
        ->select('s.brandingcss')
        ->from("Shop s")
        ->where('s.id='.$shopID)
        ->fetchArray(Doctrine::HYDRATE_SINGLE_SCALAR);

        return (!empty($brandingCss[0]['brandingcss'])) ? unserialize($brandingCss[0]['brandingcss']) : null;
    }

}

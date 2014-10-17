<?php
namespace KC\Repository;
class Offer Extends \KC\Entity\Offer
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
    
    public static function getExpiredOffers($type, $limit, $shopId = 0)
    {
        $expiredDate = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'o.id, o.title, o.Visability, o.couponCode, o.refOfferUrl, o.endDate,
            o.extendedOffer, o.extendedUrl, s.id as shopId, s.affliateProgram'
        )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->setParameter(1, 0)
        ->where('o.deleted = ?1')
        ->setParameter(2, 0)
        ->andWhere('o.userGenerated = ?2')
        ->andWhere('o.endDate <='."'".$expiredDate."'")
        ->setParameter(4, 'CD')
        ->andWhere('o.discountType = ?4')
        ->setParameter(5, 0)
        ->andWhere('s.deleted = ?5')
        ->setParameter(6, 1)
        ->andWhere('s.status = ?6')
        ->orderBy('o.id', 'DESC');
        if ($shopId != '') {
            $query = $query->setParameter(7, $shopId);
            $query = $query->andWhere('s.id = ?7');
        }
        $query = $query->setMaxResults($limit);
        $expiredOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $expiredOffers;
    }
    public static function similarStoresAndSimilarCategoriesOffers($type, $limit, $shopId = 0)
    {
        $date = date("Y-m-d H:i");
        $similarShopsOffers = self::getOffersBySimilarShops($date, $limit, $shopId);
        print_r($similarShopsOffers);die;
        $similarCategoriesOffers = self::getOffersBySimilarCategories($date, $limit, $shopId);
        $similarShopsAndSimilarCategoriesOffers = self::mergeSimilarShopsOffersAndSimilarCategoriesOffers(
            $similarShopsOffers,
            $similarCategoriesOffers,
            $limit
        );
        return $similarShopsAndSimilarCategoriesOffers;
    }
    public static function getOffersBySimilarShops($date, $limit, $shopId)
    {
        $similarShopsIds = self::getSimilarShopsIds($shopId);
        if (count($similarShopsIds) > 0) {
            $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerUser
                ->select(
                    's.id as shopId,s.permaLink as permalink,s.name,s.deepLink,s.usergenratedcontent,s.deepLinkStatus,
                    o.refURL, o.refOfferUrl, s.refUrl,s.actualUrl,terms.content,o.id,o.title, o.Visability,
                    o.discountType, o.couponCode, o.startDate as startdate, o.endDate as enddate, o.exclusiveCode,
                    o.editorPicks,o.extendedOffer as extendedoffer,o.extendedUrl,o.discount, o.authorId, o.authorName,
                    o.userGenerated,o.couponCodeType, o.approved,o.discountvalueType,img.id, img.path,
                    img.name,fv.shopId,fv.visitorId,fv.id,vot.id,vot.vote'
                )
                ->from('KC\Entity\Offer', 'o')
                ->addSelect(
                    "(SELECT count(id) FROM KC\Entity\CouponCode WHERE offer = o.id and status=1) as totalAvailableCodes"
                )
                ->leftJoin('o.shopOffers', 's')
                ->leftJoin('s.visitors', 'fv')
                ->leftJoin('o.offertermandcondition', 'terms')
                ->leftJoin('o.votes', 'vot')
                ->leftJoin('s.categoryshops', 'c')
                ->leftJoin('s.logo', 'img')
                ->setParameter(1, 0)
                ->where('o.deleted = ?1')
                ->setParameter(2, 'MEM')
                ->andWhere('o.Visability != ?2')
                ->andWhere('o.endDate >'."'".$date."'")
                ->andWhere('o.startDate <='."'".$date."'")
                ->setParameter(4, 'CD')
                ->andWhere('o.discountType = ?4')
                ->setParameter(5, 0)
                ->andWhere('s.deleted = ?5')
                ->setParameter(6, 1)
                ->andWhere('s.status = ?6')
                ->setParameter(8, 0)
                ->andWhere('o.userGenerated = ?8')
                ->setParameter(9, $shopId)
                ->andWhere('o.shopOffers != ?9')
                ->setParameter(10, 1)
                ->andWhere('s.affliateProgram = ?10')
                ->setParameter(11, $similarShopsIds)
                ->andWhere($entityManagerUser->expr()->in('o.shopOffers', '?11'))
                ->orderBy('o.startDate', 'DESC')
                ->setMaxResults($limit);
                //print_r($query->getQuery()->getDql());die;
                $similarShopsOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } else {
            $similarShopsOffers = array();
        }
        return $similarShopsOffers;
    }
    public static function getSimilarShopsIds($shopId)
    {
        $similarShopsIds = array();
        $similarShops=self::getRelatedShops($shopId);
        if (count($similarShops)>0) {
            $countOfSimilarShops = count($similarShops);
            for ($i=0; $i<$countOfSimilarShops; $i++) {
                $similarShopsIds[$i] = $similarShops[$i]['relatedshopId'];
            }
        }
        return $similarShopsIds;
    }
    public static function getOffersBySimilarCategories($date, $limit, $shopId)
    {
        $similarCategoriesIds = self::getSimilarCategoriesIds($shopId);
        $similarCategoriesOffer = array();
        if (!empty($similarCategoriesIds)) {
            $commaSepratedCategroyIdValues = implode(', ', $similarCategoriesIds);
            $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerUser
            ->select(
                's.id as shopId,s.permaLink as permalink,s.name,s.deepLink,s.usergenratedcontent,s.deepLinkStatus,
                o.refURL,
                o.refOfferUrl, s.refUrl,s.actualUrl,terms.content,o.id,o.title, o.Visability, o.discountType,
                o.couponCodeType, o.couponCode, o.refOfferUrl as refofferurl, o.startDate as startdate,
                o.endDate as enddate, o.exclusiveCode, o.editorPicks,
                o.extendedOffer as extendedoffer,o.extendedUrl,o.discount, o.authorId, o.authorName,
                o.userGenerated, o.approved,o.discountvalueType,img.id, img.path, img.name,fv.shopId,fv.visitorId,
                fv.id,vot.id,vot.vote'
            )
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.visitors', 'fv')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->leftJoin('o.votes', 'vot')
            ->leftJoin('s.categoryshops', 'c')
            ->leftJoin('s.logo', 'img')
            ->setParameter(1, 0)
            ->where('o.deleted = ?1')
            ->setParameter(5, 0)
            ->andWhere('s.deleted = ?5')
            ->setParameter(6, 1)
            ->andWhere('s.status = ?6')
            ->setParameter(10, 1)
            ->andWhere('s.affliateProgram = ?10')
            ->setParameter(4, 'CD')
            ->andWhere('o.discountType = ?4')
            ->setParameter(2, 'MEM')
            ->andWhere('o.Visability != ?2')
            ->andWhere('o.endDate >'."'".$date."'")
            ->andWhere('o.startDate <='."'".$date."'")
            ->setParameter(8, 0)
            ->andWhere('o.userGenerated = ?8')
            ->setParameter(11, $commaSepratedCategroyIdValues)
            ->andWhere($entityManagerUser->expr()->in('c.categoryId', '?11'))
            ->setParameter(9, $shopId)
            ->andWhere('o.shopOffers != ?9')
            ->orderBy('o.startDate', 'DESC')
            ->setMaxResults($limit);
            $similarCategoriesOffer = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $similarCategoriesOffer;
    }

    public static function getSimilarCategoriesIds($shopId)
    {
        $similarCategoriesIds = array();
        $shopsCategories = self::getShopCategories($shopId);
        if (count($shopsCategories)>0) {
            $countOfShopCategories = count($shopsCategories);
            for ($i=0; $i<$countOfShopCategories; $i++) {
                $similarCategoriesIds[$i]=$shopsCategories[$i]['categoryId'];
            }
        }
        return $similarCategoriesIds;
    }

    public static function getShopCategories($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('r')
            ->from('KC\Entity\RefShopCategory', 'r')
            ->setParameter(1, $shopId)
            ->where('r.category = ?1');
        $shopCategories = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopCategories;
    }

    public static function getRelatedShops($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('r')
            ->from('KC\Entity\RefShopRelatedshop', 'r')
            ->setParameter(1, $shopId)
            ->where('r.shop = ?1');
        $relatedShops = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $relatedShops;
    }

    public static function mergeSimilarShopsOffersAndSimilarCategoriesOffers(
        $similarShopsOffers,
        $similarCategoriesOffers,
        $limit
    ) {
        $shopsOffers = self::createShopsArrayAccordingToOfferHtml($similarShopsOffers);
        $categoriesOffers = self::createCategoriesArrayAccordingToOfferHtml($similarCategoriesOffers);
        $mergedOffers = array_merge($shopsOffers, $categoriesOffers);
        return self::sliceOffersByLimit($mergedOffers, $limit);
    }

    public static function createShopsArrayAccordingToOfferHtml($similarShopsOffers)
    {
        $NewOffersOfRelatedShops = array();
        foreach ($similarShopsOffers as $shopOffer):
            $NewOfferOfRelatedShops["'".$shopOffer['id']."'"] = $shopOffer;
        endforeach;
        return $NewOffersOfRelatedShops;
    }

    public static function createCategoriesArrayAccordingToOfferHtml($similarCategoriesOffers)
    {
        $NewOfferOfRelatedCategories = array();
        foreach ($similarCategoriesOffers as $categoryOffer):
            $NewOfferOfRelatedCategories["'".$categoryOffer['id']."'"] = $categoryOffer;
        endforeach;
        return $NewOfferOfRelatedCategories;
    }

    public static function sliceOffersByLimit($mergedOffers, $limit)
    {
        $offers = array();
        if (!empty($mergedOffers)) {
            foreach($mergedOffers as $newOfShop):
                $offers[] = $newOfShop;
            endforeach;
        }
        $slicedOffers = array_slice($offers, 0, $limit);
        return $slicedOffers;
    }

    public static function getTopOffers($limit)
    {
        $topCouponCodes = self::getTopCouponCodes(array(), $limit);
        if (count($topCouponCodes) < $limit) {
            $newestCodesLimit = $limit - count($topCouponCodes);
            $newestTopVouchercodes = self::getNewestOffers('newest', $newestCodesLimit);
            foreach ($newestTopVouchercodes as $value) {
                $topCouponCodes[] = array(
                    'id'=> $value['shop']['id'],
                    'permalink' => $value['shop']['permalink'],
                    'offer' => $value
                 );
            }
        }
        $topOffers = array();
        foreach ($topCouponCodes as $value) {
            $topOffers[] = $value['offer'];
        }
        return $topOffers;
    }

    public static function getTopCouponCodes($shopCategories, $limit = 5)
    {
        $currentDate = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'p.id as PopularCodeId,o.id as offerId,o.couponCodeType,o.refURL,
            o.discountType,o.title,o.discountvalueType,o.Visability as visability,o.exclusiveCode,
            o.editorPicks,o.userGenerated,o.couponCode,o.extendedOffer,o.totalViewcount,
            o.startDate as startdate,o.endDate as enddate,o.refOfferUrl,
            o.extendedUrl,s.id as shopId,s.name,s.permaLink as permalink,s.usergenratedcontent,s.deepLink,
            s.deepLinkStatus, s.refUrl,s.actualUrl,terms.content,img.id, img.path, img.name'
        )
        ->from('KC\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'img')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->setParameter(1, 0)
        ->where('o.deleted = ?1')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM KC\Entity\CouponCode cc WHERE
            cc.offer = o.id and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
        )
        ->setParameter(3, 0)
        ->andWhere('s.deleted = ?3')
        ->setParameter(4, 0)
        ->andWhere('o.offline = ?4');

        if (!empty($shopCategories)) {
            $query = $query->leftJoin('s.categoryshops', 'sc')
            ->setParameter(5, $shopCategories)
            ->andWhere($entityManagerUser->expr()->in('sc.shop', '?5'));
        }
        $query->setParameter(5, 1)
            ->andWhere('s.status = ?5')
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'")
            ->setParameter(7, 'CD')
            ->andWhere('o.discountType = ?7')
            ->setParameter(8, 0)
            ->andWhere('o.userGenerated = ?8')
            ->setParameter(9, 'MEM')
            ->andWhere('o.Visability != ?9')
            ->orderBy('p.position', 'ASC')
            ->setMaxResults($limit);
        $topCouponCodes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $topCouponCodes;
    }

    public static function getNewestOffers($type, $limit, $shopId = 0, $userId = "", $homeSection = '')
    {
        $currentDate = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            's.id as shopId,s.name,
            s.permaLink as permalink,s.permaLink,s.deepLink,s.deepLinkStatus,s.usergenratedcontent,s.refUrl,
            s.actualUrl,terms.content,
            o.id,o.Visability as visability,o.userGenerated,o.title,o.authorId,
            o.discountvalueType,o.exclusiveCode,o.extendedOffer,o.editorPicks,
            o.discount,o.couponCode,o.couponCodeType,o.refOfferUrl,o.refURL as refUrl,o.extendedUrl,
            o.discountType,o.startDate as startdate,o.endDate as enddate,
            img.id as imageId, img.path, img.name,fv.favoriteshops, logo.id, logo.name, logo.path,
            vot.id as voteId,vot.vote'
        )
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.logo', 'logo')
            ->leftJoin('o.votes', 'vot')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('s.visitors', 'fv')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->setParameter(10, 0)
            ->where('o.deleted = ?10')
            ->andWhere(
                "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM KC\Entity\CouponCode cc WHERE
                cc.offer = o.id and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
            )
            ->setParameter(8, 0)
            ->andWhere('s.deleted = ?8')
            ->setParameter(9, 1)
            ->andWhere('s.status = ?9')
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'")
            ->setParameter(2, 'CD')
            ->andWhere('o.discountType = ?2')
            ->setParameter(3, 'NW')
            ->andWhere('o.discountType != ?3')
            ->setParameter(4, 0)
            ->andWhere('o.userGenerated = ?4')
            ->setParameter(5, 'MEM')
            ->andWhere('o.Visability != ?5')
            ->orderBy('o.startDate', 'DESC');
        if ($shopId!='') {
            $query->setParameter(6, $shopId);
            $query->andWhere('s.id = ?6');
        }
        if ($userId!="") {
            $query->setParameter(7, $userId);
            $query->andWhere('o.authorId = ?7');
        }
        if ($homeSection!='') {
            $query->groupBy('s.id');
        }
        $query = $query->setMaxResults($limit);
        $newestCouponCodes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $newestCouponCodes;
    }

    public static function updateCache($offerId)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('s.id')
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->setParameter(1, $offerId)
            ->where('o.id = ?1');
        $offerDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $shopId = $offerDetails[0]['id'];
        $key = 'shopDetails_'  . $shopId . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'offerDetails_'  . $shopId . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = '6_topOffers'  . $shopId . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shop_latestUpdates'  . $shopId . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shop_expiredOffers'  . $shopId . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allMoneySavingGuideLists');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allOfferList');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allNewOfferList');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allNewPopularCodeList');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allHomeNewOfferList');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('extended_coupon_details');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage_offers');
    }

    public static function getSpecialPageOffers($specialPage)
    {
        $currentDate = date("Y-m-d H:i");
        $pageRelatedOffers = self::getSpecialOffersByPage($specialPage['id'], $currentDate);
        $constraintsRelatedOffers = self::getOffersByPageConstraints($specialPage, $currentDate);
        $pageRelatedOffersAndPageConstraintsOffers = array_merge($pageRelatedOffers, $constraintsRelatedOffers);
        $specialOffers = self::getDataForOfferPhtml($pageRelatedOffersAndPageConstraintsOffers, $specialPage);
        return $specialOffers;
    }

    public static function getSpecialOffersByPage($pageId, $currentDate)
    {
        $specialPageOffers = self::getOffersByPageId($pageId, $currentDate);
        return self::removeDuplicateOffers($specialPageOffers);
    }

    public static function getOffersByPageId($pageId, $currentDate)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'op.offers,op.refoffers,o.couponCodeType,o.totalViewcount as clicks,o.title,o.refURL as refUrl,o.refOfferUrl,
            o.discountType,o.startDate as startdate,o.endDate as enddate,o.authorId,o.authorName,
            o.Visability as visability,o.couponCode,o.exclusiveCode,
            o.editorPicks,o.discount,o.discountvalueType,o.extendedOffer,o.extendedUrl,s.name,s.refUrl,
            s.actualUrl,s.permaLink as permalink,s.views,l.name,l.path,fv.id,fv.visitorId,fv.shopId,vot.id,vot.vote,
            ologo.path,ologo.name'
        )
        ->from('KC\Entity\RefOfferPage', 'op')
        ->leftJoin('op.refoffers', 'o')
        ->leftJoin('o.logo', 'ologo')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id) FROM KC\Entity\CouponCode cc WHERE cc.offer = o.id and cc.status=1)  > 0)
            or o.couponCodeType = 'GN'"
        )
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.votes', 'vot')
        ->leftJoin('s.logo', 'l')
        ->leftJoin('s.visitors', 'fv')
        ->setParameter(7, $pageId)
        ->where('op.pageId = ?7')
        ->andWhere('o.endDate >'."'".$currentDate."'")
        ->andWhere('o.startDate <='."'".$currentDate."'")
        ->setParameter(2, 0)
        ->andWhere('o.deleted = ?2')
        ->setParameter(3, 0)
        ->andWhere('s.deleted = ?3')
        ->setParameter(4, 1)
        ->andWhere('s.status = ?4')
        ->setParameter(5, 'CD')
        ->andWhere('o.discountType = ?5')
        ->setParameter(6, 'MEM')
        ->andWhere('o.Visability != ?6')
        ->orderBy('o.exclusiveCode', 'DESC')
        ->addOrderBy('o.startDate', 'DESC');
        $specialPageOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialPageOffers;
    }

    public static function removeDuplicateOffers($specialPageOffers)
    {
        $specialOffersWithoutDuplication = array();
        if (count($specialPageOffers) > 0) {
            $countOfSpecialPageOffers = count($specialPageOffers);
            for ($offerIndex = 0; $offerIndex < $countOfSpecialPageOffers; $offerIndex++) {
                $specialOffersWithoutDuplication[$offerIndex] = $specialPageOffers[$offerIndex]['Offer'];
            }
        }
        return $specialOffersWithoutDuplication;
    }

    public static function getOffersByPageConstraints($specialPage, $currentDate)
    {
        $specialOffersByPageConstraints = self::getSpecialOffersByPageConstraints($specialPage, $currentDate);
        return self::getFilteredOffersByConstraints($specialPage, $specialOffersByPageConstraints);
    }

    public static function getSpecialOffersByPageConstraints($specialPage, $currentDate)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $offersConstraintsQuery = $entityManagerUser->select(
            'o.title,o.couponCodeType,o.discountType,o.totalViewcount as clicks,o.startdate,o.enddate,o.refUrl,
            o.refOfferUrl,o.authorId,o.authorName,o.visability,o.couponCode,o.exclusiveCode,o.editorPicks,o.discount,
            o.discountvalueType,s.name,s.refUrl, s.actualUrl,s.permaLink as permalink,s.views,l.name,l.path,fv.id,
            fv.visitorId,fv.shopId,vot.id,vot.vote, ologo.path, ologo.name'
        )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.logo', 'ologo')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id) FROM KC\Entity\CouponCode cc WHERE cc.offer = o.id 
                and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
        )
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.votes', 'vot')
        ->leftJoin('s.logo', 'l')
        ->leftJoin('s.visitors', 'fv')
        ->setParameter(1, $entityManagerUser->expr()->literal($currentDate))
        ->where($entityManagerUser->expr()->gt('o.endDate', '?1'))
        ->andWhere($entityManagerUser->expr()->lte('o.startDate', '?1'))
        ->setParameter(2, 0)
        ->andWhere('o.deleted = ?2')
        ->setParameter(3, 0)
        ->andWhere('s.deleted = ?3')
        ->setParameter(4, 0)
        ->andWhere('o.userGenerated = ?4')
        ->setParameter(5, 'MEM')
        ->andWhere('o.Visability != ?5')
        ->setParameter(6, 'SL')
        ->andWhere('o.discountType != ?6')
        ->setParameter(7, 'PA')
        ->andWhere('o.discountType != ?7')
        ->orderBy('o.exclusiveCode', 'DESC')
        ->addOrderBy('o.startDate', 'DESC');
        $offersConstraintsQuery = self::implementOffersConstraints($offersConstraintsQuery, $specialPage);
        $specialOffersByConstraints = $offersConstraintsQuery->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialOffersByConstraints;
    }

    public static function implementOffersConstraints($offersConstraintsQuery, $specialPage)
    {
        if (isset($specialPage['oderOffers']) && $specialPage['oderOffers'] == 1) {
            $offersConstraintsQuery->orderBy('o.title', 'ASC');
        } else if (isset($specialPage['oderOffers']) && $specialPage['oderOffers'] == 0) {
            $offersConstraintsQuery->orderBy('o.title', 'DESC');
        } else {
            $offersConstraintsQuery->orderBy('o.id', 'DESC');
        }
        if (isset($specialPage['enableWordConstraint']) && $specialPage['enableWordConstraint'] > 0
                && $specialPage['enableWordConstraint'] != null) {
            $wordTitle = $specialPage['wordTitle'];
            $offersConstraintsQuery->setParameter(1, $wordTitle.'%');
            $offersConstraintsQuery->andWhere($entityManagerUser->expr()->like('o.title, ?1'));
        }

        $offerEditorpick = $specialPage['couponEditorPick']==1 ? $specialPage['couponEditorPick'] : 0;
        $offerExclusive = $specialPage['couponExclusive'] == 1 ? $specialPage['couponExclusive'] : 0;
        $offerRegular = $specialPage['couponRegular'] == 1 ? $specialPage['couponRegular'] : 0;
        if ($offerRegular == 0) {
            $offersConstraintsQuery =
                self::offersWithNoRegular($offersConstraintsQuery, $offerEditorpick, $offerExclusive);
        } else {
            $offersConstraintsQuery =
                self::offersWithYesRegular($offersConstraintsQuery, $offerEditorpick, $offerExclusive);
        }
        return $offersConstraintsQuery;
    }

    public static function offersWithNoRegular($offersConstraintsQuery, $offerEditorpick, $offerExclusive)
    {
        if ($offerEditorpick == 0) {
            if ($offerExclusive == 0) {
                $offersConstraintsQuery = self::noCouponNoExclusiveNoEditorPickConstraints($offersConstraintsQuery);
            } else {
                $offersConstraintsQuery = self::yesCouponAndYesExclusiveConstraints($offersConstraintsQuery);
            }
        } else {
            if ($offerExclusive == 0) {
                $offersConstraintsQuery = self::yesCouponAndYesEditorPicksConstraints($offersConstraintsQuery);
            } else {
                $offersConstraintsQuery = self::
                yesCouponAndYesEditorPicksOrYesExclusiveConstraints($offersConstraintsQuery);
            }
        }
        return $offersConstraintsQuery;
    }

    public static function offersWithYesRegular($offersConstraintsQuery, $offerEditorpick, $offerExclusive)
    {
        if ($offerEditorpick == 0) {
            if ($offerExclusive == 0) {
                $offersConstraintsQuery = self::
                yesCouponAndNoEditorPicksAndNoExclusiveConstraints($offersConstraintsQuery);
            } else {
                $offersConstraintsQuery = self::
                yesCouponAndYesExclusiveAndNoEditorPicksConstraints($offersConstraintsQuery);
            }
        } else {
            if ($offerExclusive == 0) {
                $offersConstraintsQuery = self::
                yesCouponAndYesEditorPicksAndNoExclusiveConstraints($offersConstraintsQuery);
            } else {
                $offersConstraintsQuery = self::yesCouponCodeConstraint($offersConstraintsQuery);
            }
        }
        return $offersConstraintsQuery;
    }

    public static function noCouponNoExclusiveNoEditorPickConstraints($offersConstraintsQuery)
    {
        $offersConstraintsQuery = self::noCouponCodeConstraint($offersConstraintsQuery);
        $offersConstraintsQuery = self::noExclusiveCodeAndNoEditorPickCodeConstraints($offersConstraintsQuery);
        return $offersConstraintsQuery;
    }

    public static function yesCouponAndYesExclusiveConstraints($offersConstraintsQuery)
    {
        $offersConstraintsQuery = self::yesCouponCodeConstraint($offersConstraintsQuery);
        $offersConstraintsQuery = self::yesExclusiveCodeConstraint($offersConstraintsQuery);
        return $offersConstraintsQuery;
    }

    public static function yesCouponAndYesEditorPicksConstraints($offersConstraintsQuery)
    {
        $offersConstraintsQuery = self::yesCouponCodeConstraint($offersConstraintsQuery);
        $offersConstraintsQuery = self::yesEditorPicksCodeConstraint($offersConstraintsQuery);
        return $offersConstraintsQuery;
    }

    public static function yesCouponAndYesEditorPicksOrYesExclusiveConstraints($offersConstraintsQuery)
    {
        $offersConstraintsQuery = self::yesCouponCodeConstraint($offersConstraintsQuery);
        $offersConstraintsQuery = self::yesExclusiveOrYesEditorPickConstraints($offersConstraintsQuery);
        return $offersConstraintsQuery;
    }

    public static function yesCouponAndNoEditorPicksAndNoExclusiveConstraints($offersConstraintsQuery)
    {
        $offersConstraintsQuery = self::yesCouponCodeConstraint($offersConstraintsQuery);
        $offersConstraintsQuery = self::noExclusiveCodeAndNoEditorPickCodeConstraints($offersConstraintsQuery);
        return $offersConstraintsQuery;
    }

    public static function yesCouponAndYesExclusiveAndNoEditorPicksConstraints($offersConstraintsQuery)
    {
        $offersConstraintsQuery = self::yesCouponAndYesExclusiveConstraints($offersConstraintsQuery);
        $offersConstraintsQuery = self::noEditorPicksCodeConstraint($offersConstraintsQuery);
        return $offersConstraintsQuery;
    }

    public static function yesCouponAndYesEditorPicksAndNoExclusiveConstraints($offersConstraintsQuery)
    {
        $offersConstraintsQuery = self::yesCouponAndYesEditorPicksConstraints($offersConstraintsQuery);
        $offersConstraintsQuery = self::notExclusiveCodeConstraint($offersConstraintsQuery);
        return $offersConstraintsQuery;
    }

    public static function noExclusiveCodeAndNoEditorPickCodeConstraints($offersConstraintsQuery)
    {
        $offersConstraintsQuery = self::noEditorPicksCodeConstraint($offersConstraintsQuery);
        $offersConstraintsQuery = self::notExclusiveCodeConstraint($offersConstraintsQuery);
        return $offersConstraintsQuery;
    }

    public static function noCouponCodeConstraint($offersConstraintsQuery)
    {
        $offersConstraintsQuery->setParameter(1, 'CD');
        $offersConstraintsQuery = $offersConstraintsQuery->andWhere('o.discountType != ?1');
        return $offersConstraintsQuery ;
    }

    public static function yesExclusiveOrYesEditorPickConstraints($offersConstraintsQuery)
    {
        return  $offersConstraintsQuery->andWhere(
            $offersConstraintsQuery->expr()->orX('o.exclusiveCode = 1', 'o.editroPicks = 1')
        );
    }

    public static function noEditorPicksCodeConstraint($offersConstraintsQuery)
    {
        return $offersConstraintsQuery->andWhere(
            $offersConstraintsQuery->expr()->orX('o.editorPicks = 0', 'o.editorPicks is NULL')
        );
    }

    public static function notExclusiveCodeConstraint($offersConstraintsQuery)
    {
        return $offersConstraintsQuery->andWhere(
            $offersConstraintsQuery->expr()->orX('o.exclusiveCode = 0', 'o.exclusiveCode is NULL')
        );
    }

    public static function yesExclusiveCodeConstraint($offersConstraintsQuery)
    {
        $offersConstraintsQuery->setParameter(1, 1);
        $offersConstraintsQuery = $offersConstraintsQuery->andWhere('o.exclusiveCode = ?1');
        return $offersConstraintsQuery;
    }

    public static function yesEditorPicksCodeConstraint($offersConstraintsQuery)
    {
        $offersConstraintsQuery->setParameter(1, 1);
        $offersConstraintsQuery = $offersConstraintsQuery->andWhere('o.editorPicks = ?1');
        return $offersConstraintsQuery;
    }

    public static function yesCouponCodeConstraint($offersConstraintsQuery)
    {
        $offersConstraintsQuery->setParameter(1, 'CD');
        $offersConstraintsQuery = $offersConstraintsQuery->andWhere('o.discountType = ?1');
        return $offersConstraintsQuery;
    }

    public static function getFilteredOffersByConstraints($specialPage, $specialOffersByConstraints)
    {
        $offersAccordingToConstraints = array();
        if (count($specialOffersByConstraints) > 0) {
            $countOfSpecialOffersByConstraints = count($specialOffersByConstraints);
            for ($offerIndex = 0; $offerIndex < $countOfSpecialOffersByConstraints; $offerIndex++) {

                $offerPublishDate = $specialOffersByConstraints[$offerIndex]['startdate'];
                $offerExpiredDate = $specialOffersByConstraints[$offerIndex]['enddate'];
                $offerSubmissionDaysIncreasedBy = ' +'.$specialPage['timenumberOfDays'].' days';
                $offerSubmissionDaysDecreasedBy  = ' -'.$specialPage['timenumberOfDays'].' days';
                $increasedOfferPublishDate = date(
                    'Y-m-d',
                    strtotime($offerPublishDate .$offerSubmissionDaysIncreasedBy)
                );
                $decreasedOfferExpiredDate = date(
                    'Y-m-d',
                    strtotime($offerExpiredDate .$offerSubmissionDaysDecreasedBy)
                );
                $currentDate = strtotime(date("Y-m-d"));
                $newOfferPublishDate = strtotime($increasedOfferPublishDate);
                $newOfferExprationDate = strtotime($decreasedOfferExpiredDate);

                if (isset($specialPage['enableTimeConstraint']) && $specialPage['enableTimeConstraint'] == 1) {
                    if ($specialPage['timeType'] == 1) {
                        if ($newOfferPublishDate >= $currentDate) {
                            $offersAccordingToConstraints[$offerIndex] = $specialOffersByConstraints[$offerIndex];
                        }
                    } else if ($specialPage['timeType'] == 2) {
                        if ($newOfferExprationDate <= $currentDate) {
                            $offersAccordingToConstraints[$offerIndex] = $specialOffersByConstraints[$offerIndex];
                        }
                    }
                } else if (isset($specialPage['enableClickConstraint']) && $specialPage['enableClickConstraint'] == true
                        && $specialPage['enableClickConstraint'] == 1) {
                    if ($specialOffersByConstraints[$offerIndex]['clicks'] >= $specialPage['numberOfClicks']) {
                        $offersAccordingToConstraints[$offerIndex] = $specialOffersByConstraints[$offerIndex];
                    }
                } else {
                    $offersAccordingToConstraints[$offerIndex] = $specialOffersByConstraints[$offerIndex];
                }

            }
        }
        return $offersAccordingToConstraints;
    }

    public static function getDataForOfferPhtml($specialMargedOffers, $specialPage)
    {
        if (isset($specialPage['maxOffers']) && $specialPage['maxOffers'] > 0 && $specialPage['maxOffers'] != null) {
            $specialMargedOffers = array_slice($specialMargedOffers, 0, $specialPage['maxOffers']);
        }
        $specialOffersAfterMerging = array();
        foreach ($specialMargedOffers as $specialOffer) {
            if (!isset($specialOffersAfterMerging[$specialOffer['id']])) {
                $specialOffersAfterMerging[$specialOffer['id']] = $specialOffer;
            }
        }
        return $specialOffersAfterMerging;
    }

    public static function getActiveCoupons($keyword)
    {
        $currentDate = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            's.id as shopId,o.id, o.title, o.Visability as visability, o.couponCode as couponcode,
            o.refOfferUrl as refofferurl,o.endDate as enddate, o.extendedOffer as extendedoffer,o.extendedUrl'
        )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->setParameter(1, 0)
        ->where('o.deleted = ?1')
        ->setParameter(2, 0)
        ->andWhere('o.userGenerated = ?2')
        ->andWhere("o.title LIKE '%$keyword%'")
        ->andWhere('o.endDate >'."'".$currentDate."'")
        ->setParameter(5, 'CD')
        ->andWhere('o.discountType = ?5')
        ->setParameter(6, 0)
        ->andWhere('s.deleted = ?6')
        ->orderBy('o.id', 'DESC');
        $activeCoupons = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $activeCoupons;
    }

    public static function getSplashPagePopularCoupon($offerId)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'o.id,o.Visability,o.userGenerated,o.title,o.authorId,
            o.discountvalueType,o.exclusiveCode,o.extendedOffer,o.editorPicks,
            o.discount,o.userGenerated,o.couponCode,o.couponCodeType,o.refOfferUrl,o.refURL as refUrl,
            o.discountType,o.startDate as startdate,o.endDate, s.id as shopId'
        )
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->setParameter(1, 0)
            ->where('o.deleted = ?1')
            ->setParameter(2, 0)
            ->andWhere('o.userGenerated = ?2')
            ->setParameter(3, $offerId)
            ->andWhere('o.id = ?3')
            ->setParameter(4, 'CD')
            ->andWhere('o.discountType = ?4')
            ->orderBy('o.id', 'DESC');
            $offerDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $shopDetails = self::getShopDetailFromOffer($offerDetails[0]['shopId']);
            $logoDetails = self::getShopLogo($shopDetails[0]['logoId']);
            $splashPagePopularCoupon = array_merge($offerDetails, $shopDetails, $logoDetails);
        return $splashPagePopularCoupon;
    }

    public static function getShopDetailFromOffer($shopId)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            's.id,s.name,
            s.permaLink as permalink,s.permaLink,s.deepLink,s.deepLinkStatus,s.usergenratedcontent,s.refUrl,
            s.actualUrl, logo.id as logoId'
        )
            ->from('KC\Entity\Shop', 's')
            ->leftJoin('s.logo', 'logo')
            ->setParameter(1, $shopId)
            ->where('s.id = ?1')
            ->setParameter(2, 0)
            ->andWhere('s.deleted = ?2');
        $splashPagePopularCouponShopDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $splashPagePopularCouponShopDetails;
    }

    public static function getShopLogo($logoId)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('l.name, l.path')
            ->from('KC\Entity\Logo', 'l')
            ->setParameter(1, $logoId)
            ->where('l.id = ?1');
        $splashPagePopularCouponLogo = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $splashPagePopularCouponLogo;
    }

    public static function searchOffers($searchParameters, $shopIds, $limit)
    {
        $searchKeyword = '';
        if(isset($searchParameters['searchField'])) :
            $searchKeyword = $searchParameters['searchField'];
        endif;

        $currentDate = new \DateTime();
        $searchedOffersByIds = self::getOffersByShopIds($shopIds, $currentDate);
        $offersBySearchedKeywords = self::getOffersBySearchedKeywords($searchKeyword, $currentDate);
        $mergedOffersBySearchedKeywords = array_merge($searchedOffersByIds, $offersBySearchedKeywords);
        $searchedOffers = array_slice($mergedOffersBySearchedKeywords, 0, $limit);
        return $searchedOffers;
    }

    public static function getOffersByShopIds($shopIds, $currentDate)
    {
        $shopOffersByShopIds = array();
        if(!empty($shopIds)) :
            $shopIds = ("'" . implode("', '", $shopIds) . "'");
            $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerUser
            ->select(
                's.id as shopId,s.name,s.refUrl,s.actualUrl,s.permaLink as permalink,terms.content,o.refURL,
                o.discountType,o.id,o.title,o.extendedUrl,o.Visability as visability,
                o.discountvalueType as discountValueType,
                o.couponCode as couponcode , o.refOfferUrl as refofferurl,
                o.startDate as startdate ,o.endDate as enddate, o.exclusiveCode as exclusivecode,
                o.editorPicks as editorpicks, o.extendedOffer as extendedoffer,o.discount, o.authorId,
                o.authorName, o.userGenerated, o.approved,fv.shopId,fv.visitorId,img.id, img.path,
                img.name'
            )
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('s.visitors', 'fv')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->leftJoin('o.offerTiles', 't')
            ->setParameter(1, 0)
            ->where('o.deleted = ?1')
            ->setParameter(2, 0)
            ->andWhere('o.userGenerated = ?2')
            ->setParameter(3, 0)
            ->andWhere('o.offline = ?3')
            ->setParameter(4, 0)
            ->andWhere('s.deleted = ?4')
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'")
            ->setParameter(6, 'CD')
            ->andWhere('o.discountType = ?6')
            ->setParameter(7, 'MEM')
            ->andWhere('o.Visability != ?7')
            ->setParameter(8, $shopIds)
            ->andWhere($entityManagerUser->expr()->in('s.id', '?8'))
            ->orderBy('s.name', 'ASC');
            $shopOffersByShopIds = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        endif;
        return $shopOffersByShopIds;
    }

    public static function getOffersBySearchedKeywords($searchKeyword, $currentDate)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
            ->select(
                's.id as shopId,s.name,s.refUrl,s.actualUrl,s.permaLink as permalink,terms.content,
                o.id,o.title,o.refURL,o.discountType,o.extendedUrl,o.Visability as visability,
                o.discountvalueType as discountValueType, o.couponCode as couponcode, 
                o.refOfferUrl as refofferurl, o.startDate as startdate, o.endDate as enddate,
                o.exclusiveCode as exclusivecode, o.editorPicks as editorpicks,o.extendedOffer as extendedoffer,
                o.discount,o.authorId, o.authorName,o.userGenerated, o.approved,img.id,
                img.path,img.name,fv.shopId,fv.visitorId,t.content'
            )
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('s.visitors', 'fv')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->leftJoin('o.offerTiles', 't')
            ->setParameter(1, 0)
            ->where('o.deleted = ?1')
            ->setParameter(2, 0)
            ->andWhere('o.offline = ?2')
            ->setParameter(3, 0)
            ->andWhere('s.deleted = ?3')
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'")
            ->setParameter(5, 'CD')
            ->andWhere('o.discountType = ?5')
            ->setParameter(6, 'MEM')
            ->andWhere('o.Visability != ?6')
            ->andWhere(
                $query->expr()->orX(
                    "s.name LIKE '%$searchKeyword%'",
                    "o.title LIKE '%$searchKeyword%'"
                )
            )
            ->orderBy('s.name', 'ASC');
        $shopOffersBySearchedKeywords = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopOffersBySearchedKeywords;
    }

    public static function getOfferList($parameters)
    {
        $userRole           = \Auth_StaffAdapter::getIdentity()->roleId;
        $searchOffer        = $parameters["offerText"]!='undefined' ? $parameters["offerText"] : '';
        $searchShop         = $parameters["shopText"]!='undefined' ? $parameters["shopText"] : '';
        $searchCoupon       = $parameters["shopCoupon"]!='undefined' ? $parameters["shopCoupon"] : '';
        $searchCouponType   = $parameters["couponType"]!='undefined' ? $parameters["couponType"] : '';
        $deletedStatus      = $parameters['flag'];
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $getOffersQuery = $entityManagerUser
            ->select(
                'o.id,o.title,s.name,s.accountManagerName as acName,o.totalViewcount as clicks,
                o.discountType,o.Visability,o.extendedOffer,o.startDate,o.endDate,o.authorName,o.refURL,
                o.couponCode as couponcode'
            )
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->setParameter(1, $deletedStatus)
            ->where('o.deleted = ?1')
            ->setParameter(2, 0)
            ->andWhere('o.userGenerated = ?2');
        if ($userRole=='4') {
            $getOffersQuery->setParameter(3, 'DE');
            $getOffersQuery->andWhere('o.Visability =?3');
        }
        if ($searchOffer != '') {
            $getOffersQuery->andWhere("o.title LIKE '%$searchOffer%'");
        }
        if ($searchShop!='') {
            $getOffersQuery->andWhere("s.name LIKE '%$searchShop%'");
        }
        if ($searchCoupon!='') {
            $getOffersQuery->andWhere("o.couponCode LIKE '%$searchCoupon%'");
        }
        if ($searchCouponType!='') {
            $getOffersQuery->andWhere('o.discountType ='."'".$searchCouponType."'");
        }
        $offersList = \DataTable_Helper::generateDataTableResponse(
            $getOffersQuery,
            $parameters,
            array("__identifier" => 'o.id','o.title','s.name','o.discountType','o.refURL','o.couponCode','o.startDate',
                'o.endDate', 'clicks','authorName'),
            array(),
            array()
        );
        return $offersList;
    }
    public static function addConversion($offerId)
    {
        $clientIP = ip2long(\FrontEnd_Helper_viewHelper::getRealIpAddress());

        if (self:: getCloakLink($offerId, true)) {
            $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $entityManagerUser
                        ->select('count(c.id) as exist,c.id')
                        ->from('KC\Entity\Conversions', 'c')
                        ->setParameter(1, $offerId)
                        ->andWhere('c.offer = ?1')
                        ->setParameter(2, $clientIP)
                        ->andWhere('c.IP = ?2')
                        ->setParameter(3, 0)
                        ->andWhere('c.converted = ?3')
                        ->groupBy('c.id');
                $offerData = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (!$offerData[0]['exist']) {
                $offerCount  = new \KC\Entity\Conversions();
                $offerCount->offerId = $offerId;
                $offerCount->IP = $clientIP;
                $offerCount->utma = \Zend_Controller_Front::getInstance()->getRequest()->getCookie('__utma');
                $offerCount->utmz = \Zend_Controller_Front::getInstance()->getRequest()->getCookie('__utmz');
                $offerCount->subid = md5(time()*rand(1, 999));
                \Zend_Registry::get('emLocale')->persist($offerCount);
                \Zend_Registry::get('emLocale')->flush();
            } else {
                $query = $entityManagerUser
                            ->select('c')
                            ->from('KC\Entity\Conversions', 'c')
                            ->setParameter(1, $offerData[0]['id'])
                            ->andWhere('c.offer = ?1');
                $offerCount = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if ($offerCount) {
                    $offerCount->utma = \Zend_Controller_Front::getInstance()->getRequest()->getCookie('__utma');
                    $offerCount->utmz = \Zend_Controller_Front::getInstance()->getRequest()->getCookie('__utmz');
                    $offerCount->subid = md5(time()*rand(1, 999));
                    \Zend_Registry::get('emLocale')->persist($offerCount);
                    \Zend_Registry::get('emLocale')->flush();
                }
            }
        }
    }

    public static function getCloakLink($offerId, $checkRefUrl = false)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
            ->select(
                's.permaLink as permalink, s.deepLink, s.deepLinkStatus, s.refUrl as shoprefUrl, s.actualUrl, o.refOfferUrl,
                o.refURL as refUrl, s.id as shopId'
            )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->setParameter(1, $offerId)
        ->where('o.id = ?1');
        $shopData = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $network = Shop::getAffliateNetworkDetail($shopData['shopId']);
        if ($checkRefUrl) {
            # retur false if s shop is not associated with any network
            if (!isset($network['affname'])) {
                return false;
            }

            if ($shopData['refUrl'] != "") {
                return true ;

            } else if ($shopData['shoprefUrl'] != "") {
                return true;
            } else {
                return true;
            }
        }
        $subid = "" ;
        if (isset($network['affname'])) {
            if (!empty($network['subid'])) {
                $subid = "&". $network['subid'];
                $clientIP = \FrontEnd_Helper_viewHelper::getRealIpAddress();
                $clientProperAddress = ip2long($clientIP);
                # get click detail and replcae A2ASUBID click subid
                $conversion = \KC\Repository\Conversions::getConversionId(
                    $offerId,
                    $clientProperAddress,
                    'offer'
                );
                $subid = str_replace('A2ASUBID', $conversion['subid'], $subid);
            }
        }
        if ($shopData['refUrl'] != "") {
            $url = $shopData['refUrl'];
            $url .= $subid;

        } else if ($shopData['shoprefUrl'] != "") {

            $url = $shopData['shoprefUrl'];
            $url .=  $subid;

        } else if ($shopData['actualUrl'] != "") {
            $url = $shopData['actualUrl'];
        } else {
            $urll = $shopData['permalink'];
            $url = HTTP_PATH_LOCALE.$urll;
        }
        return $url;
    }

    public static function getOfferInfo($offerId)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
            ->select(
                'o,s.name,s.notes,s.accountManagerName,s.deepLink,s.refUrl,s.actualUrl,
                s.permaLink,a.name as affname,a.id as affiliateNetworkId,p.id as pageId,tc.content,cat.id,img.path,
                img.name'
            )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.affliatenetwork', 'a')
        ->leftJoin('o.offers', 'p')
        ->leftJoin('o.offertermandcondition', 'tc')
        ->leftJoin('o.categoryoffres', 'cat')
        ->leftJoin('s.logo', 'img')
        ->setParameter(1, $offerId)
        ->where('o.id = ?1');
        $OfferDetails = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $OfferDetails;
    }

    public static function getAllOfferOnShop($id, $limit = null, $getExclusiveOnly = false, $includingOffline = false)
    {
        $nowDate = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
            ->select(
                'o.id,o.authorId,o.refURL,o.discountType,o.title,o.discountvalueType,o.Visability,o.exclusiveCode,
                o.editorPicks,o.userGenerated,o.couponCode,o.extendedOffer,o.totalViewcount,o.startDate,
                o.endDate,o.refOfferUrl,o.couponCodeType, o.extendedUrl,l.name,l.path,t.path,t.name,
                s.id as shopId,s.name,s.permaLink as permalink,
                s.usergenratedcontent,s.deepLink,s.deepLinkStatus,s.refUrl,s.actualUrl,terms.content,img.id, img.path,
                img.name,vot.id,vot.vote'
            )
        ->from('KC\Entity\Offer', 'o')
        ->addSelect(
            "(SELECT count(c.id)  FROM KC\Entity\CouponCode c WHERE c.offer = o.id and c.status=1) as totalAvailableCodes"
        )
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.logo', 'l')
        ->leftJoin('s.logo', 'img')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->leftJoin('o.votes', 'vot')
        ->leftJoin('o.offerTiles', 't')
        ->setParameter(1, 0)
        ->where('o.deleted = ?1');

        if (!$includingOffline) {
            $query = $query
                ->andWhere('o.offline = 0')
                ->andWhere('o.endDate >='."'".$nowDate."'")
                ->andWhere('o.startDate <='."'".$nowDate."'");
        }
            
        $query = $query->andWhere(
            '(o.userGenerated = 0 and o.approved = 0) or (o.userGenerated = 1 and o.approved = 1)
            '
        )
            ->setParameter(5, $id)
            ->andWhere('s.id = ?5')
            ->setParameter(6, 0)
            ->andWhere('s.deleted = ?6')
            ->setParameter(7, 'NW')
            ->andWhere('o.discountType != ?7')
            ->setParameter(8, 'MEM')
            ->andWhere('o.Visability != ?8')
            ->orderBy('o.editorPicks', 'DESC')
            ->addOrderBy('o.exclusiveCode', 'DESC')
            ->addOrderBy('o.discountType', 'ASC')
            ->addOrderBy('o.startDate', 'DESC')
            ->addOrderBy('o.popularityCount', 'DESC')
            ->addOrderBy('o.title', 'ASC');

        if ($getExclusiveOnly) {
            $query = $query->andWhere('o.exclusiveCode = 1');
        }

        if ($limit) {
            $query = $query->setMaxResults($limit);
        }

        $offers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offers;
    }

    public static function getCommonNewestOffers($type, $limit, $shopId = 0, $userId = "")
    {
        $currentDateTime = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
                ->select(
                    's.id as shopId,s.name, s.permaLink as permalink,s.deepLink,s.deepLinkStatus,
                    s.usergenratedcontent,s.refUrl,s.actualUrl,terms.content,o.id,
                    o.extendedUrl as extendedurl,o.extendedOffer as extendedoffer,
                    o.editorPicks as editorpicks,o.Visability,o.userGenerated,o.title,o.authorId,o.discountvalueType,
                    o.exclusiveCode,
                    o.discount,o.userGenerated,o.couponCode,o.couponCodeType,o.refOfferUrl,o.refURL as refUrl,
                    o.discountType,
                    o.startDate,o.endDate,img.id as imageId, img.path, img.name,fv.id, fv.favoriteshops,ologo.id,
                    vot.id,vot.vote'
                )
                ->from('KC\Entity\Offer', 'o')
                ->leftJoin('o.shopOffers', 's')
                ->leftJoin('o.logo', 'ologo')
                ->leftJoin('o.votes', 'vot')
                ->leftJoin('s.logo', 'img')
                ->leftJoin('s.visitors', 'fv')
                ->leftJoin('o.offertermandcondition', 'terms')
                ->where('o.deleted = 0')
                ->andWhere(
                    "(o.couponCodeType = 'UN' AND (
                        SELECT count(c.id)  FROM KC\Entity\CouponCode c WHERE c.offer = o.id and c.status=1)  > 0
                    ) or o.couponCodeType = 'GN'"
                )
                ->andWhere('s.deleted = 0')
                ->andWhere('s.status = 1')
                ->andWhere('o.endDate >'."'".$currentDateTime."'")
                ->andWhere('o.startDate <='."'".$currentDateTime."'")
                ->andWhere("o.discountType != 'NW'")
                ->andWhere("o.discountType = 'CD'")
                ->andWhere("o.Visability != 'MEM'")
                ->andWhere('o.userGenerated = 0')
                ->orderBy('o.startDate', 'DESC');
        if ($shopId!='') {
                    $query->andWhere('s.id = '.$shopId);
        }
        if ($userId!="") {
                    $query->andWhere('o.authorId = '.$userId);
        }
        $query->setMaxResults($limit);
        $newOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $newOffers;
    }
    
    public static function getrelatedOffers($shopId)
    {
        $currentDate = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
            ->select('t,o,s,s.permaLink as permalink,tc,img.name,img.path,ws.name,ws.path,ologo')
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.logo', 'ologo')
            ->leftJoin('o.offertermandcondition', 'tc')
            ->leftJoin('o.offerTiles', 't')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('s.screnshot', 'ws')
            ->where('o.shopOffers = '.$shopId)
            ->andWhere('o.endDate <='."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'")
            ->andWhere('o.deleted = 0')
            ->setMaxResults(1);
        $relatedOffers = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $relatedOffers;
    }
    ##################################################################################
    ################## END REFACTORED CODE ###########################################
    ##################################################################################

    public static function moveToTrash($id)
    {
        if ($id) {
            //find record by id
            $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerUser
            ->select('s.id as shopId')
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.id = '.$id);
            $u = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $offer_id = $id;
            $authorId = self::getAuthorId($offer_id);

            //Delete popular code if exist
            $query = $entityManagerUser
            ->select('p')
            ->from('KC\Entity\PopularCode', 'p')
            ->where('p.popularcode = '.$offer_id);
            $exist = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if ($exist) {
                \KC\Repository\PopularCode::deletePopular($offer_id, $exist['position']);
            }
            //Delete popular code if exist
            $uid = $authorId[0]['authorId'];
            $popularcodekey ="all_". "popularcode".$uid ."_list";
            $newcodekey ="all_". "newestcode".$uid ."_list";

            $key = '6_topOffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_latestUpdates'  .$u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_expiredOffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($newcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeTopCategoriesOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget5_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget6_list');
            $entityManagerUser->update('KC\Entity\Offer', 'od')
            ->set('od.deleted', 1)
            ->where('od.id ='.$id)
            ->getQuery()->execute();
        } else {
            $id = null;
        }
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
        return $id;
    }

    public static function deleteOffer($id)
    {
        if ($id) {
            //find record by id and change status (deleted=1)
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
            ->select('s.id as shopId, o.extendedUrl')
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.id = '.$id);
            $u = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            $offer_id = $id;
            $authorId = self::getAuthorId($offer_id);
            $uid = $authorId[0]['authorId'];
            $popularcodekey ="all_". "popularcode".$uid ."_list";
            $newcodekey ="all_". "newestcode".$uid ."_list";
            $key = '6_topOffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_latestUpdates'  .$u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_expiredOffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'extendedTopOffer_of_'.$u['shopId'];
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'extended_'.
                \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($u['extendedUrl']).
                '_couponDetails';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'offer_'.$id.'_details';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($newcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage_offers');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeTopCategoriesOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\RefOfferCategory', 'w')
                    ->where("w.category=" . $id)
                    ->getQuery();
            $query->execute();
            $query = $queryBuilder->delete('KC\Entity\TermAndCondition', 't')
                    ->where("t.termandcondition=" . $id)
                    ->getQuery();
            $query->execute();
            $query = $queryBuilder->delete('KC\Entity\PopularCode', 'pc')
                    ->where("pc.popularcode=" . $id)
                    ->getQuery();
            $query->execute();
            $query = $queryBuilder->delete('KC\Entity\RefOfferPage', 'ro')
                    ->where("ro.refoffers=" . $id)
                    ->getQuery();
            $query->execute();
            $query = $queryBuilder->delete('KC\Entity\ViewCount', 'v')
                    ->where("v.viewcount=" . $id)
                    ->getQuery();
            $query->execute();
            $query = $queryBuilder->delete('KC\Entity\OfferNews', 'n')
                    ->where("n.offerId=" . $id)
                    ->getQuery();
            $query->execute();
            $key = 'all_widget5_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'all_widget6_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $query = $queryBuilder->delete('KC\Entity\Offer', 'od')
                    ->where("od.id=" . $id)
                    ->getQuery();
            $query->execute();

        } else {
            $id = null;
        }
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage_offers');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeTopCategoriesOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        return $id;
    }

    public static function restoreOffer($id)
    {
        if ($id) {

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
            ->select('s.id as shopId')
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.id = '.$id);
            $u = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            $offer_id = $id;
            $authorId = self::getAuthorId($offer_id);

            $uid = $authorId[0]['authorId'];
            $popularcodekey ="all_". "popularcode".$uid ."_list";
            $newcodekey ="all_". "newestcode".$uid ."_list";
            $key = '6_topOffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'shop_latestUpdates'  .$u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'shop_expiredOffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($newcodekey);

            $key = 'all_widget5_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'all_widget6_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            //update status of record by id(deleted=0)
            $query = $queryBuilder->update('KC\Entity\Offer', 'od')
                ->set('od.deleted', 0)
                ->where('od.id='.$id)
                ->getQuery();
            $query->execute();
        } else {
            $id = null;
        }
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage_offers');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeTopCategoriesOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        return $id;
    }

    public static function searchToFiveOffer($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('o.title as title')
                ->from('KC\Entity\Offer', 'o')
                ->where('o.deleted='.$flag)
                ->andWhere("o.title LIKE '$keyword%'")
                ->andWhere('o.userGenerated = 0')
                ->orderBy('o.title', 'ASC')
                ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function searchToFiveShop($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s')
                ->from('KC\Entity\Offer', 'o')
                ->leftJoin('o.shopOffers', 's')
                ->where('o.deleted='.$flag)
                ->andWhere("s.name LIKE '$keyword%'")
                ->andWhere('o.userGenerated = 0')
                ->orderBy('s.id', 'ASC')
                ->groupBy('s.name')
                ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function searchToFiveCoupon($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('o')
                ->from('KC\Entity\Offer', 'o')
                ->where('o.deleted='.$flag)
                ->andWhere("o.couponCode LIKE '$keyword%'")
                ->orderBy('o.id', 'ASC')
                ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function addkortingscode($title, $shopid, $kortingscode, $desc, $userid, $uname)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $pc = new KC\Entity\Offer();
        $pc->shopId =$shopid;
        $pc->title =\BackEnd_Helper_viewHelper::stripSlashesFromString($title);
        $pc->couponCode =\BackEnd_Helper_viewHelper::stripSlashesFromString($kortingscode);
        $pc->extendedMetaDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($desc);
        $pc->userGenerated = 1;
        $pc->authorId = \BackEnd_Helper_viewHelper::stripSlashesFromString($userid);
        $pc->authorName =\BackEnd_Helper_viewHelper::stripSlashesFromString($uname);
        \Zend_Registry::get('emLocale')->persist($pc);
        \Zend_Registry::get('emLocale')->flush();
        return true;
    }

    public static function getAuthorId($offerId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('o.authorId')
                ->from('KC\Entity\Offer', 'o')
                ->where('o.id='.$offerId);
        $userId = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userId;
    }

    public static function exportofferList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
                ->select('o.title,s.name,s.deepLink as deeplink,term.content')
                ->from('KC\Entity\Offer', 'o')
                ->leftJoin('o.shopOffers', 's')
                ->leftJoin('o.offertermandcondition', 'term')
                ->addSelect("(SELECT COUNT(v.id) FROM KC\Entity\ViewCount v WHERE v.viewcount = o.id) as Count")
                ->where("o.deleted=0")
                ->andWhere("o.userGenerated=0")
                ->orderBy("o.id", "DESC");
        $offerList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offerList;
    }

    public static function getOfferDetail($offerId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select(
            'o.title,o.id,s.name,s.notes,s.strictConfirmation,s.accountManagerName,a.name as affname,
            p.id as pageId,tc.content,cat.id,img.name,img.path,news.title, news.url, news.content,
            t.path,t.name,t.position, s.id as shopId'
        )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.affliatenetwork', 'a')
        ->leftJoin('o.offers', 'p')
        ->leftJoin('o.offertermandcondition', 'tc')
        ->leftJoin('o.categoryoffres', 'cat')
        ->leftJoin('o.logo', 'img')
        ->leftJoin('s.offerNews', 'news')
        ->leftJoin('o.offerTiles', 't')
        ->addSelect("(SELECT count(cc.status) FROM KC\Entity\CouponCode cc WHERE cc.offer = o.id and cc.status = 0) as used")
        ->addSelect("(SELECT count(ccc.status) FROM KC\Entity\CouponCode ccc WHERE ccc.offer = o.id and ccc.status = 1) as available")
        ->andWhere("o.id =".$offerId)
        ->andWhere("o.userGenerated = '0'")
        ->setMaxResults(1);
        $offerDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offerDetails;
    }

    public function uploadFile($imgName)
    {
        $uploadPath = "images/upload/offer/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $user_path = ROOT_PATH . $uploadPath;
        $img = $imgName;

        //unlink image file from folder if exist
        if ($img) {
            unlink($user_path . $img);
            unlink($user_path . "thum_" . $img);
            unlink($user_path . "thum_large" . $img);
        }
        if (!file_exists($user_path)) {
            mkdir($user_path, 0776, true);
        }
        $adapter->setDestination(ROOT_PATH . $uploadPath);
        $adapter->addValidator('Extension', false, 'jpg,pdf,jpeg');
        $adapter->addValidator('Size', false, array('max' => '2MB'));
        $files = $adapter->getFileInfo();

        foreach ($files as $file => $info) {
            if ($file=='uploadoffer' && $info['name']!='') {

                $name = $adapter->getFileName($file, false);
                $name = $adapter->getFileName($file);
                $orgName = time() . "_" . $info['name'];
                $fname = $user_path . $orgName;
                //call function resize image
                $path = ROOT_PATH . $uploadPath . "thum_" . $orgName;
                \BackEnd_Helper_viewHelper::resizeImage(
                    $_FILES["uploadoffer"],
                    $orgName,
                    126,
                    90,
                    $path
                );
                //call function resize image
                $path = ROOT_PATH . $uploadPath . "thum_large" . $orgName;
                \BackEnd_Helper_viewHelper::resizeImage(
                    $_FILES["uploadoffer"],
                    $orgName,
                    132,
                    95,
                    $path
                );
                $adapter->addFilter(
                    new \Zend_Filter_File_Rename(
                        array(
                            'target' => $fname,
                            'overwrite' => true
                        )
                    ),
                    null,
                    $file
                );
                $adapter->receive($file);
                $status = "";
                $data = "";
                $msg = "";
                if ($adapter->isValid($file) == 1) {
                    $data = $orgName;
                    return $data;
                } else {
                    return false;
                }
            }
        }
    }

    public static function uploadTiles($imgName)
    {
        $uploadPath = UPLOAD_IMG_PATH."offertiles/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $user_path = ROOT_PATH . $uploadPath;
        $img = $imgName;
        //unlink image file from folder if exist
        if ($img) {
            unlink($user_path . $img);
            unlink($user_path . "thum_" . $img);
            unlink($user_path . "thum_large" . $img);
        }
        if (!file_exists($user_path)) {
            mkdir($user_path, 0776, true);
        }
        $adapter->setDestination(ROOT_PATH . $uploadPath);
        $adapter->addValidator('Extension', false, 'jpg,jpeg,png');
        $adapter->addValidator('Size', false, array('max' => '2MB'));
        $files = $adapter->getFileInfo();
        foreach ($files as $file => $info) {

            $name = $adapter->getFileName($file, false);
            $name = $adapter->getFileName($file);
            $orgName = time() . "_" . $info['name'];
            $fname = $user_path . $orgName;
            //call function resize image
            $path = ROOT_PATH . $uploadPath . "thum_" . $orgName;

            BackEnd_Helper_viewHelper::resizeImageForAjax(
                $_FILES["tileupload"],
                $orgName,
                126,
                90,
                $path
            );
            //call function resize image
            $path = ROOT_PATH . $uploadPath . "thum_large" . $orgName;
            BackEnd_Helper_viewHelper::resizeImageForAjax(
                $_FILES["tileupload"],
                $orgName,
                132,
                95,
                $path
            );

            $path = ROOT_PATH . $uploadPath . "thum_small_" . $orgName;
            $thum_small = BackEnd_Helper_viewHelper::resizeImageForAjax(
                $_FILES["tileupload"],
                $orgName,
                80,
                80,
                $path
            );

            $path = ROOT_PATH . $uploadPath . "thum_large_" . $orgName;
            $thum_small = BackEnd_Helper_viewHelper::resizeImageForAjax(
                $_FILES["tileupload"],
                $orgName,
                127,
                127,
                $path
            );

            $adapter->addFilter(
                new Zend_Filter_File_Rename(
                    array(
                            'target' => $fname,
                            'overwrite' => true
                    )
                ),
                null,
                $file
            );

            $adapter->receive($file);
            $status = "";
            $data = "";
            $msg = "";
            if ($adapter->isValid($file) == 1) {
                $data = $orgName;
                return $data;
            } else {
                return false;
            }
        }
    }

    public function uploadShopLogo($file)
    {
        // generate upload path for images related to category
        $uploadPath = UPLOAD_IMG_PATH."shop/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        // generate real path for upload path
        $rootPath = ROOT_PATH.$uploadPath;
        // get upload file info
        $files = $adapter->getFileInfo($file);

        // check upload directory exists, if no then create upload directory
        if (!file_exists($rootPath)) {
            mkdir($rootPath, 0776, true);
        }

        // set destination path and apply validations
        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, 'jpg,png');
        $adapter->addValidator('Size', false, array('max' => '2MB'));
        // get file name
        $name = $adapter->getFileName($file, false);
        // rename file name to by prefixing current unix timestamp
        $newName = time() . "_" . $name;
        // generates complete path of image
        $cp = $rootPath . $newName;
        /**
         *   generating thumnails for image
         */

        $path = ROOT_PATH . $uploadPath . "thum_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 135, 95, $path);

        $path = $uploadPath . "thum_medium_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 50, 50, $path);

        $path = $uploadPath . "thum_large_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 95, 95, $path);

        $path = $uploadPath . "thum_small_" . $newName;
        BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 24, 24, $path);

        //echo "<pre>"; print_r($file); die;
        // apply filter to rename file name and set target
        $adapter
        ->addFilter(
            new Zend_Filter_File_Rename(
                array(
                    'target' => $cp,
                    'overwrite' => true
                )
            ),
            null,
            $file
        );

        // recieve file for upload
        $adapter->receive($file);
        // check is file is valid then
        if ($adapter->isValid($file) == 1) {
            $data = $newName;
            return $data;
        } else {
            return false;
        }
    }

    public static function searchRelatedOffers()
    {
        $suggestion = array();
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select(
            'p.id,o.endDate as enddate,o.title,s.refUrl,s.actualUrl,s.permaLink as permalink,
            o.Visability,o.extendedUrl,o.couponCode as couponcode, o.exclusiveCode as exclusivecode,
            o.discount,o.discountvalueType,s.name,l.path,l.name,p.type,p.position,
            p.offerId,fv.shopId,fv.visitorId,vot.id,vot.vote'
        )
        ->from('KC\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.votes', 'vot')
        ->leftJoin('s.visitors', 'fv')
        ->leftJoin('s.logo', 'l')
        ->where('o.deleted = 0')
        ->andWhere("o.userGenerated = 0")
        ->andWhere('s.deleted = 0')
        ->andWhere('o.offline = 0')
        ->andWhere('o.endDate >'."'".$currentDate."'")
        ->andWhere('o.startDate <='."'".$currentDate."'")
        ->setParameter(1, 'CD')
        ->andWhere('o.discountType = ?1')
        ->setParameter(2, 'MEM')
        ->andWhere('o.Visability != ?2')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults(4);
        $data= $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        foreach ($data as $d) {
            $suggestion[] = $d['offer'];
        }
        return $suggestion;
    }

    public static function getCouponDetails($extendedUrl)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select(
            't.name, t.path, o.id, o.title, o.couponCode, s.actualUrl,s.name,s.id as shopId,s.discussions,s.permaLink as permalink,
            s.title,s.subTitle,s.deepLink,s.deepLinkStatus,s.refUrl,s.actualUrl,
            s.affliateProgram,tc.content,img.name,img.path,ws.name,ws.path,ologo.name, ologo.path'
        )
           ->from('KC\Entity\Offer', 'o')
           ->leftJoin('o.shopOffers', 's')
           ->leftJoin('o.logo', 'ologo')
           ->leftJoin('o.offerTiles', 't')
           ->leftJoin('o.offertermandcondition', 'tc')
           ->leftJoin('s.logo', 'img')
           ->leftJoin('s.screnshot', 'ws')
           ->Where("o.extendedUrl = '".$extendedUrl."'")
           ->andWhere('o.extendedOffer = 1')
           ->andWhere('s.status = 1');
        $couponDetails= $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $couponDetails;
    }

    public static function getpopularOffers($offerId, $cpnDetails)
    {
        $date = date('Y-m-d H:i:s');
        $title = "";
        if (isset($cpnDetails[0]['title']) && $cpnDetails[0]['title'] != "") {
            $title = $cpnDetails[0]['title'];
        }
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('po.type, po.position,o.id, o.title,s.id as shopId,s.name,s.permaLink as permalink,img.name,img.path')
        ->from('KC\Entity\PopularCode', 'po')
        ->leftJoin('p.popularcode', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'img')
        ->where('o.title !='."'".$title."'")
        ->andWhere('o.endDate >='."'".$currentDate."'")
        ->andWhere('o.startDate <='."'".$currentDate."'")
        ->andWhere('o.deleted = 0')
        ->andWhere('s.deleted = 0')
        ->orderBy('po.id', 'DESC')
        ->setMaxResults(3);
        $popularOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularOffers;
    }

    public static function commongetpopularOffers($type, $limit, $shopId = 0, $userId = "")
    {

        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select(
            's.id as shopId,s.name,s.refUrl, s.actualUrl, s.permaLink as permalink,terms.content,
            p.id,o.id,o.Visability,o.title,o.authorId,o.discountvalueType,o.exclusiveCode,
            o.discount,o.couponCodeType,o.userGenerated,o.couponCode,o.refOfferUrl,o.refURL,
            o.discountType,o.startDate as startdate,o.endDate,img.id as imageId, img.path, img.name,fv.shopId,
            fv.visitorId,ologo.name, o.logo.path,vot.id,vot.vote'
        )
            ->from('KC\Entity\PopularCode', 'p')
            ->leftJoin('p.popularcode', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.logo', 'ologo')
            ->leftJoin('o.votes', 'vot')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('s.visitors', 'fv')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->where('o.deleted = 0')
            ->andWhere(
                "(o.couponCodeType = 'UN' AND (SELECT count(c.id)  FROM CouponCode c WHERE c.offer = o.id and c.status=1)  > 0) 
                or o.couponCodeType = 'GN'"
            )
            ->andWhere("o.userGenerated = 0")
            ->andWhere('o.endDate >'."'".$date."'")
            ->andWhere('o.startDate <='."'".$date."'")
            ->andWhere('s.deleted = 0')
            ->andWhere('s.status = 1')
            ->setParameter(1, 'CD')
            ->andWhere('o.discountType = ?1')
            ->setParameter(2, 'MEM')
            ->andWhere('o.Visability != ?2')
            ->andWhere('o.userGenerated=0');
        if ($shopId != '') {
            $query->andWhere('s.id ='.$shopId);
        }

        if ($userId != '') {
            $query->andWhere('o.authorId = '.$userId);
        }

        if ($limit != '') {
            $query->setMaxResults($limit);
        }

        $query = $query->orderBy('p.position', 'ASC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $newData = array();
        foreach ($data as $res) {
            $newData[] = $res['offer'];
        }
        return $newData;
    }

    public static function commongetMemberOnlyOffer($type, $limit)
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select(
            's.id,s.name,s.usergenratedcontent, s.permaLink as permalink,s.deepLink,
            s.deepLinkStatus,s.refUrl,s.actualUrl,terms.content,o.id,o.Visability,o.title,
            o.authorId,o.discountvalueType,o.exclusiveCode,o.discount,o.userGenerated,
            o.couponCode,o.couponCodeType,o.refOfferUrl,o.refURL as refUrl,o.discountType,o.endDate,
            img.id, img.path, img.name,IDENTITY(fv.visitors) as test,IDENTITY(fv.visitors) as test2,ologo.name,ologo.path,vot.id,vot.vote'
        )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.logo', 'ologo')
        ->leftJoin('o.votes', 'vot')
        ->leftJoin('s.logo', 'img')
        ->leftJoin('s.visitors', 'fv')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->where('o.deleted = 0')
        ->andWhere('s.deleted = 0')
        ->setParameter(1, 'MEM')
        ->andWhere('o.Visability = ?1')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->setParameter(3, 'NW')
        ->andWhere('o.discountType != ?3')
        ->setParameter(4, 'CD')
        ->andWhere('o.discountType = ?4')
        ->andWhere('o.userGenerated = 0')
        ->orderBy('o.id', 'DESC')
        ->setMaxResults($limit);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getNewestOffersForRSS()
    {
        $currentDate = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select(
            'term.content as terms,img.name as shopImageName,img.path as shopImagePath,
            o.id,o.title,s.permaLink as permalink,o.updated_at as lastUpdate'
        )
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.logo', 'ologo')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('o.offertermandcondition', 'term')
            ->where('o.deleted = 0')
            ->andWhere('s.deleted = 0')
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'")
            ->setParameter(1, 'MEM')
            ->andWhere('o.Visability != ?1')
            ->setParameter(3, 'NW')
            ->andWhere('o.discountType != ?3')
            ->setParameter(4, 'CD')
            ->andWhere('o.discountType = ?4')
            ->andWhere('((o.userGenerated=0 and o.approved = 0) or (o.userGenerated=1 and o.approved = 1))')
            ->orderBy('o.startDate', 'DESC');
        $newestOffersForRss = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $newestOffersForRss;
    }

    public static function getPopularOffersForRSS()
    {
        $currentDate = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                'term.content as terms,o.id,o.title,s.permaLink as permalink,p.id,o.updated_at as lastUpdate,
                img.name as shopImageName,img.path as shopImagePath'
            )
            ->from('KC\Entity\PopularCode', 'p')
            ->leftJoin('p.popularcode', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('o.offertermandcondition', 'term')
            ->where('o.deleted = 0')
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'")
            ->andWhere('s.deleted = 0')
            ->setParameter(1, 'CD')
            ->andWhere('o.discountType = ?1')
            ->setParameter(2, 'MEM')
            ->andWhere('o.Visability != ?2')
            ->andWhere('o.userGenerated = 0')
            ->orderBy('p.position', 'ASC');
        $popularOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularOffers;
    }

    public static function commongetextendedOffers($type, $limit, $shopId = 0)
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
                ->select('o.id,o.title,img.path, img.name')
                ->from('KC\Entity\Offer', 'o')
                ->leftJoin('o.shopOffers', 's')
                ->leftJoin('o.logo', 'img')
                ->where('o.deleted = 0')
                ->andWhere('s.deleted = 0')
                ->andWhere('o.extendedOffer = 1')
                ->setParameter(1, 'NW')
                ->andWhere('o.discountType != ?1')
                ->setParameter(2, 'CD')
                ->andWhere('o.discountType = ?2')
                ->andWhere('o.endDate >'."'".$date."'")
                ->andWhere('o.startDate <='."'".$date."'")
                ->orderBy('o.id', 'DESC')
                ->setMaxResults($limit);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function commongetrelatedshops($type, $limit, $shopId = 0)
    {
        $data =  null;
        $date = date('Y-m-d H:i:s');
        $lastdata = \FrontEnd_Helper_viewHelper::getallrelatedshopsid($shopId);
        if (sizeof($lastdata)>0) {
            for ($i=0; $i<sizeof($lastdata); $i++) {
                $shopdata[$i] = $lastdata[$i]['relatedshopId'];
            }
            $shopvalues = implode(",", $shopdata);
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select(
                    's.id as shopId,s.permaLink as permalink,s.name,s.deepLink,s.deepLinkStatus,s.refUrl,s.actualUrl,
                    terms.content,o.id,o.title, o.Visability, o.couponCode, o.refOfferUrl as refofferurl,
                    o.startDate as startdate , o.endDate as enddate, o.exclusiveCode, o.editorPicks,
                    o.extendedOffer as extendedoffer ,o.extendedUrl,o.discount,
                    o.authorId, o.authorName, o.userGenerated, o.approved,o.discountvalueType,
                    img.id, img.path, img.name,fv.shopId,fv.visitorId,fv.id,vot.id,vot.vote'
                )
                ->from('KC\Entity\Offer', 'o')
                ->leftJoin('o.shopOffers', 's')
                ->leftJoin('s.visitors', 'fv')
                ->leftJoin('o.offertermandcondition', 'terms')
                ->leftJoin('o.votes', 'vot')
                ->leftJoin('s.logo', 'img')
                ->setParameter(1, $shopvalues)
                ->where($queryBuilder->expr()->in('o.shopOffers', '?1'))
                ->andWhere('o.deleted = 0')
                ->andWhere('s.deleted = 0')
                ->andWhere('o.endDate >'."'".$date."'")
                ->andWhere('o.startDate <='."'".$date."'")
                ->setParameter(1, 'CD')
                ->andWhere('o.discountType = ?1')
                ->andWhere('o.shopid !='.$shopId)
                ->orderBy('o.startDate', 'DESC')
                ->setMaxResults($limit);
            $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } else {
            $data = array();
        }
        return $data;
    }

    public static function commongetallrelatedshopsid($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('ref.relatedshopId,s.name,s.id')
            ->from('KC\Entity\RefShopRelatedshop', 'ref')
            ->leftJoin('ref.shop', 's')
            ->andWhere("ref.shop=".$shopId)
            ->orderBy("s.name", "ASC");
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getLatestUpdates($type, $limit, $shopId = 0)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('n')
            ->from('KC\Entity\OfferNews', 'n')
            ->andWhere('n.shop = ' . $shopId)
            ->orderBy('n.startdate', 'DESC')
            ->setMaxResults($limit);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getNewstoffers($flag)
    {
        $memOnly = "MEM";
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select(
            'o.title,o.Visability,o.couponCode,o.exclusiveCode,o.editorPicks,o.discount,
            o.discountvalueType,s.name,s.views,l.name,l.path'
        )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'l')
        ->where('o.Visability!=' ."'$memOnly'")
        ->andWhere('o.deleted =0')
        ->andWhere('s.deleted =0')
        ->setParameter(1, 'NW')
        ->andWhere('o.discountType != ?1')
        ->orderBy('o.id', 'DESC')
        ->setMaxResults($flag);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getAdditionalTopKortingscodeForShopPage($shopCategories, $offerIDs, $limit = 5)
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                'p.id as popularCodeId,o.id,o.authorId,o.refURL,o.couponCodeType,o.discountType,o.title,
                o.discountvalueType,
                o.Visability, o.exclusiveCode,o.editorPicks,o.userGenerated,o.couponCode,o.extendedOffer,
                o.totalViewcount,
                o.startDate, o.endDate,o.refOfferUrl, o.extendedUrl,l.name,l.path,t.path,t.name,t.position,t.label,
                s.id as shopId,s.name,s.permalink as permalink,
                s.usergenratedcontent,s.deepLink,
                s.deepLinkStatus,s.refUrl,s.actualUrl,terms.content,img.id, img.path, img.name,fv.shopId,
                fv.visitorId,fv.id,vot.id,vot.vote'
            )
        ->from('KC\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.categoryshops', 'sc')
        ->leftJoin('o.logo', 'l')
        ->leftJoin('s.logo', 'img')
        ->leftJoin('s.visitors', 'fv')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->leftJoin('o.votes', 'vot')
        ->leftJoin('o.offerTiles', 't')
        ->where('o.deleted = 0')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM CouponCode cc WHERE cc.offer = o.id and cc.status=1)  > 0)
             or o.couponCodeType = 'GN'"
        )
        ->andWhere($queryBuilder->expr()->notIn('sc.categoryId', $shopCategories))
        ->andWhere($queryBuilder->expr()->notIn('o.id', $offerIDs))
        ->andWhere('s.deleted=0')
        ->andWhere('o.offline = 0')
        ->andWhere('s.status = 1')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->setParameter(1, 'CD')
        ->andWhere('o.discountType = ?1')
        ->andWhere('o.userGenerated = 0')
        ->setParameter(2, 'MEM')
        ->andWhere('o.Visability != ?2')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults($limit);
        $additionalTopKortingscodeForShopPage = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $additionalTopKortingscodeForShopPage;
    }

    public static function getCouponOffersHowToGuide(
        $pLink,
        $limit = null,
        $getExclusiveOnly = false,
        $includingOffline = false
    )
    {
        $nowDate = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select(
            'l.name,l.path,t.path,t.name,t.position,s.id as shopId,s.name,s.permaLink as permalink,s.usergenratedcontent,s.deepLink,
            s.deepLinkStatus,s.refUrl,s.actualUrl,terms.content,o.id, o.title,img.id, img.path, img.name,
            fv.shopId,fv.visitorId,fv.id,vot.id,vot.vote'
        )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.logo', 'l')
        ->leftJoin('s.logo', 'img')
        ->leftJoin('s.visitors', 'fv')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->leftJoin('o.votes', 'vot')
        ->leftJoin('o.offerTiles', 't')
        ->where('o.deleted = 0');
        if (!$includingOffline) {
            $query = $query->andWhere('o.offline = 0')
                ->andWhere('o.endDate >='."'".$nowDate."'")
                ->andWhere('o.startDate <='."'".$nowDate."'");
        }

        $query= $query->andWhere(
            '(o.userGenerated=0 and o.approved = 0) or (o.userGenerated = 1 and o.approved = 1)'
        )
        ->andWhere('s.permaLink='."'".$pLink."'")
        ->andWhere('s.deleted =0')
        ->setParameter(1, 'CD')
        ->andWhere('o.discountType = ?1')
        ->setParameter(2, 'NW')
        ->andWhere('o.discountType != ?2')
        ->setParameter(3, 'MEM')
        ->andWhere('o.Visability != ?3')
        ->orderBy('o.exclusiveCode', 'DESC')
        ->addOrderBy('o.discountType', 'ASC')
        ->addOrderBy('o.startDate', 'DESC')
        ->addOrderBy('o.popularityCount', 'DESC')
        ->addOrderBy('o.title', 'ASC');
        // check need to get execlusive offers or not
        if ($getExclusiveOnly) {
            $query = $query->andWhere('o.exclusiveCode = 1');
        }
        // check $limit if passed or not
        if ($limit) {
            $query = $query->setMaxResults($limit);
        }
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getpopularOffersOfShops($shopId, $limit)
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                'po.position, po.status, po.type,o.title,o.id,terms.content,s.id as shopId,s.name,
                img.name,img.path,fv.id,fv.visitorId,fv.shopId,vot.id,vot.vote'
            )
            ->from('KC\Entity\PopularVouchercodes', 'po')
            ->leftJoin('po.offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->leftJoin('o.votes', 'vot')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('s.visitors', 'fv')
            ->Where('s.id='.$shopId)
            ->andWhere('o.deleted =0')
            ->andWhere('o.endDate >'."'".$date."'")
            ->andWhere('o.startDate <='."'".$date."'")
            ->andWhere('s.deleted =0')
            ->orderBy('po.offer', 'DESC')
            ->setMaxResults($limit);
        $popularOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularOffers;
    }

    public static function updateTotalViewCount()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                'o.id, o.totalViewcount, o.startDate'
            )
            ->from('KC\Entity\Offer', 'o')
            ->addSelect(
                "(SELECT  sum(v.onClick) as click FROM KC\Entity\ViewCount v WHERE v.viewcount = o.id and v.counted=0)
                as clicks"
            );
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        foreach ($data as $value) {

            # update only when there ar new click out in view_count table
            if ($value['clicks']) {

                    \KC\Repository\ViewCount::processViewCount($value['id']);

                    # COUNT POPULAITY OF AN OFFER based on otal clicks

                    $newtotal = intval($value['clicks']) + intval($value['totalViewcount']) ;

                    $dStart = date("y-m-d h:i:s");
                    $dEnd  = $value['startDate'];
                    $dDiff = $dEnd->diff($dStart);

                    $diff = (int) $dDiff->days ;

                    $popularity = round($newtotal / ($diff > 0 ? $diff : 1 ), 4);

                    # update popularity and otal click counts
                      $query = $queryBuilder
                            ->update('KC\Entity\Offer', 'o')
                            ->set('o.totalViewcount', $newtotal)
                            ->set('o.popularityCount', "'".$popularity."'")
                            ->where('o.id ='.$value['id'])
                            ->getQuery();
                            $query->execute();
            }
        }
    }

    public static function getmemberexclusiveOffersOfShops()
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                'po.position, po.status, po.type,o.id, o.title,s.id as shopId,s.name,img.name,img.path,vot.id,vot.vote'
            )
        ->from('KC\Entity\PopularVouchercodes', 'po')
        ->leftJoin('po.offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.votes', 'vot')
        ->leftJoin('s.logo', 'img')
        ->where('s.id = o.shopOffers')
        ->andWhere('o.exclusiveCode != 0')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->andWhere('o.deleted =0')
        ->andWhere('s.deleted =0')
        ->orderBy('o.title', 'ASC');
        $popularOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $popularOffers;
    }

    public static function getMemberonly()
    {
        $date = date('Y-m-d H:i:s');
        $memOnly = "MEM";
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select(
            'o.title,o.authorId,o.authorName,o.Visability,o.couponCode,o.exclusiveCode,o.editorPicks,o.discount,
             o.discountvalueType,s.name,s.views,l.name,l.path,fv.id,fv.visitorId,fv.shopId,vot.id,vot.vote'
        )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.votes', 'vot')
        ->leftJoin('s.logo', 'l')
        ->leftJoin('s.visitors', 'fv')
        ->setParameter(1, 'MEM')
        ->where('o.Visability = ?1')
        ->andWhere('o.deleted =0')
        ->andWhere('s.deleted =0')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->orderBy('o.id', 'DESC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $k = 0;

        if (count($data) > 0) {
            foreach ($data as $pag) {
                $data[$k]['marginCounter'] = 4;
                $k++;
            }
        }
        return $data;
    }

    public static function findNoOfOffersByUser($uid)
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('count(o.id) as cout')
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->where('o.authorId=' .$uid)
        ->andWhere('o.deleted =0')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->andWhere('s.deleted =0');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function countVotes($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('count(v.id) as cnt')
        ->from("KC\Entity\Votes v")
        ->where("v.offer=".$id)
        ->andWhere("v.deleted=0")
        ->setParameter(1, 'positive')
        ->andWhere('v.vote = ?1');
        $positiveVotes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $query = $queryBuilder
        ->select('count(v.id) as cnt')
        ->from('KC\Entity\Votes v')
        ->where('v.offer='.$id)
        ->andWhere('v.deleted=0')
        ->setParameter(1, 'negative')
        ->andWhere('v.vote = ?1');
        $negativeVotes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $arr = array();
        $arr['vote'] = (($positiveVotes[0]['cnt'])/($negativeVotes[0]['cnt']+$positiveVotes[0]['cnt']))*100 ;
        $arr['poscount'] = $positiveVotes[0]['cnt'];
        return $arr;
    }

    public static function getOfferShopDetail($offerId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('l.name,l.path,s.permaLink, s.id as shopId')
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'l')
            ->where('s.deleted=0')
            ->andWhere("o.id =". $offerId);
        $shopLogo = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopLogo;
    }

    public static function getAllExtendedOffers()
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('o.id, o.extendedUrl')
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.deleted=0')
            ->andWhere('s.deleted = 0')
            ->andWhere('o.extendedOffer=1')
            ->setParameter(1, 'CD')
            ->andWhere('o.discountType = ?1')
            ->andWhere('o.endDate >'."'".$date."'")
            ->andWhere('o.startDate <='."'".$date."'")
            ->orderBy('o.id', 'DESC');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getAllUrls($id)
    {
        # get offer data
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                "o.id, o.extendedOffer,o.authorId , o.extendedUrl,
                s.permaLink, s.howToUse ,s.contentManagerId , sp.permaLink, p.permaLink,c.permaLink"
            )
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.categoryoffres', 'c')
            ->leftJoin('s.shopPage', 'sp')
            ->leftJoin('o.offers', 'p')
            ->where("o.id=".$id);
        $offer = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $urlsArray = array();

        # check for related shop permalink
        if (isset($offer['shop'])) {
            $urlsArray[] = $offer['shop']['permaLink'];

            # check if a shop has editor or not
            if (isset($offer['shop']['contentManagerId'])) {

                # redactie permalink
                $redactie =  \KC\Repository\User::returnEditorUrl($offer['shop']['contentManagerId']);

                # check if an editor  has permalink then add it into array
                if (isset($redactie['permalink']) && strlen($redactie['permalink']) > 0) {
                    $urlsArray[] = $redactie['permalink'] ;
                }
            }

        }

        # check for extende offer page
        if (isset($offer['extendedOffer'])) {
            # check for extende offer url
            if ($offer['extendedUrl'] && strlen($offer['extendedUrl']) > 0) {
                $urlsArray[] = \FrontEnd_Helper_viewHelper::__link('link_deals') .'/'. $offer['extendedUrl'];
            }
        }

        # check for shop permalink
        if ($offer['shop']['howToUse']) {
            # check for extende offer url
            if (isset($offer['shop']['permaLink'])  && strlen($offer['shop']['permaLink']) > 0) {
                $urlsArray[] = \FrontEnd_Helper_viewHelper::__link('link_how-to') .'/'. $offer['shop']['permaLink'];
            }
        }

        # check an offerr has one or more categories
        if (isset($offer['category']) && count($offer['category']) > 0) {

            $cetgoriesPage = \FrontEnd_Helper_viewHelper::__link('link_categorieen') .'/' ;
            # traverse through all catgories
            foreach ($offer['category'] as $value) {
                # check if a category has permalink then add it into array
                if (isset($value['permaLink']) && strlen($value['permaLink']) > 0) {
                    $urlsArray[] = $cetgoriesPage . $value['permaLink'];
                    $urlsArray[] = $cetgoriesPage . $value['permaLink'] .'/2';
                    $urlsArray[] = $cetgoriesPage . $value['permaLink'] .'/3';
                }
            }
        }

        # check an offerr has one or more pages
        if (isset($offer['page']) && count($offer['page']) > 0) {
            # traverse through all pages
            foreach ($offer['page'] as $value) {
                # check if a page has permalink then add it into array
                if (isset($value['permaLink']) && strlen($value['permaLink']) > 0) {
                    $urlsArray[] = $value['permaLink'] ;
                }
            }
        }

        return $urlsArray ;
    }

    public static function getAmountOffersCreatedLastWeek()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        // - 7 days from today
        $past7Days = date($format, strtotime('-7 day' . $date));
        $nowDate = $date;

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select("count(o.id) as amountOffers")
        ->from('KC\Entity\Offer', 'o')
        ->where('o.deleted = 0')
        ->setParameter(1, 'NW')
        ->andWhere('o.discountType != ?1')
        ->andWhere('o.created_at BETWEEN "'.$past7Days.'" AND "'.$date.'"');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getTotalAmountOfOffers()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select("count(o.id) as amountOffers")
        ->from('KC\Entity\Offer', 'o')
        ->where('o.deleted = 0')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->setParameter(1, 'NW')
        ->andWhere('o.discountType != ?1');
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getTotalAmountOfShopCoupons($shopId, $type = '')
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select("count(o.id) as cnt")
        ->from('KC\Entity\Offer', 'o')
        ->where('o.deleted = 0')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->andWhere('o.shopOffers = '.$shopId)
        ->setParameter(1, 'NW')
        ->andWhere('o.discountType != ?1');
        if ($type == 'CD') {
            $query->setParameter(2, 'CD');
            $query->andWhere('o.discountType = ?2');
        }
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data['cnt'];
    }

    public static function getTotalAmountOfOffersByShopId($shopId)
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select("o.id")
        ->from('KC\Entity\Offer', 'o')
        ->where('o.deleted = 0')
        ->andWhere('o.shopOffers = '.$shopId)
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->setParameter(1, 'CD')
        ->andWhere('o.discountType = ?1');
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $Ids = array();
        if (!empty($data)) {
            foreach ($data as $arr) {
                
                $Ids[] = $arr;
            }
        }
        return $Ids;
    }

    public static function countFavShop($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('count(*) as total')
        ->from("FavoriteShop")
        ->where('shopId='.$shopId);
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if ($data[0]['total']>0) {
            return true;
        } else {
            return null;
        }
    }

    public static function addFavoriteShop($sid, $flag)
    {
        $userid = \Auth_VisitorAdapter::getIdentity()->id;
        if ($flag=='1' || $flag==1) {
            $fvshop = new KC\Entity\FavoriteShop();
            $fvshop->shopId = $sid;
            $fvshop->visitorId = $userid;
            $entityManagerUser->persist($fvshop);
            $entityManagerUser->flush();
            //call cache function
            $key = 'shopDetails_'  . $sid . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'offerDetails_'  . $sid . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularOffersHome_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
            return 1;
        } else {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\FavoriteShop', 'fs')
            ->where("fs.shopId=" . $sid)
            ->andWhere('fs.visitorId='.$userid)
            ->getQuery();
            $query->execute();
            $key = 'shopDetails_'  . $sid . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'offerDetails_'  . $sid . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularOffersHome_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
            return 2;
        }
    }

    public function saveOffer($params)
    {
        if (!isset($params['newsCheckbox']) && @$params['newsCheckbox'] != "news") {
                // check the offer type
            if (isset($params['defaultoffercheckbox'])) {     //offer type is default
                $this->Visability = 'DE';
                if ($params['selctedshop']!='') {

                    if (intval($params['selctedshop']) > 0) {
                        $this->shopId = $params['selctedshop'] ;
                    } else {
                        return array('result' => true , 'errType' => 'shop' );
                    }
                }
            } else {                                            // offer type member only
                $this->Visability = 'MEM';
                $this->shopId = null;
            }
        } else {

            if (intval($params['selctedshop']) > 0) {
                $this->shopId =  $params['selctedshop'] ;
            } else {
                return array('result' => true , 'errType' => 'shop' );
            }

        }

        // check the discountype
        if (isset($params['couponCodeCheckbox'])) {             // discount type coupon
            $this->discountType = 'CD';
            $this->couponCode = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['couponCode']);
            $this->discount = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                isset($params['discountamount']) ? $params['discountamount'] : 0
            );
            $this->discountvalueType =\BackEnd_Helper_viewHelper::stripSlashesFromString(
                isset($params['discountchk']) ? $params['discountchk'] : 0
            );
            if (isset($params['selectedcategories'])) {
                foreach ($params['selectedcategories'] as $categories) {
                    $this->refOfferCategory[]->categoryId = $categories;
                }
            }
        } elseif (isset($params['newsCheckbox'])) {       // discount type sale
            $this->discountType = 'NW';
        } elseif (isset($params['saleCheckbox'])) {       // discount type sale
            $this->discountType = 'SL';
        } else {                                                // discount type printable
            $this->discountType = 'PA';
            //check printable document
            if (isset($_FILES['uploadoffer']['name']) && $_FILES['uploadoffer']['name'] != '') {                          // upload offer

                $fileName = self::uploadFile($_FILES['uploadoffer']['name']);
                $ext =  BackEnd_Helper_viewHelper::getImageExtension($fileName);
                $pattern = '/^[0-9]{10}_(.+)/i' ;
                preg_match($pattern, $fileName, $matches);
                if (!$fileName) {
                    return false;
                }
                if (@$matches[1]) {
                    $this->logo->ext = $ext;
                    $this->logo->path ='images/upload/offer/';
                    $this->logo->name = $fileName;
                }
            } else {                                                   // add offer refUrl
                $this->refOfferUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerrefurlPR']);
            }
        }

        $this->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['addofferTitle']);

        if (isset($params['deepLinkStatus'])) {
            $this->refURL =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerRefUrl']);
            //$this->shop->deepLink = $params['offerRefUrl'];
        }

        if (trim($params['termsAndcondition'])!='') {
            $this->termandcondition[]->content =
                \BackEnd_Helper_viewHelper::stripSlashesFromString($params['termsAndcondition']);
        }

        if (!isset($params['newsCheckbox']) && @$params['newsCheckbox'] != "news") {
            $this->startDate = date('Y-m-d', strtotime($params['offerStartDate']))
                .' '.date(
                    'H:i',
                    strtotime($params['offerstartTime'])
                );
             $this->endDate = date('Y-m-d', strtotime($params['offerEndDate']))
                .' '.date(
                    'H:i',
                    strtotime($params['offerendTime'])
                );
        }

        if (isset($params['attachedpages'])) {
            foreach ($params['attachedpages'] as $pageId) {
                $this->refOfferPage[]->pageId = $pageId ;
            }
        }

        if (isset($params['extendedoffercheckbox'])) {                  // check if offer is extended
            $this->extendedOffer = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedoffercheckbox']);
            $this->extendedTitle =\BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferTitle']);
            $this->extendedUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferRefurl']);
            $this->extendedMetaDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferMetadesc']);
            $this->extendedFullDescription =\BackEnd_Helper_viewHelper::stripSlashesFromString($params['couponInfo']);
        } else {

            $this->extendedOffer = 0;
            $this->extendedTitle = '';
            $this->extendedUrl = '';
            $this->extendedMetaDescription = '';
            $this->extendedFullDescription = '';
        }

        $this->exclusiveCode=$this->editorPicks = 0;
        if (isset($params['exclusivecheckbox'])) {
            $this->exclusiveCode=1;
        }

        if (isset($params['editorpickcheckbox'])) {
            $this->editorPicks=1;
        }

        $this->maxlimit = 0;
        $this->maxcode = 0;
        if (isset($params['maxoffercheckbox'])) {
            $this->maxlimit='1';
            $this->maxcode = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['maxoffertxt']);
        }

        $connUser = \BackEnd_Helper_viewHelper::addConnection();

            $this->authorId = \Auth_StaffAdapter::getIdentity()->id;
            $this->authorName = \Auth_StaffAdapter::getIdentity()->firstName . " "
                . \Auth_StaffAdapter::getIdentity()->lastName;

        \BackEnd_Helper_viewHelper::closeConnection($connUser);
        $connSite = \BackEnd_Helper_viewHelper::addConnectionSite();
        if (intval($params['offerImageSelect']) > 0) {
            $this->tilesId = $params['offerImageSelect'];
        }

        // New code starts add blal

        if (isset($params['memberonlycheckbox']) && isset($params['existingShopCheckbox'])) {

            if (intval($params['selctedshop']) > 0) {
                $this->shopId = $params['selctedshop'] ;

            } else {
                return array('result' => true , 'errType' => 'shop' );
            }

        }

        if (isset($params['fromWhichShop']) && $params['fromWhichShop']== 0) {
            $this->shopExist = 0;
        } else {
            $this->shopExist = 1;
        }

        if (isset($params['memberonlycheckbox']) && isset($params['notExistingShopCheckbox'])) {
            $saveNewShop = new KC\Entity\Shop();
            $saveNewShop->name = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newShop']);
            $saveNewShop->permaLink = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newShop']);
            $saveNewShop->status = 1;

            if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {

                $fileName = self::uploadShopLogo('logoFile');
                $saveNewShop->logo->ext =   \BackEnd_Helper_viewHelper::stripSlashesFromString(
                    \BackEnd_Helper_viewHelper::getImageExtension($fileName)
                );
                $saveNewShop->logo->path = 'images/upload/shop/';
                $saveNewShop->logo->name = $fileName;
            } else {
                return false;
            }
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $saveNewShop->logoId = $saveNewShop->logo->id;
            $queryBuilder->persist($saveNewShop);
            $queryBuilder->flush();
            $this->shopId = $saveNewShop->id;

        }      // New code Ends

        try {

            if (intval($this->shopId) > 0) {
                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $queryBuilder->persist($this);
                $queryBuilder->flush();
            } else {
                return array('result' => true , 'errType' => 'shop' );
            }

            /***************** Start Add news code ********************/
            $lId = $this->id;
            if (isset($params['newsCheckbox']) && @$params['newsCheckbox'] == "news") {
                $newstitleloop = @$params['newsTitle'];
                for ($n=0; $n<count($newstitleloop); $n++) {
                    $savenews = new KC\Entity\OfferNews();
                    $savenews->shopId = @$params['selctedshop'];
                    $savenews->offerId = @$lId;
                    $savenews->title = @$newstitleloop[$n] != "" ?
                                            \BackEnd_Helper_viewHelper::stripSlashesFromString($newstitleloop[$n]) : "";

                    $savenews->url = @$params['newsrefUrl'][$n] != "" ?
                                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsrefUrl'][$n]) : "";

                    $savenews->content = @$params['newsDescription'][$n] != "" ?
                            \BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsDescription'][$n]) : "";

                    $savenews->linkstatus = @$params['newsdeepLinkStatus'][$n];
                    $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                    $queryBuilder->persist($savenews);
                    $queryBuilder->flush();
                }
            }
            /***************** End Add news code ********************/
            $offer_id = $this->id;
            $authorId = self::getAuthorId($offer_id);

            $uid = $authorId[0]['authorId'];
            $popularcodekey ="all_". "popularcode".$uid ."_list";
            $newcodekey ="all_". "newestcode".$uid ."_list";
            //call cache function
            $key = '6_topOffers'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'shop_latestUpdates'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'shop_expiredOffers'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
                    
            $key = 'offer_'.$this->id.'_details';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'extendedTopOffer_of_'.intval($params['selctedshop']);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'extended_'.
                \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($params['extendedOfferRefurl']).
                '_couponDetails';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage_offers');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeTopCategoriesOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($newcodekey);
            return array('result' => true , 'ofer_id' => $this->id );
            $key = 'all_widget5_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'all_widget6_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateOffer($params)
    {
        //echo "<pre>"; print_r($params); die;
        if (!isset($params['newsCheckbox']) && @$params['newsCheckbox'] != "news") {
                // check the offer type
            if (isset($params['defaultoffercheckbox'])) {      //offer type is default
                $this->Visability = 'DE';
                if ($params['selctedshop']!='') {
                    $this->shopId =  $params['selctedshop'] ;
                }
            } else {                                            // offer type member only
                $this->Visability = 'MEM';
                $this->shopId = null;
            }
        } else {
            $this->shopId =  $params['selctedshop'] ;
        }
        // check the discountype

        if (intval($params['offerImageSelect']) > 0) {

            $this->tilesId =  $params['offerImageSelect'] ;

        }

        $this->couponCodeType = $params['couponCodeType'];

        if (isset($params['couponCodeCheckbox'])) {             // discount type coupon

            $this->discountType = 'CD';
            $this->couponCode = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['couponCode']);
            $this->discount = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['discountamount']);
            $this->discountvalueType = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                isset($params['discountchk']) ? $params['discountchk'] : 0
            );
            $this->refOfferCategory->delete();
            if (isset($params['selectedcategories'])) {
                foreach ($params['selectedcategories'] as $categories) {

                    $this->refOfferCategory[]->categoryId = $categories ;

                }
            }

        } elseif (isset($params['newsCheckbox'])) {       // discount type sale
            $this->discountType = 'NW';
        } elseif (isset($params['saleCheckbox'])) {       // discount type sale
            $this->discountType = 'SL';

            //find if the offers exist in popular codes
            $exist = Doctrine_Core::getTable('PopularCode')->findOneByofferId($params['offerId']);

            if ($exist) {
                KC\Entity\PopularCode::deletePopular($params['offerId'], $exist->position);
            }

        } else {                                                // discount type printable
            $this->discountType = 'PA';

            //$this->tilesId = null ;

            //find if the offers exist in popular codes
            $exist = Doctrine_Core::getTable('PopularCode')->findOneByofferId($params['offerId']);

            if ($exist) {
                KC\Entity\PopularCode::deletePopular($params['offerId'], $exist->position);
            }

            //check printable document
            if (isset($_FILES['uploadoffer']['name']) && $_FILES['uploadoffer']['name'] != '') {  // upload offer

                $this->refOfferUrl = '';
                $fileName = self::uploadFile($_FILES['uploadoffer']['name']);
                $ext =  \BackEnd_Helper_viewHelper::stripSlashesFromString(
                    \BackEnd_Helper_viewHelper::getImageExtension($fileName)
                );
                $pattern = '/^[0-9]{10}_(.+)/i' ;
                preg_match($pattern, $fileName, $matches);
                if (!$fileName) {
                    return false;
                }
                if (@$matches[1]) {
                    $this->logo->ext = $ext;
                    $this->logo->path ='images/upload/offer/';
                    $this->logo->name = $fileName;
                }

            } else {                                                     // add offer refUrl
                $this->refOfferUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerrefurlPR']);
            }
        }

        $this->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['addofferTitle']);

        if (isset($params['deepLinkStatus'])) {
            $this->refURL =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerRefUrl']);
        } else {
            $this->refURL =  '';
        }
        $this->termandcondition->delete();

        if (trim($params['termsAndcondition'])!='') {
            $this->termandcondition[]->content = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                $params['termsAndcondition']
            );
        }

        if (!isset($params['newsCheckbox']) && @$params['newsCheckbox'] != "news") {
            $this->startDate = date('Y-m-d', strtotime($params['offerStartDate']))
            .' '.date(
                'H:i',
                strtotime($params['offerstartTime'])
            );
            $this->endDate = date('Y-m-d', strtotime($params['offerEndDate']))
            .' '.date(
                'H:i',
                strtotime($params['offerendTime'])
            );


        }

        $this->refOfferPage->delete();
        if (isset($params['attachedpages'])) {
            foreach ($params['attachedpages'] as $pageId) {

                $this->refOfferPage[]->pageId = $pageId ;

            }
        }
        //&& isset($params['couponCodeCheckbox'])

        if (isset($params['extendedoffercheckbox'])) {
            //echo "<pre>";
            //print_r($params['couponInfo']);
            // check if offer is extended
            $this->extendedOffer = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedoffercheckbox']);
            $this->extendedTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferTitle']);
            $this->extendedUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferRefurl']);
            $this->extendedMetaDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                $params['extendedOfferMetadesc']
            );
            $this->extendedFullDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['couponInfo']);
        } else {

            $this->extendedOffer = 0;
            $this->extendedTitle = '';
            $this->extendedUrl = '';
            $this->extendedMetaDescription = '';
            $this->extendedFullDescription = '';
        }

        $this->exclusiveCode=$this->editorPicks=0;
        if (isset($params['exclusivecheckbox'])) {
            $this->exclusiveCode=1;
        }

        if (isset($params['editorpickcheckbox'])) {
            $this->editorPicks=1;
        }

        $this->maxlimit=$this->maxcode='0';

        if (isset($params['maxoffercheckbox'])) {
            $this->maxlimit='1';
            $this->maxcode=$params['maxoffertxt'];
        }

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('o')
            ->from('KC\Entity\Offer', 'o')
            ->where('o.id = '.$params['offerId']);
        $getcategory = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (!empty($getcategory)) {

            $extendedUrl = mysql_real_escape_string($getcategory[0]['extendedUrl']);
            $query = $queryBuilder
                ->select('rp')
                ->from('KC\Entity\RoutePermalink', 'rp')
                ->where('rp.permalink = "?"', $extendedUrl)
                ->andWhere('type = "EXTOFFER"');
            $getRouteLink = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if (empty($getRouteLink)) {
                $getRouteLink = 'empty';
            }
            //$updateRouteLink = Doctrine_Core::getTable('RoutePermalink')->findOneBy('permalink', $getcategory[0]['permaLink'] );
        } else {
            $updateRouteLink = new KC\Entity\RoutePermalink();
        }

        // New code starts add blal

        if (isset($params['memberonlycheckbox']) && isset($params['existingShopCheckbox'])) {
            //die("Hiiii");
            $this->shopId = @$params['selctedshop'];
        }
        if (isset($params['fromWhichShop']) && $params['fromWhichShop']== 0) {
            $this->shopExist = 0;
        } else {
            $this->shopExist = 1;
        }
        if (isset($params['memberonlycheckbox']) && isset($params['notExistingShopCheckbox'])) {
            $saveNewShop = new KC\Entity\Shop();
            $saveNewShop->name = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newShop']);
            $saveNewShop->permaLink = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newShop']);
            $saveNewShop->status = 1;

            if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {

                $fileName = self::uploadShopLogo('logoFile');
                $saveNewShop->logo->ext =   \BackEnd_Helper_viewHelper::stripSlashesFromString(
                    \BackEnd_Helper_viewHelper::getImageExtension($fileName)
                );
                $saveNewShop->logo->path = 'images/upload/shop/';
                $saveNewShop->logo->name = $fileName;
            } else {
                return false;
            }

            $saveNewShop->logoId = $saveNewShop->logo->id;
            $queryBuilder->persist($saveNewShop);
            $queryBuilder->flush();
            $this->shopId = $saveNewShop->id;

        }      // New code Ends

        try {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilder->persist($this);
            $queryBuilder->flush();
            $lId = $this->id;

            /*****************Add more news of offer******************/

            $offerId = @$params['offerId'];
            $query = $queryBuilder->delete('KC\Entity\OfferNews', 'n')
            ->where('offerId=' . $offerId)
            ->getQuery();
            $query->execute();
            if (isset($params['newsCheckbox']) && @$params['newsCheckbox'] == "news") {
                $newsloop = @$params['newsTitle'];
                for ($n=0; $n<count($newsloop); $n++) {

                    $savenews = new KC\Entity\OfferNews();
                    $savenews->shopId = @$params['selctedshop'];
                    $savenews->offerId = @$offerId;

                    $savenews->title = @$newsloop[$n] != "" ?
                             \BackEnd_Helper_viewHelper::stripSlashesFromString($newsloop[$n]) : "";

                    $savenews->url = @$params['newsrefUrl'][$n] != "" ?
                                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsrefUrl'][$n]) : "";

                    $savenews->content = @$params['newsDescription'][$n] != "" ?
                        \BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsDescription'][$n]) : "";
                    $savenews->linkstatus = @$params['newsdeepLinkStatus'][$n];
                    $queryBuilder->persist($savenews);
                    $queryBuilder->flush();
                }
            }

            /***********End update more news of offer shop*************/

            $offerID = $params['offerId'];
            $authorId = self::getAuthorId($offerID);

            $uid = $authorId[0]['authorId'];
            $popularcodekey ="all_". "popularcode".$uid ."_list";
            $newcodekey ="all_". "newestcode".$uid ."_list";
            //call cache function

            $key = '6_topOffers'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'shop_latestUpdates'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'shop_expiredOffers'  .intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);



            $key = 'extendedTopOffer_of_'.intval($params['selctedshop']);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'extended_'.
                \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($params['extendedOfferRefurl']).
                '_couponDetails';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'offer_'.$offerID.'_details';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage_offers');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');

            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeTopCategoriesOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');

        
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');

            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($newcodekey);

            $key = 'all_widget5_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'all_widget6_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            return array('result' => true);
        } catch (Exception $e) {
            return false;
        }
    }
}
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
        $expiredDate = new \DateTime();
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'o.id, o.title, o.visability, o.couponcode, o.refofferurl, o.enddate,
            o.extendedoffer, o.extendedUrl, s.id as shopId, s.affliateProgram'
        )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->setParameter(1, 0)
        ->where('o.deleted = ?1')
        ->setParameter(2, 0)
        ->andWhere('o.userGenerated = ?2')
        ->setParameter(3, $expiredDate)
        ->andWhere($entityManagerUser->expr()->lte('o.enddate', '?3'))
        ->setParameter(4, 'CD')
        ->andWhere('o.discounttype = ?4')
        ->setParameter(5, 0)
        ->andWhere('s.deleted = ?5')
        ->setParameter(6, 1)
        ->andWhere('s.status = ?6')
        ->orderBy('o.id', 'DESC');
        if ($shopId != '') {
            $query = $query->setParameter(7, 15);
            $query = $query->andWhere('s.id = ?7');
        }
        $query = $query->setMaxResults($limit);
        $expiredOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $expiredOffers;
    }
    public static function similarStoresAndSimilarCategoriesOffers($type, $limit, $shopId = 0)
    {
        $date = new \DateTime();
        $similarShopsOffers = self::getOffersBySimilarShops($date, $limit, $shopId);
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
                    's.id,s.permalink as permalink,s.name,s.deepLink,s.usergenratedcontent,s.deepLinkStatus,
                    o.refURL, o.refOfferUrl, s.refUrl,s.actualUrl,terms.content,o.id,o.title, o.Visability,
                    o.discountType, o.couponCode, o.refofferurl, o.startdate, o.enddate, o.exclusiveCode,
                    o.editorPicks,o.extendedoffer,o.extendedUrl,o.discount, o.authorId, o.authorName, o.shopid,
                    o.offerlogoid, o.userGenerated,o.couponCodeType, o.approved,o.discountvalueType,img.id, img.path,
                    img.name,fv.shopId,fv.visitorId,fv.id,vot.id,vot.vote'
                )
                ->from('KC\Entity\Offer', 'o')
                ->addSelect(
                    "(SELECT count(id) FROM KC\Entity\CouponCode WHERE offer = o.id and status=1) as totalAvailableCodes"
                )
                ->leftJoin('o.shopOffers s')
                ->leftJoin('s.visitors fv')
                ->leftJoin('o.offertermandcondition terms')
                ->leftJoin('o.votes vot')
                ->leftJoin('s.categoryshops c')
                ->leftJoin('s.shoplogo img')
                ->setParameter(1, 0)
                ->where('o.deleted = ?1')
                ->setParameter(2, 'MEM')
                ->andWhere('o.visability != ?2')
                ->setParameter(3, $entityManagerUser->expr()->literal($date))
                ->andWhere($entityManagerUser->expr()->lte('o.startdate', '?3'))
                ->andWhere($entityManagerUser->expr()->gt('o.enddate', '?3'))
                ->setParameter(4, 'CD')
                ->andWhere('o.discounttype = ?4')
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
                ->add('where', $entityManagerUser->expr()->in('o.shopOffers', '?11'))
                ->orderBy('o.startdate', 'DESC')
                ->setMaxResults($limit);
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
                's.id,s.permalink as permalink,s.name,s.deepLink,s.usergenratedcontent,s.deepLinkStatus, o.refURL,
                o.refOfferUrl, s.refUrl,s.actualUrl,terms.content,o.id,o.title, o.Visability, o.discountType,
                o.couponCodeType, o.couponCode, o.refofferurl, o.startdate, o.enddate, o.exclusiveCode, o.editorPicks,
                o.extendedoffer,o.extendedUrl,o.discount, o.authorId, o.authorName, o.shopid, o.offerlogoid,
                o.userGenerated, o.approved,o.discountvalueType,img.id, img.path, img.name,fv.shopId,fv.visitorId,
                fv.id,vot.id,vot.vote'
            )
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers s')
            ->leftJoin('s.visitors fv')
            ->leftJoin('o.offertermandcondition terms')
            ->leftJoin('o.votes vot')
            ->leftJoin('s.categoryshops c')
            ->leftJoin('s.shoplogo img')
            ->setParameter(1, 0)
            ->where('o.deleted = ?1')
            ->setParameter(5, 0)
            ->andWhere('s.deleted = ?5')
            ->setParameter(6, 1)
            ->andWhere('s.status = ?6')
            ->setParameter(10, 1)
            ->andWhere('s.affliateProgram = ?10')
            ->setParameter(4, 'CD')
            ->andWhere('o.discounttype = ?4')
            ->setParameter(2, 'MEM')
            ->andWhere('o.visability != ?2')
            ->setParameter(7, $entityManagerUser->expr()->literal($date))
            ->andWhere($entityManagerUser->expr()->gt('o.enddate', '?7'))
            ->andWhere($entityManagerUser->expr()->lte('o.startdate', '?7'))
            ->setParameter(8, 0)
            ->andWhere('o.userGenerated = ?8')
            ->setParameter(11, $commaSepratedCategroyIdValues)
            ->add('where', $entityManagerUser->expr()->in('c.categoryId', '?11'))
            ->setParameter(9, $shopId)
            ->andWhere('o.shopOffers != ?9')
            ->orderBy('o.startdate', 'DESC')
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
        $currentDate = new \DateTime();
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'p.id as PopularCodeId,o.id as offerId,sc.categoryId,o.couponCodeType,o.refURL,
            o.discountType,o.title,o.discountvalueType,o.visability,o.exclusiveCode,
            o.editorPicks,o.userGenerated,o.couponCode,o.extendedOffer,o.totalViewcount,
            o.startdate,o.enddate,o.refOfferUrl,
            o.extendedUrl,s.id as shopId,s.name,s.permalink as permalink,s.usergenratedcontent,s.deepLink,
            s.deepLinkStatus, s.refUrl,s.actualUrl,terms.content,img.id, img.path, img.name'
        )
        ->from('KC\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode o')
        ->leftJoin('o.shopOffers s')
        ->leftJoin('s.logo img')
        ->leftJoin('o.offertermandcondition terms')
        ->setParameter(1, 0)
        ->where('o.deleted = ?1')
        ->andWhere(
            "(couponCodeType = 'UN' AND (SELECT count(id)  FROM KC\Entity\CouponCode cc WHERE
            cc.offer = o.id and status=1)  > 0) or couponCodeType = 'GN'"
        )
        ->setParameter(3, 0)
        ->andWhere('s.deleted = ?3')
        ->setParameter(4, 0)
        ->andWhere('o.offline = ?4');

        if (!empty($shopCategories)) {
            $query = $query->leftJoin('s.categoryshops sc')
            ->setParameter(5, $shopCategories)
            ->add('where', $entityManagerUser->expr()->in('sc.shop', '?5'));
        }
        $query->setParameter(5, 1)
            ->andWhere('s.status = ?5')
            ->setParameter(6, $entityManagerUser->expr()->literal($currentDate))
            ->andWhere($entityManagerUser->expr()->gt('o.enddate', '?6'))
            ->andWhere($entityManagerUser->expr()->lte('o.startdate', '?6'))
            ->setParameter(7, 'CD')
            ->andWhere('o.discounttype = ?7')
            ->setParameter(8, 0)
            ->andWhere('o.userGenerated = ?8')
            ->setParameter(9, 'MEM')
            ->andWhere('o.visability != ?9')
            ->orderBy('p.position', 'ASC')
            ->setMaxResults($limit);
        $topCouponCodes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $topCouponCodes;
    }

    public static function getNewestOffers($type, $limit, $shopId = 0, $userId = "", $homeSection = '')
    {
        $currentDate = new \DateTime();
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            's.id,s.name,
            s.permaLink as permalink,s.permaLink,s.deepLink,s.deepLinkStatus,s.usergenratedcontent,s.refUrl,
            s.actualUrl,terms.content,
            o.id,o.visability,o.userGenerated,o.title,o.authorId,
            o.discountvalueType,o.exclusiveCode,o.extendedOffer,o.editorPicks,
            o.discount,o.couponCode,o.couponCodeType,o.refOfferUrl,o.refUrl,o.extendedUrl,
            o.discountType,o.startdate,o.enddate,
            img.id, img.path, img.name,fv.shopId, fv.visitorId,ologo.*,vot.id,vot.vote'
        )
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.logooffer ologo')
            ->leftJoin('o.votes vot')
            ->leftJoin('s.shoplogo img')
            ->leftJoin('s.visitors fv')
            ->leftJoin('o.offertermandcondition terms')
            ->setParameter(10, 0)
            ->where('o.deleted = ?10')
            ->andWhere(
                "(o.couponCodeType = 'UN' AND (SELECT count(id)  FROM KC\Entity\CouponCode c WHERE c.offer = o.id
                    and status=1)  > 0) or o.couponCodeType = 'GN'"
            )
            ->setParameter(8, 0)
            ->andWhere('s.deleted = ?8')
            ->setParameter(9, 1)
            ->andWhere('s.status = ?9')
            ->setParameter(1, $entityManagerUser->expr()->literal($currentDate))
            ->andWhere($entityManagerUser->expr()->gt('o.enddate', '?1'))
            ->andWhere($entityManagerUser->expr()->lte('o.startdate', '?1'))
            ->setParameter(2, 'CD')
            ->andWhere('o.discounttype = ?2')
            ->setParameter(3, 'NW')
            ->andWhere('o.discounttype != ?3')
            ->setParameter(4, 0)
            ->andWhere('o.userGenerated = ?4')
            ->setParameter(5, 'MEM')
            ->andWhere('o.visability != ?5')
            ->orderBy('o.startdate', 'DESC');
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
        $query = $querty->setMaxResults($limit);
        $newestCouponCodes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $newestCouponCodes;
    }

    public static function updateCache($offerId)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('o.id,s.id')
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->setParameter(1, $offerId)
            ->where('o.id = ?1');
        $offerDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $shopId = $offerDetails['shop']['id'];
        $key = 'shopDetails_'  . $shopId . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'offerDetails_'  . $shopId . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = '6_topOffers'  . $shopId . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shop_latestUpdates'  . $shopId . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shop_expiredOffers'  . $shopId . '_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allMoneySavingGuideLists');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allOfferList');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allNewOfferList');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allNewPopularCodeList');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('allHomeNewOfferList');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('extended_coupon_details');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage_offers');
    }

    public static function getSpecialPageOffers($specialPage)
    {
        $currentDate = new \DateTime();
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
            'op.offers,op.refoffers,o.couponCodeType,o.totalViewcount as clicks,o.title,o.refUrl,o.refOfferUrl,
            o.discountType,o.startdate,o.enddate,o.authorId,o.authorName,o.visability,o.couponCode,o.exclusiveCode,
            o.editorPicks,o.discount,o.discountvalueType,o.extendedOffer,o.extendedUrl,s.name,s.refUrl,
            s.actualUrl,s.permalink as permalink,s.views,l.*,fv.id,fv.visitorId,fv.shopId,vot.id,vot.vote, ologo.path,
            ologo.name'
        )
        ->from('KC\Entity\RefOfferPage', 'op')
        ->leftJoin('op.refoffers o')
        ->leftJoin('o.logooffer ologo')
        ->andWhere(
            "(couponCodeType = 'UN' AND (SELECT count(id) FROM KC\Entity\CouponCode cc WHERE cc.offer = o.id and status=1)  > 0)
            or couponCodeType = 'GN'"
        )
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.votes vot')
        ->leftJoin('s.shoplogo l')
        ->leftJoin('s.visitors fv')
        ->setParameter(7, $pageId)
        ->where('op.pageId = ?7')
        ->setParameter(1, $entityManagerUser->expr()->literal($currentDate))
        ->andWhere($entityManagerUser->expr()->gt('o.enddate', '?1'))
        ->andWhere($entityManagerUser->expr()->lte('o.startdate', '?1'))
        ->setParameter(2, 0)
        ->andWhere('o.deleted = ?2')
        ->setParameter(3, 0)
        ->andWhere('s.deleted = ?3')
        ->setParameter(4, 1)
        ->andWhere('s.status = ?4')
        ->setParameter(5, 'CD')
        ->andWhere('o.discounttype = ?5')
        ->setParameter(6, 'MEM')
        ->andWhere('o.visability != ?6')
        ->orderBy('o.exclusiveCode', 'DESC')
        ->addOrderBy('o.startdate', 'DESC');
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
            o.discountvalueType,s.name,s.refUrl, s.actualUrl,s.permalink as permalink,s.views,l.*,fv.id,
            fv.visitorId,fv.shopId,vot.id,vot.vote, ologo.path, ologo.name'
        )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.logooffer ologo')
        ->andWhere(
            "(couponCodeType = 'UN' AND (SELECT count(id) FROM KC\Entity\CouponCode cc WHERE cc.offer = o.id and status=1)  > 0)
            or couponCodeType = 'GN'"
        )
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.votes vot')
        ->leftJoin('s.shoplogo l')
        ->leftJoin('s.visitors fv')
        ->setParameter(1, $entityManagerUser->expr()->literal($currentDate))
        ->where($entityManagerUser->expr()->gt('o.enddate', '?1'))
        ->andWhere($entityManagerUser->expr()->lte('o.startdate', '?1'))
        ->setParameter(2, 0)
        ->andWhere('o.deleted = ?2')
        ->setParameter(3, 0)
        ->andWhere('s.deleted = ?3')
        ->setParameter(4, 0)
        ->andWhere('o.userGenerated = ?4')
        ->setParameter(5, 'MEM')
        ->andWhere('o.visability != ?5')
        ->setParameter(6, 'SL')
        ->andWhere('o.discountType != ?6')
        ->setParameter(7, 'PA')
        ->andWhere('o.discountType != ?7')
        ->orderBy('o.exclusiveCode', 'DESC')
        ->addOrderBy('o.startdate', 'DESC');
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
        $currentDate = new \DateTime();
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            's.id as shopId,o.id, o.title, o.visability, o.couponcode, o.refofferurl, o.enddate, o.extendedoffer,
            o.extendedUrl'
        )
        ->from('KC\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->setParameter(1, 0)
        ->where('o.deleted = ?1')
        ->setParameter(2, 0)
        ->andWhere('o.userGenerated = ?2')
        ->setParameter(3, $keyword.'%')
        ->andWhere($entityManagerUser->expr()->like('o.title, ?3'))
        ->setParameter(4, $entityManagerUser->expr()->literal($currentDate))
        ->andWhere($entityManagerUser->expr()->gt('o.enddate', '?4'))
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
            o.discount,o.userGenerated,o.couponCode,o.couponCodeType,o.refOfferUrl,o.refUrl,
            o.discountType,o.startdate,o.endDate,o.shopOffers as shopId'
        )
            ->from('KC\Entity\Offer', 'o')
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
            s.actualUrl,s.logoId'
        )
            ->from('KC\Entity\Shop s')
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
            ->from('KC\Entity\Logo l')
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
                's.id as shopId,s.name,s.refUrl,s.actualUrl,s.permaLink as permalink,terms.content,o.refURL,o.discountType,
                o.id,o.title,o.extendedUrl,o.visability,o.discountValueType, o.couponcode, o.refofferurl, o.startdate,
                o.enddate, o.exclusivecode, o.editorpicks,o.extendedoffer,o.discount, o.authorId, o.authorName,
                o.shopOffers, o.offerlogoid, o.userGenerated, o.approved,img.id, img.path, img.name,fv.shopId,fv.visitorId'
            )
            ->from('KC\Entity\Offer o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.shoplogo img')
            ->leftJoin('s.visitors fv')
            ->leftJoin('o.offertermandcondition terms')
            ->leftJoin('o.offerTiles t')
            ->setParameter(1, 0)
            ->where('o.deleted = ?1')
            ->setParameter(2, 0)
            ->andWhere('o.userGenerated = ?2')
            ->setParameter(3, 0)
            ->andWhere('o.offline = ?3')
            ->setParameter(4, 0)
            ->andWhere('s.deleted = ?4')
            ->setParameter(5, $entityManagerUser->expr()->literal($currentDate))
            ->andWhere($entityManagerUser->expr()->lte('o.startdate', '?5'))
            ->andWhere($entityManagerUser->expr()->gt('o.enddate', '?5'))
            ->setParameter(6, 'CD')
            ->andWhere('o.discounttype = ?6')
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
                's.id,s.name,s.refUrl,s.actualUrl,s.permaLink as permalink,terms.content,
                o.id,o.title,o.refURL,o.discountType,o.extendedUrl,o.visability,o.discountValueType, o.couponcode, 
                o.refofferurl, o.startdate,o.enddate, o.exclusivecode, o.editorpicks,o.extendedoffer,o.discount,
                o.authorId, o.authorName, o.shopid,o.offerlogoid, o.userGenerated, o.approved,img.id, img.path,
                img.name,fv.shopId,fv.visitorId,t.*'
            )
            ->from('KC\Entity\Offer o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.shoplogo img')
            ->leftJoin('s.visitors fv')
            ->leftJoin('o.offertermandcondition terms')
            ->leftJoin('o.offerTiles t')
            ->setParameter(1, 0)
            ->where('o.deleted = ?1')
            ->setParameter(2, 0)
            ->andWhere('o.offline = ?2')
            ->setParameter(3, 0)
            ->andWhere('s.deleted = ?3')
            ->setParameter(4, $entityManagerUser->expr()->literal($currentDate))
            ->andWhere($entityManagerUser->expr()->lte('o.startdate', '?4'))
            ->andWhere($entityManagerUser->expr()->gt('o.enddate', '?4'))
            ->setParameter(5, 'CD')
            ->andWhere('o.discounttype = ?5')
            ->setParameter(6, 'MEM')
            ->andWhere('o.Visability != ?6')
            ->setParameter(7, '%'.$searchKeyword.'%')
            ->andWhere(
                $query->expr()->orX(
                    $entityManagerUser->expr()->like('s.name, ?7'),
                    $entityManagerUser->expr()->like('o.title, ?7')
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
                o.discountType,o.Visability,o.extendedOffer,o.startDate,o.endDate,authorName,o.refURL,o.couponcode'
            )
            ->from('KC\Entity\Offer o')
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
            $getOffersQuery->setParameter(4, '%'.$searchOffer.'%');
            $getOffersQuery->andWhere($entityManagerUser->expr()->like('o.title, ?4'));
        }
        if ($searchShop!='') {
            $getOffersQuery->setParameter(5, '%'.$searchShop.'%');
            $getOffersQuery->andWhere($entityManagerUser->expr()->like('s.name, ?5'));
        }
        if ($searchCoupon!='') {
            $getOffersQuery->setParameter(6, '%'.$searchCoupon.'%');
            $getOffersQuery->andWhere($entityManagerUser->expr()->like('o.couponCode, ?5'));
        }
        if ($searchCouponType!='') {
            $getOffersQuery->setParameter(7, $entityManagerUser->expr()->literal($searchCouponType));
            $getOffersQuery->andWhere('o.discountType = ?7');
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
                        ->select('count(c.id) as exists,c.id')
                        ->from('KC\Entity\Conversions c')
                        ->setParameter(1, $offerId)
                        ->andWhere('c.offerId = ?1')
                        ->setParameter(2, $clientIP)
                        ->andWhere('c.IP = ?2')
                        ->setParameter(3, 0)
                        ->andWhere('c.converted = ?3')
                        ->groupBy('c.id');
                $offerData = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if (!$offerData['exists']) {
                $offerCount  = new KC\Entity\Conversions();
                $offerCount->offerId = $offerId;
                $offerCount->IP = $clientIP;
                $offerCount->utma = \Zend_Controller_Front::getInstance()->getRequest()->getCookie('__utma');
                $offerCount->utmz = \Zend_Controller_Front::getInstance()->getRequest()->getCookie('__utmz');
                $offerCount->subid = md5(time()*rand(1, 999));
                $entityManagerUser->persist($offerCount);
                $entityManagerUser->flush();
            } else {
                $query = $entityManagerUser
                            ->select('c')
                            ->from('KC\Entity\Conversions c')
                            ->setParameter(1, $offerData['id'])
                            ->andWhere('c.offerId = ?1');
                $offerCount = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if ($offerCount) {
                    $offerCount->utma = \Zend_Controller_Front::getInstance()->getRequest()->getCookie('__utma');
                    $offerCount->utmz = \Zend_Controller_Front::getInstance()->getRequest()->getCookie('__utmz');
                    $offerCount->subid = md5(time()*rand(1, 999));
                    $entityManagerUser->persist($offerCount);
                    $entityManagerUser->flush();
                }
            }
        }
    }

    public static function getCloakLink($offerId, $checkRefUrl = false)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
            ->select(
                's.permaLink as permalink, s.deepLink, s.deepLinkStatus, s.refUrl, s.actualUrl, o.refOfferUrl, o.refUrl'
            )
        ->from('KC\Entity\Offer o')
        ->leftJoin('o.shopOffers', 's')
        ->setParameter(1, $offerId)
        ->where('o.id = ?1');
        $shopData = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $network = Shop::getAffliateNetworkDetail($shopData['shop']['id']);
        if ($checkRefUrl) {
            # retur false if s shop is not associated with any network
            if (! isset($network['affliatenetwork'])) {
                return false;
            }

            if ($shopData['refURL'] != "") {
                return true ;

            } else if ($shopData['shop']['refUrl'] != "") {
                return true;
            } else {
                return true;
            }
        }

        $subid = "" ;
        if (isset($network['affliatenetwork'])) {
            if (!empty($network['subid'])) {
                 $subid = "&". $network['subid'];
                 $clientIP = \FrontEnd_Helper_viewHelper::getRealIpAddress();
                 $clientProperAddress = ip2long($clientIP);
                 # get click detail and replcae A2ASUBID click subid
                 $conversion = KC\Entity\Conversions::getConversionId($shopData['id'], $clientProperAddress, 'offer');
                 $subid = str_replace('A2ASUBID', $conversion['subid'], $subid);
            }
        }

        if ($shopData['refURL'] != "") {
            $url = $shopData['refURL'];
            $url .= $subid;

        } else if ($shopData['shop']['refUrl'] != "") {

            $url = $shopData['shop']['refUrl'];
            $url .=  $subid;

        } else if ($shopData['shop']['actualUrl'] != "") {
            $url = $shopData['shop']['actualUrl'];
        } else {
            $urll = $shopData['shop']['permalink'];
            $url = HTTP_PATH_LOCALE.$urll;
        }
        return $url;
    }

    public static function getOfferInfo($offerId)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
            ->select(
                'o.*,s.name,s.notes,s.accountManagerName,s.deepLink,s.refUrl,s.actualUrl,s.affliateNetworkId as aid,
                s.permaLink,a.name as affname,a.id as affiliateNetworkId,p.id as pageId,tc.*,cat.id,img.*'
            )
        ->from('KC\Entity\Offer o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.affliatenetwork a')
        ->leftJoin('o.offers p')
        ->leftJoin('o.offertermandcondition tc')
        ->leftJoin('o.categoryoffres cat')
        ->leftJoin('s.logo img')
        ->setParameter(1, $offerId)
        ->where('o.id = ?1');
        $OfferDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $OfferDetails;
    }

    public static function getAllOfferOnShop($id, $limit = null, $getExclusiveOnly = false, $includingOffline = false)
    {
        $nowDate = new \DateTime();
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
            ->select(
                'o.id,o.authorId,o.refURL,o.discountType,o.title,o.discountvalueType,o.Visability,o.exclusiveCode,
                o.editorPicks,o.userGenerated,o.couponCode,o.extendedOffer,o.totalViewcount,o.startDate,
                o.endDate,o.refOfferUrl,o.couponCodeType, o.extendedUrl,l.*,t.*,
                s.id as shopId,s.name,s.permalink as permalink,
                s.usergenratedcontent,s.deepLink,s.deepLinkStatus,s.refUrl,s.actualUrl,terms.content,img.id, img.path,
                img.name,vot.id,vot.vote'
            )
        ->from('KC\Entity\Offer o')
        ->addSelect(
            "(SELECT count(id)  FROM KC\Entity\CouponCode WHERE offer = o.id and status=1) as totalAvailableCodes"
        )
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.logo l')
        ->leftJoin('s.logo img')
        ->leftJoin('o.offertermandcondition terms')
        ->leftJoin('o.votes vot')
        ->leftJoin('o.offerTiles t')
        ->setParameter(1, 0)
        ->where('o.deleted = ?1');

        if (!$includingOffline) {
            $query = $query
                ->setParameter(2, 0)
                ->andWhere('o.offline = ?2')
                ->setParameter(3, $entityManagerUser->expr()->literal($nowDate))
                ->andWhere($entityManagerUser->expr()->lte('o.startdate', '?3'))
                ->andWhere($entityManagerUser->expr()->gt('o.endDate', '?3'));
        }
            
        $query = $query->andWhere(
            $entityManagerUser->expr()->orX(
                $entityManagerUser->expr()->andX('o.userGenerated = 0', 'o.approved = 0'),
                $entityManagerUser->expr()->andX('o.userGenerated=1', 'o.approved = 1')
            )
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
            ->addOrderBy('o.startdate', 'DESC')
            ->addOrderBy('o.popularityCount', 'DESC')
            ->addOrderBy('o.title', 'ASC');

        if ($getExclusiveOnly) {
            $query = $query->setParameter(9, 1)->andWhere('o.exclusiveCode = ?9');
        }

        if ($limit) {
            $query = $query->setMaxResults($limit);
        }

        $offers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offers;
    }
} 
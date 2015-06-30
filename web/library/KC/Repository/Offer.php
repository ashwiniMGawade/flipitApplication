<?php
namespace KC\Repository;
class Offer extends \Core\Domain\Entity\Offer
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

    public static function getViewCountByOfferId($offerId)
    {
        $dateTimeFormat = 'Y-m-j H:i:s';
        $currentDate = date($dateTimeFormat);
        $past24Hours = date($dateTimeFormat, strtotime('-1 day' . $currentDate));
        $past7Days = date($dateTimeFormat, strtotime('-7 day' . $currentDate));
        $past31Days = date($dateTimeFormat, strtotime('-31 day' . $currentDate));
        $offerViewCount = 0;
        $offerViewCount = \KC\Repository\ViewCount::getOfferViewCountBasedOnDate($offerId, $past24Hours, $currentDate, 'day');
        $offerViewCount = self::getViewCountByCondition($offerViewCount, $offerId, $past7Days, $currentDate, 'week');
        $offerViewCount = self::getViewCountByCondition($offerViewCount, $offerId, $past31Days, $currentDate, 'month');
        if (intval($offerViewCount['viewCount']) < 5) {
            $offerViewCount = '';
        }
        return $offerViewCount;
    }

    public static function getViewCountByCondition($offerViewCount, $offerId, $offsetDate, $currentDate, $offsetType)
    {
        if (intval($offerViewCount['viewCount']) < 5) {
            $offerViewCount = \KC\Repository\ViewCount::getOfferViewCountBasedOnDate($offerId, $offsetDate, $currentDate, $offsetType);
        }
        return $offerViewCount;
    }

    public static function offerExistOrNot($offerId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('o')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->setParameter(1, $offerId)
            ->where('o.id = ?1');
        $offers = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offers;
    }

    public static function checkUserGeneratedOffer($offerId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $offerDetail = $queryBuilder->select('o.userGenerated, o.approved')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->where('o.id ='.$offerId)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $userGenerated = false;
        if (isset($offerDetail[0]['userGenerated'])) {
            if ($offerDetail[0]['userGenerated'] == 1 && $offerDetail[0]['approved'] == 0) {
                $userGenerated = true;
            }
        }
        return $userGenerated;
    }

    public static function getOffersForNewsletter($offerIds)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                's.id,s.name,
                s.permaLink as permalink,s.permaLink,s.deepLink,s.deepLinkStatus,s.usergenratedcontent,s.refUrl,
                s.actualUrl,terms.content,
                o.id,o.Visability,o.userGenerated,o.title,o.authorId,
                o.discountvalueType,o.exclusiveCode,o.extendedOffer,o.editorPicks,o.authorName,
                o.discount,o.userGenerated,o.couponCode,o.couponCodeType,o.refOfferUrl,o.refUrl,o.extendedUrl,
                o.discountType,o.startdate,o.endDate,o.nickname,o.approved,
                img.id, img.path, img.name'
            )
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->andWhere($queryBuilder->expr()->in('o.id', $offerIds));
        $offers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $changedOrderOffers = self::changeOrder($offers);
        $changedOrderOffersSameAsTopOffers = self::getOfferWithOrder($changedOrderOffers, $offerIds);
        return $changedOrderOffersSameAsTopOffers;
    }

    public static function changeOrder($offers)
    {
        $changeOrder = '';
        foreach ($offers as $offer) {
            $changeOrder[$offer['id']] = $offer;
        }
        return $changeOrder;
    }

    public static function getOfferWithOrder($offers, $offerIds)
    {
        $offersWithOrder = '';
        foreach ($offerIds as $id) {
            $offersWithOrder[] = $offers[$id];
        }
        return $offersWithOrder;
    }
    
    public static function getExpiredOffers($type, $limit, $shopId = 0)
    {
        $expiredDate = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('o, s')
        ->from('\Core\Domain\Entity\Offer', 'o')
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
    
    public static function getOffersBySimilarShops($date, $limit, $shopId)
    {
        $similarShopsIds = self::getSimilarShopsIds($shopId);
        if (count($similarShopsIds) > 0) {
            $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerUser
                ->select('o, s, terms, c, img')
                ->from('\Core\Domain\Entity\Offer', 'o')
                ->addSelect(
                    "(SELECT count(cc.id) FROM \Core\Domain\Entity\CouponCode cc WHERE cc.offer = o.id and cc.status=1) as totalAvailableCodes"
                )
                ->leftJoin('o.shopOffers', 's')
                ->leftJoin('o.offertermandcondition', 'terms')
                ->leftJoin('s.categoryshops', 'c')
                ->leftJoin('s.logo', 'img')
                ->where('o.deleted = 0')
                ->andWhere('o.Visability != '.$entityManagerUser->expr()->literal('MEM'))
                ->andWhere('o.endDate >'."'".$date."'")
                ->andWhere('o.startDate <='."'".$date."'")
                ->andWhere('o.discountType ='.$entityManagerUser->expr()->literal('CD'))
                ->andWhere('s.deleted = 0')
                ->andWhere('s.status = 1')
                ->andWhere('o.userGenerated = 0')
                ->andWhere('o.shopOffers !='. $shopId)
                ->andWhere('s.affliateProgram = 1')
                ->andWhere($entityManagerUser->expr()->in('o.shopOffers', $similarShopsIds))
                ->orderBy('o.totalViewcount', 'DESC');
            if ($limit!='') {
                $query = $query->setMaxResults($limit);
            }
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
                'o, s, terms, c, img'
            )
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->leftJoin('s.categoryshops', 'c')
            ->leftJoin('s.logo', 'img')
            ->where('o.deleted = 0')
            ->andWhere('s.deleted = 0')
            ->andWhere('s.status = 1')
            ->andWhere('s.affliateProgram = 1')
            ->andWhere('o.Visability != '.$entityManagerUser->expr()->literal('MEM'))
            ->andWhere('o.endDate >'."'".$date."'")
            ->andWhere('o.startDate <='."'".$date."'")
            ->andWhere('o.discountType ='.$entityManagerUser->expr()->literal('CD'))
            ->andWhere('o.userGenerated = 0')
            ->andWhere($entityManagerUser->expr()->in('c.categoryId', $commaSepratedCategroyIdValues))
            ->andWhere('o.shopOffers !='. $shopId)
            ->orderBy('o.totalViewcount', 'DESC')
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
            ->from('\Core\Domain\Entity\RefShopCategory', 'r')
            ->setParameter(1, $shopId)
            ->where('r.category = ?1');
        $shopCategories = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopCategories;
    }

    public static function getRelatedShops($shopId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('r')
            ->from('\Core\Domain\Entity\RefShopRelatedshop', 'r')
            ->setParameter(1, $shopId)
            ->where('r.shop = ?1');
        $relatedShops = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $relatedShops;
    }

    public static function getTopOffers($limit)
    {
        $topCouponCodes = self::getTopCouponCodes(array(), $limit);
       
        if (count($topCouponCodes) < $limit) {
            $newestCodesLimit = $limit - count($topCouponCodes);
            $newestTopVouchercodes = self::getNewestOffers('newest', $newestCodesLimit);
            foreach ($newestTopVouchercodes as $value) {
                $topCouponCodes[] = array(
                    'id'=> $value['shopOffers']['id'],
                    'permaLink' => $value['shopOffers']['permaLink'],
                    'popularcode' => $value
                 );
            }
        }
        $topOffers = array();
        foreach ($topCouponCodes as $value) {
            $topOffers[] = $value['popularcode'];
        }
        return $topOffers;
    }

    public static function getTopCouponCodes($shopCategories, $limit = 5)
    {
        $currentDate = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            'p, o , s, img, terms'
        )
        ->from('\Core\Domain\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'img')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->where('o.deleted = 0')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM \Core\Domain\Entity\CouponCode cc WHERE
            cc.offer = o.id and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
        )
        ->andWhere('s.deleted = 0')
        ->andWhere('o.offline = 0');

        if (!empty($shopCategories)) {
            $query = $query->leftJoin('s.categoryshops', 'sc')
            ->andWhere($entityManagerUser->expr()->in('sc.shop', $shopCategories));
        }
        $topCouponCodes = $query
            ->andWhere('s.status = 1')
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'")
            ->andWhere("o.discountType = 'CD'")
            ->andWhere('o.userGenerated = 0')
            ->andWhere("o.Visability != 'MEM'")
            ->orderBy('p.position', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $topCouponCodes;
    }

    public static function getNewestOffers($type, $limit, $shopId = 0, $userId = "", $homeSection = '')
    {
        $currentDate = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            's, o, img, terms'
        )
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->setParameter(10, 0)
            ->where('o.deleted = ?10')
            ->andWhere(
                "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM \Core\Domain\Entity\CouponCode cc WHERE
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
            ->setParameter(5, 'MEM')
            ->andWhere('o.Visability != ?5')
            ->orderBy('o.startDate', 'DESC');
        if ($type == 'UserGeneratedOffers') {
            $query->andWhere('o.userGenerated=1 and o.approved="0"');
        } else {
            $query->andWhere('o.userGenerated=0');
        }
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
            ->from('\Core\Domain\Entity\Offer', 'o')
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
        $key = '4_shopLatestUpdates'  . $shopId . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shop_expiredOffers'  . $shopId . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'shop_topthreeexpiredoffers'  . $shopId . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $shophowtokey = '6_topOffersHowto'  . $shopId . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($shophowtokey);
        $key = 'shop_similarShopsAndSimilarCategoriesOffers'. $shopId . '_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('extended_coupon_details');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage_offers');
        self::clearSpecialPagesCache($offerId);
    }

    public static function getSpecialPageOffersByFallBack($specialPage, $pageType = '')
    {
        $pageRelatedOffersAndPageConstraintsOffers =
            \KC\Repository\SpecialPagesOffers::getSpecialPageOffersByPageIdForFrontEnd($specialPage['id'], $pageType);
        if (empty($pageRelatedOffersAndPageConstraintsOffers)) {
            $pageRelatedOffersAndPageConstraintsOffers = self::getSpecialPageOffers($specialPage);
            $pageRelatedOffersAndPageConstraintsOffers =
            self::getDataForOfferPhtml($pageRelatedOffersAndPageConstraintsOffers, $specialPage);
        }
        return $pageRelatedOffersAndPageConstraintsOffers;
    }

    public static function getSpecialPageOffers($specialPage)
    {
        $currentDate = date("Y-m-d H:i");
        $pageRelatedOffers = self::getSpecialOffersByPage($specialPage['id'], $currentDate);
        $constraintsRelatedOffers = self::getOffersByPageConstraints($specialPage, $currentDate);
        $pageRelatedOffersAndPageConstraintsOffers = array_merge($pageRelatedOffers, $constraintsRelatedOffers);
        return $pageRelatedOffersAndPageConstraintsOffers;
    }

    public static function getSpecialOffersByPage($pageId, $currentDate)
    {
        $specialPageOffers = self::getOffersByPageId($pageId, $currentDate);
        return self::removeDuplicateOffers($specialPageOffers);
    }

    public static function getOffersByPageId($pageId, $currentDate)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('op, o, p, s, l')
        ->from('\Core\Domain\Entity\RefOfferPage', 'op')
        ->leftJoin('op.refoffers', 'o')
        ->leftJoin('o.offers', 'p')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id) FROM \Core\Domain\Entity\CouponCode cc WHERE cc.offer = o.id and cc.status=1)  > 0)
            or o.couponCodeType = 'GN'"
        )
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'l')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->where($entityManagerUser->expr()->eq('op.offers', $entityManagerUser->expr()->literal($pageId)))
        ->andWhere($entityManagerUser->expr()->gt('o.endDate', $entityManagerUser->expr()->literal($currentDate)))
        ->andWhere($entityManagerUser->expr()->lte('o.startDate', $entityManagerUser->expr()->literal($currentDate)))
        ->andWhere('o.deleted = 0')
        ->andWhere('s.deleted = 0')
        ->andWhere('s.status = 1')
        ->andWhere($entityManagerUser->expr()->neq('o.Visability', $entityManagerUser->expr()->literal("MEM")))
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
                $specialOffersWithoutDuplication[$offerIndex] = $specialPageOffers[$offerIndex]['refoffers'];
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
            'o, s, l, ologo'
        )
        ->from('\Core\Domain\Entity\Offer', 'o')
        ->leftJoin('o.logo', 'ologo')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id) FROM \Core\Domain\Entity\CouponCode cc WHERE cc.offer = o.id 
                and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
        )
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'l')
        ->where($entityManagerUser->expr()->gt('o.endDate', $entityManagerUser->expr()->literal($currentDate)))
        ->andWhere($entityManagerUser->expr()->lte('o.startDate', $entityManagerUser->expr()->literal($currentDate)))
        ->andWhere('o.deleted = 0')
        ->andWhere('s.deleted = 0')
        ->andWhere('o.userGenerated = 0')
        ->andWhere($entityManagerUser->expr()->neq('o.Visability', $entityManagerUser->expr()->literal("MEM")))
        ->andWhere($entityManagerUser->expr()->neq('o.discountType', $entityManagerUser->expr()->literal("SL")))
        ->andWhere($entityManagerUser->expr()->neq('o.discountType', $entityManagerUser->expr()->literal("PA")))
        ->orderBy('o.exclusiveCode', 'DESC')
        ->addOrderBy('o.startDate', 'DESC');
        $offersConstraintsQuery = self::implementOffersConstraints($offersConstraintsQuery, $specialPage);
        $specialOffersByConstraints = $offersConstraintsQuery->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialOffersByConstraints;
    }

    public static function implementOffersConstraints($offersConstraintsQuery, $specialPage)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
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
            $offersConstraintsQuery->andWhere($entityManagerUser->expr()->like('o.title', $entityManagerUser->expr()->literal($wordTitle.'%')));
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
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $offersConstraintsQuery = $offersConstraintsQuery->andWhere(
            $entityManagerUser->expr()->neq('o.discountType', $entityManagerUser->expr()->literal('CD'))
        );
        return $offersConstraintsQuery ;
    }

    public static function yesExclusiveOrYesEditorPickConstraints($offersConstraintsQuery)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        return  $offersConstraintsQuery->andWhere(
            $entityManagerUser->expr()->orX('o.exclusiveCode = 1', 'o.editroPicks = 1')
        );
    }

    public static function noEditorPicksCodeConstraint($offersConstraintsQuery)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        return $offersConstraintsQuery->andWhere(
            $entityManagerUser->expr()->orX('o.editorPicks = 0', 'o.editorPicks is NULL')
        );
    }

    public static function notExclusiveCodeConstraint($offersConstraintsQuery)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        return $offersConstraintsQuery->andWhere(
            $entityManagerUser->expr()->orX('o.exclusiveCode = 0', 'o.exclusiveCode is NULL')
        );
    }

    public static function yesExclusiveCodeConstraint($offersConstraintsQuery)
    {
        $offersConstraintsQuery = $offersConstraintsQuery->andWhere('o.exclusiveCode = 1');
        return $offersConstraintsQuery;
    }

    public static function yesEditorPicksCodeConstraint($offersConstraintsQuery)
    {
        $offersConstraintsQuery = $offersConstraintsQuery->andWhere('o.editorPicks = 1');
        return $offersConstraintsQuery;
    }

    public static function yesCouponCodeConstraint($offersConstraintsQuery)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $offersConstraintsQuery = $offersConstraintsQuery->andWhere(
            $entityManagerUser->expr()->like('o.discountType', $entityManagerUser->expr()->literal('CD'))
        );
        return $offersConstraintsQuery;
    }

    public static function getFilteredOffersByConstraints($specialPage, $specialOffersByConstraints)
    {
        $offersAccordingToConstraints = array();
        if (count($specialOffersByConstraints) > 0) {
            $countOfSpecialOffersByConstraints = count($specialOffersByConstraints);
            for ($offerIndex = 0; $offerIndex < $countOfSpecialOffersByConstraints; $offerIndex++) {

                $offerPublishDate = $specialOffersByConstraints[$offerIndex]['startDate']->format('Y-m-d');
                $offerExpiredDate = $specialOffersByConstraints[$offerIndex]['endDate']->format('Y-m-d');
                $offerSubmissionDaysIncreasedBy = ' +'.$specialPage['timenumberOfDays'].' days';
                $offerSubmissionDaysDecreasedBy  = ' -'.$specialPage['timenumberOfDays'].' days';
                $increasedOfferPublishDate = date($offerPublishDate .$offerSubmissionDaysIncreasedBy);
                $decreasedOfferExpiredDate = date($offerExpiredDate .$offerSubmissionDaysDecreasedBy);
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
            o.refOfferUrl as refofferurl, o.endDate as enddate, o.extendedOffer as extendedoffer,o.extendedUrl,
            o.authorName, o.editorPicks'
        )
        ->from('\Core\Domain\Entity\Offer', 'o')
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
            o.discountType,o.startDate,o.endDate, s.id as shopId'
        )
            ->from('\Core\Domain\Entity\Offer', 'o')
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
        if (!empty($offerDetails)) {
            $shopDetails = self::getShopDetailFromOffer($offerDetails[0]['shopId']);
            $logoDetails = self::getShopLogo($shopDetails[0]['logoId']);
            $splashPagePopularCoupon = array_merge($offerDetails, $shopDetails, $logoDetails);
            return $splashPagePopularCoupon;
        } else {
            return '';
        }
    }

    public static function getShopDetailFromOffer($shopId)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            's.id,s.name,
            s.permaLink as permalink,s.permaLink,s.deepLink,s.deepLinkStatus,s.usergenratedcontent,s.refUrl,
            s.actualUrl, logo.id as logoId'
        )
            ->from('\Core\Domain\Entity\Shop', 's')
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
            ->from('\Core\Domain\Entity\Logo', 'l')
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

        $currentDate = date("Y-m-d H:i");
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
                's, terms, o, img'
            )
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->leftJoin('o.offerTiles', 't')
            ->where('o.deleted = 0')
            ->andWhere('o.userGenerated = 0')
            ->andWhere('o.offline = 0')
            ->andWhere('s.deleted = 0')
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'")
            ->andWhere($entityManagerUser->expr()->eq('o.discountType', $entityManagerUser->expr()->literal('CD')))
            ->andWhere($entityManagerUser->expr()->neq('o.Visability', $entityManagerUser->expr()->literal('MEM')))
            ->andWhere($entityManagerUser->expr()->in('s.id', $shopIds))
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
                's, terms, o, img, t'
            )
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->leftJoin('o.offerTiles', 't')
            ->where('o.deleted = 0')
            ->andWhere('o.offline = 0')
            ->andWhere('s.deleted = 0')
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'")
            ->andWhere($entityManagerUser->expr()->eq('o.discountType', $entityManagerUser->expr()->literal('CD')))
            ->andWhere($entityManagerUser->expr()->neq('o.Visability', $entityManagerUser->expr()->literal('MEM')))
            ->andWhere($entityManagerUser->expr()->orX(
                $entityManagerUser->expr()->like("s.name", $entityManagerUser->expr()->literal("%".mysqli_real_escape_string(\FrontEnd_Helper_viewHelper::getDbConnectionDetails(), $searchKeyword)."%")),
                $entityManagerUser->expr()->like("o.title", $entityManagerUser->expr()->literal("%".mysqli_real_escape_string(\FrontEnd_Helper_viewHelper::getDbConnectionDetails(), $searchKeyword)."%"))
            ))
            ->orderBy('s.name');
        $shopOffersBySearchedKeywords = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopOffersBySearchedKeywords;
    }

    public static function getOfferList($parameters)
    {
        $userRole           = \Auth_StaffAdapter::getIdentity()->users->id;
        $searchOffer        = $parameters["offerText"]!='undefined' ? $parameters["offerText"] : '';
        $searchShop         = $parameters["shopText"]!='undefined' ? $parameters["shopText"] : '';
        $searchCoupon       = isset($parameters["shopCoupon"]) && $parameters["shopCoupon"]!='undefined' ? $parameters["shopCoupon"] : '';
        $searchCouponType   = $parameters["couponType"]!='undefined' ? $parameters["couponType"] : '';
        $deletedStatus      = $parameters['flag'];
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $getOffersQuery = $entityManagerUser
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where($entityManagerUser->expr()->eq('o.deleted', $entityManagerUser->expr()->literal($deletedStatus)))
            ->andWhere("(o.userGenerated=0 and o.approved='0') or (o.userGenerated=1 and o.approved='1')");
        if ($userRole=='4') {
            $getOffersQuery->andWhere(
                $entityManagerUser->expr()->like('o.Visability', $entityManagerUser->expr()->literal('DE'))
            );
        }
        if ($searchOffer != '') {
            $getOffersQuery->andWhere(
                $entityManagerUser->expr()->like('o.title', $entityManagerUser->expr()->literal('%'.$searchOffer.'%'))
            );
        }
        if ($searchShop!='') {
            $getOffersQuery->andWhere(
                $entityManagerUser->expr()->like('s.name', $entityManagerUser->expr()->literal('%'.$searchShop.'%'))
            );
        }
        if ($searchCoupon!='') {
            $getOffersQuery->andWhere(
                $entityManagerUser->expr()->like('o.couponCode', $entityManagerUser->expr()->literal('%'.$searchCoupon.'%'))
            );
        }
        if ($searchCouponType!='') {
            if ($searchCouponType != 'EX') {
                $getOffersQuery->andWhere(
                    $entityManagerUser->expr()->eq('o.discountType', $entityManagerUser->expr()->literal($searchCouponType))
                );
            } else {
                $getOffersQuery->andWhere($entityManagerUser->expr()->eq('o.extendedOffer', 1));
            }
        }

        //with new change we willd delete above code after completons of datatatable
        $request  = \DataTable_Helper::createSearchRequest(
            $parameters,
            array('o.title','s.name','o.discountType','o.refURL','o.couponcode','o.startDate',
                'o.endDate', 'o.totalViewcount','o.authorName'
            )
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($getOffersQuery)
            ->add('text', 'o.title')
            ->add('text', 's.name')
            ->add('text', 'o.discountType')
            ->add('text', 'o.refURL')
            ->add('text', 'o.couponCode')
            ->add('number', 'o.startDate')
            ->add('number', 'o.endDate')
            ->add('number', 'o.totalViewcount')
            ->add('text', 'o.authorName');
        $offersList = $builder->getTable()->getResponseArray();
        return $offersList;
    }

    public static function getTrashedOfferList($parameters)
    {
        $userRole           = \Auth_StaffAdapter::getIdentity()->users->id;
        $searchOffer        = $parameters["offerText"]!='undefined' ? $parameters["offerText"] : '';
        $searchShop         = $parameters["shopText"]!='undefined' ? $parameters["shopText"] : '';
        $searchCoupon       = isset($parameters["shopCoupon"]) && $parameters["shopCoupon"]!='undefined' ? $parameters["shopCoupon"] : '';
        $searchCouponType   = $parameters["couponType"]!='undefined' ? $parameters["couponType"] : '';
        $deletedStatus      = $parameters['flag'];
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $getOffersQuery = $entityManagerUser
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where($entityManagerUser->expr()->eq('o.deleted', $entityManagerUser->expr()->literal($deletedStatus)))
            ->andWhere($entityManagerUser->expr()->eq('o.userGenerated', $entityManagerUser->expr()->literal('0')))
            ->andWhere($entityManagerUser->expr()->eq('o.approved', $entityManagerUser->expr()->literal('0')));
        if ($userRole=='4') {
            $getOffersQuery->andWhere(
                $entityManagerUser->expr()->like('o.Visability', $entityManagerUser->expr()->literal('DE'))
            );
        }
        if ($searchOffer != '') {
            $getOffersQuery->andWhere(
                $entityManagerUser->expr()->like('o.title', $entityManagerUser->expr()->literal('%'.$searchOffer.'%'))
            );
        }
        if ($searchShop!='') {
            $getOffersQuery->andWhere(
                $entityManagerUser->expr()->like('s.name', $entityManagerUser->expr()->literal('%'.$searchShop.'%'))
            );
        }
        if ($searchCoupon!='') {
            $getOffersQuery->andWhere(
                $entityManagerUser->expr()->like('o.couponCode', $entityManagerUser->expr()->literal('%'.$searchCoupon.'%'))
            );
        }
        if ($searchCouponType!='') {
            if ($searchCouponType != 'EX') {
                $getOffersQuery->andWhere(
                    $entityManagerUser->expr()->eq('o.discountType', $entityManagerUser->expr()->literal($searchCouponType))
                );
            } else {
                $getOffersQuery->andWhere($entityManagerUser->expr()->eq('o.extendedOffer', 1));
            }
        }
        $request  = \DataTable_Helper::createSearchRequest(
            $parameters,
            array('o.title','s.name','o.discountType','o.refURL','o.extendedOffer','o.startDate','o.endDate'
            )
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($getOffersQuery)
            ->add('text', 'o.title')
            ->add('text', 's.name')
            ->add('text', 'o.discountType')
            ->add('text', 'o.refURL')
            ->add('text', 'o.extendedOffer')
            ->add('number', 'o.startDate')
            ->add('number', 'o.endDate');
        $offersList = $builder->getTable()->getResponseArray();
        return $offersList;
    }

    public static function getShopInfoByOfferId($offerId)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shopInfo = $entityManagerUser
            ->select(
                's,o'
            )
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.id = '.$offerId)
            ->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shopInfo;
    }

    public static function getOfferInfo($offerId)
    {
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
            ->select(
                'o,s,a.name as affname,a.id as affiliateNetworkId,p.id as pageId,tc.content,cat.id as categoryId,
                img.path as shopImagePath, img.name as shopImageName'
            )
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.affliatenetwork', 'a')
            ->leftJoin('o.offers', 'p')
            ->leftJoin('o.offertermandcondition', 'tc')
            ->leftJoin('o.categoryoffres', 'c')
            ->leftJoin('c.categories', 'cat')
            ->leftJoin('s.logo', 'img')
            ->setParameter(1, $offerId)
            ->where('o.id = ?1');
        $OfferDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $OfferDetails;
    }

    public static function getAllOfferOnShop(
        $id,
        $limit = null,
        $getExclusiveOnly = false,
        $includingOffline = false,
        $visibility = false,
        $expired = false
    ) {
        $nowDate = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
            ->select('o, s, img, vot, t, terms')
        ->from('\Core\Domain\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'img')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->leftJoin('o.votes', 'vot')
        ->leftJoin('o.offerTiles', 't')
        ->where('o.deleted = 0');
        
        if (!$includingOffline) {
            $query = $query
                ->andWhere('o.offline = 0')
                ->andWhere('o.endDate >='."'".$nowDate."'")
                ->andWhere('o.startDate <='."'".$nowDate."'");
        }
        
        $query->andWhere("(o.userGenerated=0 and o.approved='0') or (o.userGenerated=1 and o.approved='1')")
        ->andWhere('s.id ='. $id)
        ->andWhere('s.deleted = 0');

        if ($expired == true) {
            $query = $query
                ->andWhere('o.endDate <='."'".$nowDate."'")
                ->andWhere('o.discountType ='.$entityManagerUser->expr()->literal('CD'))
                ->addOrderBy('o.endDate', 'DESC');
        } else {
            $query = $query
                ->andWhere('o.discountType !='.$entityManagerUser->expr()->literal('NW'))
                ->orderBy('o.discountType', 'ASC')
                ->addOrderBy('o.totalViewcount', 'DESC');
        }

        if ($getExclusiveOnly) {
            $query = $query->andWhere('o.exclusiveCode = 1');
        }

        if ($visibility) {
            $query = $query->andWhere('o.Visability !='.$entityManagerUser->expr()->literal('MEM'));
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
                ->select('o, s, img, terms')
                ->from('\Core\Domain\Entity\Offer', 'o')
                ->leftJoin('o.shopOffers', 's')
                ->leftJoin('s.logo', 'img')
                ->leftJoin('o.offertermandcondition', 'terms')
                ->where('o.deleted = 0')
                ->andWhere(
                    "(o.couponCodeType = 'UN' AND (
                        SELECT count(c.id)  FROM \Core\Domain\Entity\CouponCode c WHERE c.offer = o.id and c.status=1)  > 0
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

    public static function clearSpecialPagesCache($offerID)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
            ->select('r, p')
            ->from('\Core\Domain\Entity\RefOfferPage', 'r')
            ->leftJoin('r.offers', 'p')
            ->where('r.refoffers = '.$offerID);
        $specialPagesCacheRefresh = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (!empty($specialPagesCacheRefresh)) {
            foreach ($specialPagesCacheRefresh as $specialPageCacheRefresh) {
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll(
                    'error_specialPage'.$specialPageCacheRefresh['offers']['id'].'_offers'
                );
            }
        }

        return true;
    }

    public static function getOfferVisiblity($offerId)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
            ->select('o.id')
            ->from("\Core\Domain\Entity\Offer", "o")
            ->where("o.Visability ='MEM'")
            ->andWhere("o.id =".$offerId);
        $offerVisiblity = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return !empty($offerVisiblity) ? true : false;
    }
    
    public static function getrelatedOffers($shopId)
    {
        $currentDate = date("Y-m-d H:i");
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser
            ->select('t,o,s,s.permaLink as permalink,tc,img.name,img.path,ologo')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.logo', 'ologo')
            ->leftJoin('o.offertermandcondition', 'tc')
            ->leftJoin('o.offerTiles', 't')
            ->leftJoin('s.logo', 'img')
            ->where('o.shopOffers = '.$shopId)
            ->andWhere('o.endDate <='."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'")
            ->andWhere('o.deleted = 0')
            ->setMaxResults(1);
        $relatedOffers = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $relatedOffers;
    }

    public static function checkOfferExpired($offerId)
    {
        $currentDateTime = date("Y-m-d H:i:s");
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerLocale
            ->select('o.id')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->where('o.deleted = 0')
            ->andWhere('o.enddate <'."'".$currentDateTime."'")
            ->andWhere('o.id ='.$offerId)
            ->andWhere('o.offline = 0')
            ->setMaxResults(1);
        $offerDetail = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return !empty($offerDetail) ? true : false;
    }
    ##################################################################################
    ################## END REFACTORED CODE ###########################################
    ##################################################################################

    public static function moveToTrash($id)
    {
        if ($id) {
            $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $entityManagerUser
            ->select('s.id as shopId')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.id = '.$id);
            $u = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $offer_id = $id;
            $authorId = self::getAuthorId($offer_id);

            $query = $entityManagerUser
            ->select('p')
            ->from('\Core\Domain\Entity\PopularCode', 'p')
            ->where('p.popularcode = '.$offer_id);
            $exist = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if ($exist) {
                \KC\Repository\PopularCode::deletePopular($offer_id, $exist['position']);
            }
        
            $uid = $authorId[0]['authorId'];
            $popularcodekey ="all_". "popularcode".$uid ."_list";
            $newcodekey ="all_". "newestcode".$uid ."_list";

            $key = '6_topOffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $shophowtokey = '6_topOffersHowto'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($shophowtokey);
            $key = '4_shopLatestUpdates'  .$u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_expiredOffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_topthreeexpiredoffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_similarShopsAndSimilarCategoriesOffers'. $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($newcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            self::clearSpecialPagesCache($id);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeTopCategoriesOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget5_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_widget6_list');
            $entityManagerUser->update('\Core\Domain\Entity\Offer', 'od')
            ->set('od.deleted', 1)
            ->where('od.id ='.$id)
            ->getQuery()->execute();
        } else {
            $id = null;
        }
     
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
        return $id;
    }

    public static function deleteOffer($id)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
            ->select('s.id as shopId, o.extendedUrl')
            ->from('\Core\Domain\Entity\Offer', 'o')
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
            $shophowtokey = '6_topOffersHowto'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($shophowtokey);
            $key = '4_shopLatestUpdates'  .$u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_expiredOffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_topthreeexpiredoffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'extendedTopOffer_of_'.$u['shopId'];
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_similarShopsAndSimilarCategoriesOffers'. $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            if (!empty($u['extendedUrl'])) {
                $key = 'extended_'.
                    \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($u['extendedUrl']).
                    '_couponDetails';
                \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            }
            $key = 'offer_'.$id.'_details';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($newcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage_offers');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            self::clearSpecialPagesCache($id);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeTopCategoriesOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('\Core\Domain\Entity\RefOfferCategory', 'w')
                    ->where("w.offers=" . $id)
                    ->getQuery();
            $query->execute();
            $query = $queryBuilder->delete('\Core\Domain\Entity\TermAndCondition', 't')
                    ->where("t.termandcondition=" . $id)
                    ->getQuery();
            $query->execute();
            $query = $queryBuilder->delete('\Core\Domain\Entity\PopularCode', 'pc')
                    ->where("pc.popularcode=" . $id)
                    ->getQuery();
            $query->execute();
            $query = $queryBuilder->delete('\Core\Domain\Entity\RefOfferPage', 'ro')
                    ->where("ro.refoffers=" . $id)
                    ->getQuery();
            $query->execute();
            $query = $queryBuilder->delete('\Core\Domain\Entity\ViewCount', 'v')
                    ->where("v.viewcount=" . $id)
                    ->getQuery();
            $query->execute();
            $query = $queryBuilder->delete('\Core\Domain\Entity\OfferNews', 'n')
                    ->where("n.offerId=" . $id)
                    ->getQuery();
            $query->execute();
            $key = 'all_widget5_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'all_widget6_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $query = $queryBuilder->delete('\Core\Domain\Entity\Offer', 'od')
                    ->where("od.id=" . $id)
                    ->getQuery();
            $query->execute();

        } else {
            $id = null;
        }
       
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
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
            ->from('\Core\Domain\Entity\Offer', 'o')
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

            $shophowtokey = '6_topOffersHowto'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($shophowtokey);
            $key = '4_shopLatestUpdates'  .$u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'shop_expiredOffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_topthreeexpiredoffers'  . $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_similarShopsAndSimilarCategoriesOffers'. $u['shopId'] . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($newcodekey);
            self::clearSpecialPagesCache($id);
            $key = 'all_widget5_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'all_widget6_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

           
            $query = $queryBuilder->update('\Core\Domain\Entity\Offer', 'od')
                ->set('od.deleted', 0)
                ->where('od.id='.$id)
                ->getQuery();
            $query->execute();
        } else {
            $id = null;
        }
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeTopCategoriesOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
        return $id;
    }

    public static function searchTopFiveOffer($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('o.title as title, o.id')
                ->from('\Core\Domain\Entity\Offer', 'o')
                ->where('o.deleted='.$flag)
                ->andWhere(
                    $queryBuilder->expr()->like('o.title', $queryBuilder->expr()->literal($keyword.'%'))
                )
                ->andWhere("(o.userGenerated=0 and o.approved='0') or (o.userGenerated=1 and o.approved='1')")
                ->orderBy('o.title', 'ASC')
                ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function searchToFiveShop($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('s.name, s.id')
                ->from('\Core\Domain\Entity\Shop', 's')
                ->where('s.deleted='.$flag)
                ->andWhere("s.name LIKE '$keyword%'")
                ->orderBy('s.id', 'ASC')
                ->groupBy('s.name')
                ->setMaxResults(5);
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function searchToFiveCoupon($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('o.couponCode, o.id')
                ->from('\Core\Domain\Entity\Offer', 'o')
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
        $pc = new \Core\Domain\Entity\Offer();
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
                ->from('\Core\Domain\Entity\Offer', 'o')
                ->where('o.id='.$offerId);
        $userId = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $userId;
    }

    public static function exportofferList()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
                ->select('o.title, o.id,s.name,s.deepLink as deeplink,term.content')
                ->from('\Core\Domain\Entity\Offer', 'o')
                ->leftJoin('o.shopOffers', 's')
                ->leftJoin('o.offertermandcondition', 'term')
                ->addSelect("(SELECT COUNT(v.id) FROM \Core\Domain\Entity\ViewCount v WHERE v.viewcount = o.id) as Count")
                ->where("o.deleted=0")
                ->andWhere("o.userGenerated=0")
                ->orderBy("o.id", "DESC");
        $offerList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offerList;
    }

    public static function getOfferDetail($offerId, $type = '')
    {
        $shopParameters = $type != '' ? ',s.refUrl,s.permalink' : '';
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select(
            'o.title, o.id, o.Visability, o.shopExist,o.discountType, o.offer_position,
            o.couponCode, o.extendedOffer, o.editorPicks, o.userGenerated, o.couponCodeType, s.name as shopName,
            s.notes,s.strictConfirmation,s.accountManagerName,a.name as affname,o.extendedTitle, o.extendedoffertitle,
            o.extendedMetaDescription,
            page.id as pageId,tc.content as termsAndconditionContent,category.id as categoryId,img.name as imageName,
            img.path,news.title as newsTitle,
            news.url, news.content as newsContent, o.tilesId as tilesId, t.path as offerTilesPath,t.name as offerTilesName,
            t.position,
            s.id as shopId, o.extendedFullDescription,o.discountvalueType, o.refOfferUrl, o.startDate, o.endDate,
            o.refURL,
            o.exclusiveCode, o.maxlimit, o.maxcode, o.extendedUrl'.$shopParameters
        )
        ->from('\Core\Domain\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.affliatenetwork', 'a')
        ->leftJoin('o.offers', 'p')
        ->leftJoin('p.offers', 'page')
        ->leftJoin('o.offertermandcondition', 'tc')
        ->leftJoin('o.categoryoffres', 'cat')
        ->leftJoin('cat.categories', 'category');
        if ($type != '') {
            $query = $query->leftJoin('s.logo', 'img');
        } else {
            $query = $query->leftJoin('o.logo', 'img');
        }
        $query = $query->leftJoin('s.offerNews', 'news')
        ->leftJoin('o.offerTiles', 't')
        ->addSelect("(SELECT count(cc.status) FROM \Core\Domain\Entity\CouponCode cc WHERE cc.offer = o.id and cc.status = 0) as used")
        ->addSelect("(SELECT count(ccc.status) FROM \Core\Domain\Entity\CouponCode ccc WHERE ccc.offer = o.id and ccc.status = 1) as available")
        ->andWhere("o.id =".$offerId);
        $offerDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offerDetails;
    }

    public function uploadFile($imgName)
    {
        $uploadPath = "images/upload/offer/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $user_path = ROOT_PATH . $uploadPath;
        $img = $imgName;
        
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
                $path = ROOT_PATH . $uploadPath . "thum_" . $orgName;
                \BackEnd_Helper_viewHelper::resizeImage(
                    $_FILES["uploadoffer"],
                    $orgName,
                    126,
                    90,
                    $path
                );
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
            $path = ROOT_PATH . $uploadPath . "thum_" . $orgName;
            \BackEnd_Helper_viewHelper::resizeImageForAjax(
                $_FILES["tileupload"],
                $orgName,
                126,
                90,
                $path
            );
            $path = ROOT_PATH . $uploadPath . "thum_large" . $orgName;
            \BackEnd_Helper_viewHelper::resizeImageForAjax(
                $_FILES["tileupload"],
                $orgName,
                132,
                95,
                $path
            );

            $path = ROOT_PATH . $uploadPath . "thum_small_" . $orgName;
            $thum_small = \BackEnd_Helper_viewHelper::resizeImageForAjax(
                $_FILES["tileupload"],
                $orgName,
                80,
                80,
                $path
            );

            $path = ROOT_PATH . $uploadPath . "thum_large_" . $orgName;
            $thum_small = \BackEnd_Helper_viewHelper::resizeImageForAjax(
                $_FILES["tileupload"],
                $orgName,
                127,
                127,
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

    public function uploadShopLogo($file)
    {
        
        $uploadPath = UPLOAD_IMG_PATH."shop/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $rootPath = ROOT_PATH.$uploadPath;
        $files = $adapter->getFileInfo($file);

        if (!file_exists($rootPath)) {
            mkdir($rootPath, 0776, true);
        }

        $adapter->setDestination($rootPath);
        $adapter->addValidator('Extension', false, 'jpg,png');
        $adapter->addValidator('Size', false, array('max' => '2MB'));
        $name = $adapter->getFileName($file, false);
        $newName = time() . "_" . $name;
        $cp = $rootPath . $newName;
        $path = ROOT_PATH . $uploadPath . "thum_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 135, 95, $path);
        $path = $uploadPath . "thum_medium_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 50, 50, $path);
        $path = $uploadPath . "thum_large_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 95, 95, $path);
        $path = $uploadPath . "thum_small_" . $newName;
        \BackEnd_Helper_viewHelper::resizeImage($files[$file], $newName, 24, 24, $path);
        $adapter
        ->addFilter(
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
            'p.id as popularCodeId,o.endDate as enddate,o.title,s.refUrl,s.actualUrl,s.permaLink as permalink, o.id,
            o.Visability,o.extendedUrl,o.couponCode as couponcode, o.exclusiveCode as exclusivecode,
            o.discount,o.discountvalueType,s.name as shopName,l.path,l.name,p.type,p.position'
        )
        ->from('\Core\Domain\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'l')
        ->where('o.deleted = 0')
        ->andWhere("o.userGenerated = 0")
        ->andWhere('s.deleted = 0')
        ->andWhere('o.offline = 0')
        ->andWhere('o.endDate >'."'".$date."'")
        ->andWhere('o.startDate <='."'".$date."'")
        ->setParameter(1, 'CD')
        ->andWhere('o.discountType = ?1')
        ->setParameter(2, 'MEM')
        ->andWhere('o.Visability != ?2')
        ->orderBy('p.position', 'ASC')
        ->setMaxResults(4);
        $data= $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        foreach ($data as $d) {
            $suggestion[] = $d;
        }
        return $suggestion;
    }

    public static function getCouponDetails($extendedUrl)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('tc, o, s, ologo, t, img')
           ->from('\Core\Domain\Entity\Offer', 'o')
           ->leftJoin('o.shopOffers', 's')
           ->leftJoin('o.logo', 'ologo')
           ->leftJoin('o.offerTiles', 't')
           ->leftJoin('o.offertermandcondition', 'tc')
           ->leftJoin('s.logo', 'img')
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
        ->from('\Core\Domain\Entity\PopularCode', 'po')
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
        $query = $queryBuilder->select('p, o, s, img, terms')
            ->from('\Core\Domain\Entity\PopularCode', 'p')
            ->leftJoin('p.popularcode', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.logo', 'ologo')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->where('o.deleted = 0')
            ->andWhere(
                "(o.couponCodeType = 'UN' AND (SELECT count(c.id)  FROM \Core\Domain\Entity\CouponCode c WHERE c.offer = o.id and c.status=1)  > 0) 
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
            ->andWhere('o.Visability != ?2');
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
            $newData[] = $res['popularcode']['shopOffers'];
        }
        return $newData;
    }

    public static function commongetMemberOnlyOffer($type, $limit)
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select(
            's.id as shopId,s.name,s.usergenratedcontent, s.permaLink as permalink,s.deepLink,
            s.deepLinkStatus,s.refUrl,s.actualUrl,terms.content,o.id,o.Visability,o.title,
            o.authorId,o.discountvalueType,o.exclusiveCode,o.discount,o.userGenerated,
            o.couponCode,o.couponCodeType,o.refOfferUrl,o.refURL as refUrl,o.discountType,o.endDate,
            img.path as shopImagePath, img.name as shopImageName'
        )
        ->from('\Core\Domain\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'img')
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
            ->from('\Core\Domain\Entity\Offer', 'o')
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
            ->from('\Core\Domain\Entity\PopularCode', 'p')
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
                ->from('\Core\Domain\Entity\Offer', 'o')
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
                    img.id, img.path, img.name'
                )
                ->from('\Core\Domain\Entity\Offer', 'o')
                ->leftJoin('o.shopOffers', 's')
                ->leftJoin('o.offertermandcondition', 'terms')
                ->leftJoin('s.logo', 'img')
                ->setParameter(1, $shopvalues)
                ->where($queryBuilder->expr()->in('o.shopOffers', '?1'))
                ->andWhere('o.deleted = 0')
                ->andWhere('s.deleted = 0')
                ->andWhere('o.endDate >'."'".$date."'")
                ->andWhere('o.startDate <='."'".$date."'")
                ->setParameter(1, 'CD')
                ->andWhere('o.discountType = ?1')
                ->andWhere('o.shopOffers !='.$shopId)
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
            ->from('\Core\Domain\Entity\RefShopRelatedshop', 'ref')
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
            ->from('\Core\Domain\Entity\OfferNews', 'n')
            ->andWhere('n.shop = ' . $shopId)
            ->orderBy('n.created_at', 'DESC')
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
        ->from('\Core\Domain\Entity\Offer', 'o')
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
                'p.id as popularCodeId,o.id,o.authorId,o.refURL,o.couponCodeType,o.discountType,o.title, o.discountvalueType, o.Visability, o.exclusiveCode,o.editorPicks,
                o.userGenerated,o.couponCode,o.extendedOffer, o.totalViewcount,
                o.startDate, o.endDate,o.refOfferUrl, o.extendedUrl,l.name as offerLogoName,l.path as offerLogoPath,t.path,t.name,t.position,t.label,
                s.id as shopId,s.name as shopName,s.permaLink as permalink,
                s.usergenratedcontent,s.deepLink,
                s.deepLinkStatus,s.refUrl,s.actualUrl,terms.content,img.path as shopImagePath, img.name as shopImageName'
            )
        ->from('\Core\Domain\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.categoryshops', 'sc')
        ->leftJoin('s.logo', 'img')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->leftJoin('o.offerTiles', 't')
        ->where('o.deleted = 0')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM \Core\Domain\Entity\CouponCode cc WHERE cc.offer = o.id and cc.status=1)  > 0)
             or o.couponCodeType = 'GN'"
        )
        ->setParameter(10, $queryBuilder->expr()->literal($shopCategories))
        ->andWhere($queryBuilder->expr()->notIn('sc.categoryId', '?10'))
        ->setParameter(11, $queryBuilder->expr()->literal($offerIDs))
        ->andWhere($queryBuilder->expr()->notIn('o.id', '?11'))
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
            'l.name as offerLogoName,l.path as offerLogoPath,t.path,t.name,t.position,s.id as shopId,s.name as shopName,s.permaLink as permalink,s.usergenratedcontent,s.deepLink,
            s.deepLinkStatus,s.refUrl,s.actualUrl,terms.content,o.id, o.title,img.path as shopImagePath, img.name as shopImageName'
        )
        ->from('\Core\Domain\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'img')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->leftJoin('o.offerTiles', 't')
        ->where('o.deleted = 0');
        if (!$includingOffline) {
            $query = $query->andWhere('o.offline = 0')
                ->andWhere('o.endDate >='."'".$nowDate."'")
                ->andWhere('o.startDate <='."'".$nowDate."'");
        }

        $query= $query->andWhere('o.userGenerated=0')
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
        if ($getExclusiveOnly) {
            $query = $query->andWhere('o.exclusiveCode = 1');
        }
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
                img.name,img.path'
            )
            ->from('\Core\Domain\Entity\PopularVouchercodes', 'po')
            ->leftJoin('po.offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->leftJoin('s.logo', 'img')
            ->Where('s.id='.$shopId)
            ->andWhere('o.deleted =0')
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
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->addSelect(
                "(SELECT  sum(v.onClick) as click FROM \Core\Domain\Entity\ViewCount v WHERE v.viewcount = o.id and v.counted=0)
                as clicks"
            );
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        foreach ($data as $value) {
            if ($value['clicks']) {
                    \KC\Repository\ViewCount::processViewCount($value['id']);
                    $newtotal = intval($value['clicks']) + intval($value['totalViewcount']) ;
                    $dStart = date("y-m-d h:i:s");
                    $dEnd  = $value['startDate'];
                    $dDiff = $dEnd->diff($dStart);
                    $diff = (int) $dDiff->days ;
                    $popularity = round($newtotal / ($diff > 0 ? $diff : 1 ), 4);
                    $query = $queryBuilder
                        ->update('\Core\Domain\Entity\Offer', 'o')
                        ->set('o.totalViewcount', $newtotal)
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
        ->from('\Core\Domain\Entity\PopularVouchercodes', 'po')
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
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select(
            'o.id,o.title,o.authorId,o.authorName,o.Visability,o.couponCode,o.exclusiveCode,o.editorPicks,o.discount, o.discountvalueType,s.name as shopName, s.id as shopId,s.views,l.name,l.path'
        )
        ->from('\Core\Domain\Entity\Offer', 'o')
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'l')
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
        ->from('\Core\Domain\Entity\Offer', 'o')
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
        ->from('\Core\Domain\Entity\Votes', 'v')
        ->where("v.offer=".$id)
        ->andWhere("v.deleted=0")
        ->setParameter(1, 'positive')
        ->andWhere('v.vote = ?1');
        $positiveVotes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $query = $queryBuilder
        ->select('count(v.id) as cnt')
        ->from('\Core\Domain\Entity\Votes', 'v')
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
            ->select('s.name,l.name as logoName, l.path,s.permaLink, s.id as shopId')
            ->from('\Core\Domain\Entity\Offer', 'o')
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
            ->from('\Core\Domain\Entity\Offer', 'o')
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
            ->select("o, s, sp, p, refPage, page, c")
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.categoryoffres', 'c')
            ->leftJoin('o.offers', 'refPage')
            ->leftJoin('refPage.offers', 'page')
            ->leftJoin('s.shopPage', 'sp')
            ->leftJoin('c.categories', 'p')
            ->where("o.id=".$id);
        $offer = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $urlsArray = array();
        if (isset($offer[0]['shopOffers'])) {
            $urlsArray[] = $offer[0]['shopOffers']['permaLink'];
            if (isset($offer[0]['shopOffers']['contentManagerId'])) {
                $redactie =  \KC\Repository\User::returnEditorUrl($offer[0]['shopOffers']['contentManagerId']);
                if (isset($redactie['permalink']) && strlen($redactie['permalink']) > 0) {
                    $urlsArray[] = $redactie['permalink'] ;
                }
            }
        }
        if (isset($offer[0]['extendedOffer'])) {
            if ($offer[0]['extendedUrl'] && strlen($offer[0]['extendedUrl']) > 0) {
                $urlsArray[] = \FrontEnd_Helper_viewHelper::__link('link_deals') .'/'. $offer[0]['extendedUrl'];
            }
        }

        if ($offer[0]['shopOffers']['howToUse']) {
            if (isset($offer[0]['shopOffers']['permaLink'])  && strlen($offer[0]['shopOffers']['permaLink']) > 0) {
                if (!empty($offer[0]['shopOffers']['howtoguideslug'])) {
                    $urlsArray[] = $offer[0]['shopOffers']['permaLink']. '/'. $offer[0]['shopOffers']['howtoguideslug'];
                } else {
                    $urlsArray[] = \FrontEnd_Helper_viewHelper::__link('link_how-to'). '/'. $offer[0]['shopOffers']['permaLink'];
                }
            }
        }

        $cetgoriesPage = \FrontEnd_Helper_viewHelper::__link('link_categorieen') .'/' ;
        foreach ($offer as $value) {
            if (isset($value['categoryoffres'][0]['categories']['permaLink']) && strlen($value['categoryoffres'][0]['categories']['permaLink']) > 0) {
                $urlsArray[] = $cetgoriesPage . $value['categoryoffres'][0]['categories']['permaLink'];
                $urlsArray[] = $cetgoriesPage . $value['categoryoffres'][0]['categories']['permaLink'] .'/2';
                $urlsArray[] = $cetgoriesPage . $value['categoryoffres'][0]['categories']['permaLink'] .'/3';
            }
        }

        foreach ($offer[0]['offers'] as $value) {
            if (isset($value['offers']['permalink']) && strlen($value['offers']['permalink']) > 0) {
                $urlsArray[] = $value['offers']['permalink'];
            }
        }
        $urlsArray['startDate'] = $offer[0]['startDate'];
        $urlsArray['endDate'] = $offer[0]['endDate'];
        return $urlsArray ;
    }

    public static function getAmountOffersCreatedLastWeek()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $past7Days = date($format, strtotime('-7 day' . $date));
        $nowDate = $date;

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select("count(o.id) as amountOffers")
        ->from('\Core\Domain\Entity\Offer', 'o')
        ->where('o.deleted = 0')
        ->setParameter(1, 'NW')
        ->andWhere('o.discountType != ?1')
        ->setParameter(2, $queryBuilder->expr()->literal($past7Days))
        ->setParameter(3, $queryBuilder->expr()->literal($date))
        ->andWhere('o.created_at BETWEEN ?2 AND ?3');
        $data = $query->getQuery()->getSql();
        return $data;
    }


    public static function getNumberOfOffersCreatedByShopId($shopId)
    {
        $dateFormat = 'Y-m-j H:i:s';
        $currentDate = date($dateFormat);
        $past7Days = date($dateFormat, strtotime('-7 day' . $currentDate));
        $past31Days = date($dateFormat, strtotime('-31 day' . $currentDate));
        $offersCreated = array();
        $offersInfo = self::getOffersForDateRange($shopId, $past7Days, $currentDate);
        $offers = self::validateOffersAmount($offersInfo, $shopId, $past31Days, $currentDate, "week");
        $offersCreated = !empty($offers)
            ? array(
                "offersInfo" => $offers["offersInfo"],
                "type" => $offers["type"]
            )
            : "";

        return $offersCreated;
    }

    public static function validateOffersAmount($offersInfo, $shopId, $past31Days, $currentDate, $type)
    {
        $offers = array(
            "offersInfo" => $offersInfo,
            "type" => $type
        );
        if (!empty($offersInfo) && $offersInfo['amountOffers'] < 2) {
            $offersPast31Days = self::getOffersForDateRange($shopId, $past31Days, $currentDate);
            $offersInfo = !empty($offersPast31Days) && $offersPast31Days['amountOffers'] < 2 ? "" : $offersPast31Days;
            $offers = array(
                "offersInfo" => $offersInfo,
                "type" => "month"
            );
        }
        return $offers;
    }

    public static function getOffersForDateRange($shopId, $offsetDate, $currentDate)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select("count(o.id) as amountOffers")
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.deleted = 0')
            ->andWhere('o.offline = 0')
            ->add(
                'where',
                $queryBuilder->expr()->between(
                    'o.created_at',
                    $queryBuilder->expr()->literal($offsetDate),
                    $queryBuilder->expr()->literal($currentDate)
                )
            )
            ->andWhere('s.id = '.$shopId)
            ->setParameter(0, 'NW')
            ->andWhere('o.discountType != ?0')
            ->setMaxResults(1);
        $offersInfo =  $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offersInfo;
    }


    public static function getTotalAmountOfOffers()
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select("count(o.id) as amountOffers")
        ->from('\Core\Domain\Entity\Offer', 'o')
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
        ->from('\Core\Domain\Entity\Offer', 'o')
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
        ->from('\Core\Domain\Entity\Offer', 'o')
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
        ->from("\Core\Domain\Entity\FavoriteShop")
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
        $entityManagerUser  = \Zend_Registry::get('emLocale');
        $userid = \Auth_VisitorAdapter::getIdentity()->id;
        if ($flag=='1' || $flag==1) {
            $fvshop = new \Core\Domain\Entity\FavoriteShop();
            $fvshop->shopId = $sid;
            $fvshop->visitorId = $userid;
            $entityManagerUser->persist($fvshop);
            $entityManagerUser->flush();
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
            $query = $queryBuilder->delete('\Core\Domain\Entity\FavoriteShop', 'fs')
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
        $saveOffer = new \Core\Domain\Entity\Offer();
        $entityManagerUser  = \Zend_Registry::get('emLocale');
        if (!isset($params['newsCheckbox']) && @$params['newsCheckbox'] != "news") {
            if (isset($params['defaultoffercheckbox'])) {
                $saveOffer->Visability = 'DE';
                if ($params['selctedshop']!='') {
                    if (intval($params['selctedshop']) > 0) {
                        $saveOffer->shopOffers = $entityManagerUser->find('\Core\Domain\Entity\Shop', $params['selctedshop']);
                    } else {
                        return array('result' => true , 'errType' => 'shop' );
                    }
                }
            } else {
                $saveOffer->Visability = 'MEM';
                $saveOffer->shopOffers = null;
            }
        } else {

            if (intval($params['selctedshop']) > 0) {
                $saveOffer->shopOffers =  $entityManagerUser->find('\Core\Domain\Entity\Shop', $params['selctedshop']);
            } else {
                return array('result' => true , 'errType' => 'shop' );
            }

        }

        if (isset($params['couponCodeCheckbox'])) {
            $saveOffer->discountType = 'CD';
            $saveOffer->couponCode = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['couponCode']);
            $saveOffer->discount = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                isset($params['discountamount']) ? $params['discountamount'] : 0
            );
            $saveOffer->discountvalueType =\BackEnd_Helper_viewHelper::stripSlashesFromString(
                isset($params['discountchk']) ? $params['discountchk'] : 0
            );
        } elseif (isset($params['newsCheckbox'])) {
            $saveOffer->discountType = 'NW';
        } elseif (isset($params['saleCheckbox'])) {
            $saveOffer->discountType = 'SL';
        } else {
            $saveOffer->discountType = 'PA';
            if (isset($_FILES['uploadoffer']['name']) && $_FILES['uploadoffer']['name'] != '') {                          // upload offer

                $fileName = self::uploadFile($_FILES['uploadoffer']['name']);
                $ext =  \BackEnd_Helper_viewHelper::getImageExtension($fileName);
                $pattern = '/^[0-9]{10}_(.+)/i' ;
                preg_match($pattern, $fileName, $matches);
                if (!$fileName) {
                    return false;
                }
                if (@$matches[1]) {

                    $offerImage  = new \Core\Domain\Entity\Image();
                    $offerImage->ext = $ext;
                    $offerImage->path ='images/upload/offer/';
                    $offerImage->name = $fileName;
                    $offerImage->type = 'LG';
                    $offerImage->deleted = 0;
                    $offerImage->created_at = new \DateTime('now');
                    $offerImage->updated_at = new \DateTime('now');
                    $entityManagerUser->persist($offerImage);
                    $entityManagerUser->flush();
                    $saveOffer->offerlogoid =  $offerImage->getId();
                }
            } else {
                $saveOffer->refOfferUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerrefurlPR']);
            }
        }

        $saveOffer->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['addofferTitle']);
        $saveOffer->offer_position = \FrontEnd_Helper_viewHelper::sanitize($params['offerPosition']);
        if (isset($params['deepLinkStatus'])) {
            $saveOffer->refURL =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerRefUrl']);
        
        }

        if (!isset($params['newsCheckbox']) && @$params['newsCheckbox'] != "news") {
            $startDate= date('Y-m-d', strtotime($params['offerStartDate']))
                .' '.date(
                    'H:i',
                    strtotime($params['offerstartTime'])
                );
            $endDate = date('Y-m-d', strtotime($params['offerEndDate']))
                .' '.date(
                    'H:i',
                    strtotime($params['offerendTime'])
                );
            $saveOffer->startDate = new \DateTime($startDate);
            $saveOffer->endDate = new \DateTime($endDate);
        }

        if (isset($params['extendedoffercheckbox'])) {                  // check if offer is extended
            $saveOffer->extendedOffer = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedoffercheckbox']);
            $saveOffer->extendedTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferTitle']);
            $saveOffer->extendedoffertitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedTitle']);
            $saveOffer->extendedUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferRefurl']);
            $saveOffer->extendedMetaDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferMetadesc']);
            $saveOffer->extendedFullDescription =\BackEnd_Helper_viewHelper::stripSlashesFromString($params['couponInfo']);
        } else {
            $saveOffer->extendedoffertitle = '';
            $saveOffer->extendedOffer = 0;
            $saveOffer->extendedTitle = '';
            $saveOffer->extendedUrl = '';
            $saveOffer->extendedMetaDescription = '';
            $saveOffer->extendedFullDescription = '';
        }

        $saveOffer->exclusiveCode=$saveOffer->editorPicks = 0;
        if (isset($params['exclusivecheckbox'])) {
            $saveOffer->exclusiveCode=1;
        }

        if (isset($params['editorpickcheckbox'])) {
            $saveOffer->editorPicks=1;
        }

        $saveOffer->maxlimit = 0;
        $saveOffer->maxcode = 0;
        if (isset($params['maxoffercheckbox'])) {
            $saveOffer->maxlimit='1';
            $saveOffer->maxcode = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['maxoffertxt']);
        }
        $saveOffer->deleted = 0;
        $saveOffer->created_at = new \DateTime('now');
        $saveOffer->updated_at = new \DateTime('now');
        $saveOffer->userGenerated = 0;
        $saveOffer->approved = true;
        $saveOffer->offline = 0;

        $saveOffer->authorId = \Auth_StaffAdapter::getIdentity()->id;
        $saveOffer->authorName = \Auth_StaffAdapter::getIdentity()->firstName . " "
            . \Auth_StaffAdapter::getIdentity()->lastName;
        
        if (intval($params['offerImageSelect']) > 0) {
            $saveOffer->tilesId = $params['offerImageSelect'];
        }

        if (isset($params['memberonlycheckbox']) && isset($params['existingShopCheckbox'])) {

            if (intval($params['selctedshop']) > 0) {
                $saveOffer->shopOffers = $entityManagerUser->find('\Core\Domain\Entity\Shop', $params['selctedshop']);
            } else {
                return array('result' => true , 'errType' => 'shop' );
            }
        }

        if (isset($params['fromWhichShop']) && $params['fromWhichShop']== 0) {
            $saveOffer->shopExist = 0;
        } else {
            $saveOffer->shopExist = 1;
        }

        if (isset($params['memberonlycheckbox']) && isset($params['notExistingShopCheckbox'])) {
            $saveNewShop = new \Core\Domain\Entity\Shop();
            $saveNewShop->name = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newShop']);
            $saveNewShop->permaLink = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newShop']);
            $saveNewShop->status = 1;
            $saveNewShop->howToUse = 0;
            $saveNewShop->screenshotId = 0;
            $saveNewShop->deleted = 0;
            $saveNewShop->created_at = new \DateTime('now');
            $saveNewShop->updated_at = new \DateTime('now');
            $saveNewShop->displayExtraProperties = 1;
            $saveNewShop->showSignupOption = 0;
            $saveNewShop->addtosearch = 0;
            $saveNewShop->showSimliarShops = 0;
            $saveNewShop->showChains = 0;
            $saveNewShop->strictConfirmation = 0;
            if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {

                $fileName = self::uploadShopLogo('logoFile');
                $shopImage  = new \Core\Domain\Entity\Image();
                $shopImage->ext =   \BackEnd_Helper_viewHelper::stripSlashesFromString(
                    \BackEnd_Helper_viewHelper::getImageExtension($fileName)
                );
                $shopImage->path = 'images/upload/shop/';
                $shopImage->name = $fileName;
                $shopImage->deleted = 0;
                $shopImage->type = "LG";
                $shopImage->created_at = new \DateTime('now');
                $shopImage->updated_at = new \DateTime('now');
                $entityManagerUser->persist($shopImage);
                $entityManagerUser->flush();
            } else {
                return false;
            }

            $saveNewShop->logo = $shopImage->getId();
            $entityManagerUser->persist($saveNewShop);
            $entityManagerUser->flush();
            $saveOffer->shopOffers = $entityManagerUser->find('\Core\Domain\Entity\Shop', $saveNewShop->__get('id'));

        }
        try {
                $entityManagerUser->persist($saveOffer);
                $entityManagerUser->flush();

            if (isset($params['selectedcategories'])) {
                foreach ($params['selectedcategories'] as $categories) {
                    $offerCategories  = new \Core\Domain\Entity\RefOfferCategory();
                    $offerCategories->created_at = new \DateTime('now');
                    $offerCategories->updated_at = new \DateTime('now');
                    $offerCategories->categories = $entityManagerUser->find('\Core\Domain\Entity\Category', $categories);
                    $offerCategories->offers = $entityManagerUser->find('\Core\Domain\Entity\Offer', $saveOffer->getId());
                    $entityManagerUser->persist($offerCategories);
                    $entityManagerUser->flush();
                }
            }


            if (isset($params['attachedpages'])) {
                foreach ($params['attachedpages'] as $pageId) {
                    $offerPage  = new \Core\Domain\Entity\RefOfferPage();
                    $offerPage->created_at = new \DateTime('now');
                    $offerPage->updated_at = new \DateTime('now');
                    $offerPage->offers = $entityManagerUser->find('\Core\Domain\Entity\Page', $pageId);
                    $offerPage->refoffers = $entityManagerUser->find('\Core\Domain\Entity\Offer', $saveOffer->getId());
                    $entityManagerUser->persist($offerPage);
                    $entityManagerUser->flush();
                }
            }

            if (trim($params['termsAndcondition'])!='') {
                $offerTerms  = new \Core\Domain\Entity\TermAndCondition();
                $offerTerms->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['termsAndcondition']);
                $offerTerms->deleted = 0;
                $offerTerms->termandcondition = $entityManagerUser->find('\Core\Domain\Entity\Offer', $saveOffer->getId());
                $offerTerms->created_at = new \DateTime('now');
                $offerTerms->updated_at = new \DateTime('now');
                $entityManagerUser->persist($offerTerms);
                $entityManagerUser->flush();
            }
            $lId = $saveOffer->getId();
            if (isset($params['newsCheckbox']) && @$params['newsCheckbox'] == "news") {
                $newstitleloop = @$params['newsTitle'];
                for ($n=0; $n<count($newstitleloop); $n++) {
                    $savenews = new \Core\Domain\Entity\OfferNews();
                    $savenews->shop = @$params['selctedshop'];
                    $savenews->offerId = @$lId;
                    $savenews->title = @$newstitleloop[$n] != "" ?
                                            \BackEnd_Helper_viewHelper::stripSlashesFromString($newstitleloop[$n]) : "";

                    $savenews->url = @$params['newsrefUrl'][$n] != "" ?
                                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsrefUrl'][$n]) : "";

                    $savenews->content = @$params['newsDescription'][$n] != "" ?
                            \BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsDescription'][$n]) : "";

                    $savenews->linkstatus = @$params['newsdeepLinkStatus'][$n];
                    $entityManagerUser->persist($savenews);
                    $entityManagerUser->flush();
                }
            }
            $offer_id = $saveOffer->getId();
            if (isset($params['codealertcheckbox']) && $params['codealertcheckbox'] == '1') {
                $codeAlertShopId = isset($params['selctedshop']) && $params['selctedshop'] != '' ?
                    $params['selctedshop'] : '';
                \KC\Repository\CodeAlertQueue::saveCodeAlertQueue($codeAlertShopId, $offer_id);
            }
            $authorId = self::getAuthorId($offer_id);

            $uid = $authorId[0]['authorId'];
            $popularcodekey ="all_". "popularcode".$uid ."_list";
            $newcodekey ="all_". "newestcode".$uid ."_list";

            $key = '6_topOffers'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $shophowtokey = '6_topOffersHowto'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($shophowtokey);
            $key = '4_shopLatestUpdates'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'shop_expiredOffers'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_topthreeexpiredoffers'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'offer_'.$offer_id.'_details';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'futurecode_'.intval($params['selctedshop']).'_shop';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'extendedTopOffer_of_'.intval($params['selctedshop']);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_similarShopsAndSimilarCategoriesOffers'. intval($params['selctedshop'])  . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'extended_'.
                \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($params['extendedOfferRefurl']).
                '_couponDetails';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'offersAdded_'.intval($params['selctedshop']).'_shop';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage_offers');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_newOffers_list');
            self::clearSpecialPagesCache($offer_id);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularCategories_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_homeTopCategoriesOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($popularcodekey);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($newcodekey);
            return array('result' => true , 'ofer_id' => $offer_id);
            $key = 'all_widget5_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'all_widget6_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateOffer($params)
    {//echo "<pre>";print_r($params);die;
        $entityManagerLocale  = \Zend_Registry::get('emLocale');
        $repo = $entityManagerLocale->getRepository('\Core\Domain\Entity\Offer');
        $updateOffer = $repo->find($params['offerId']);
       
        if (!isset($params['newsCheckbox']) && @$params['newsCheckbox'] != "news") {
            if (isset($params['defaultoffercheckbox'])) {
                $updateOffer->Visability = 'DE';
                if ($params['selctedshop']!='') {
                    $updateOffer->shopOffers =  $entityManagerLocale->find('\Core\Domain\Entity\Shop', $params['selctedshop']);
                }
            } else {
                $updateOffer->Visability = 'MEM';
                $updateOffer->shopOffers = null;
            }
        } else {
            $updateOffer->shopOffers =  $entityManagerLocale->find('\Core\Domain\Entity\Shop', $params['selctedshop']);
        }
        if (intval($params['offerImageSelect']) > 0) {
            $updateOffer->tilesId =  $params['offerImageSelect'] ;

        }
        $updateOffer->couponCodeType = $params['couponCodeType'];

        if (isset($params['couponCodeCheckbox'])) {
            $updateOffer->discountType = 'CD';
            $updateOffer->couponCode = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['couponCode']);
            $updateOffer->discount = isset($params['discountamount']) ? \BackEnd_Helper_viewHelper::stripSlashesFromString($params['discountamount']) : '';
            $updateOffer->discountvalueType = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                isset($params['discountchk']) ? $params['discountchk'] : 0
            );
        } elseif (isset($params['newsCheckbox'])) {
            $updateOffer->discountType = 'NW';
        } elseif (isset($params['saleCheckbox'])) {
            $updateOffer->discountType = 'SL';
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                    ->select('p.position')
                    ->from('\Core\Domain\Entity\PopularCode', 'p')
                    ->where('p.popularcode = '.$params['offerId']);
            $exist = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (!empty($exist)) {
                KC\Repository\PopularCode::deletePopular($params['offerId'], $exist['position']);
            }

        } else {
            $updateOffer->discountType = 'PA';
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                    ->select('p.position')
                    ->from('\Core\Domain\Entity\PopularCode', 'p')
                    ->where('p.popularcode = '.$params['offerId']);
            $exist = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if ($exist) {
                KC\Repository\PopularCode::deletePopular($params['offerId'], $exist['position']);
            }

            if (isset($_FILES['uploadoffer']['name']) && $_FILES['uploadoffer']['name'] != '') {
                $updateOffer->refOfferUrl = '';
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
                    $offerImage  = new \Core\Domain\Entity\Image();
                    $offerImage->ext = $ext;
                    $offerImage->path ='images/upload/offer/';
                    $offerImage->name = $fileName;
                    $offerImage->deleted = 0;
                    $offerImage->type = "LG";
                    $offerImage->created_at = new \DateTime('now');
                    $offerImage->updated_at = new \DateTime('now');
                    $entityManagerLocale->persist($offerImage);
                    $entityManagerLocale->flush();
                    $saveOffer->offerlogoid =  $offerImage->getId();
                }
            } else {
                $updateOffer->refOfferUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerrefurlPR']);
            }
        }
        $updateOffer->title = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['addofferTitle']);
        $updateOffer->offer_position = \FrontEnd_Helper_viewHelper::sanitize($params['offerPosition']);
        if (isset($params['deepLinkStatus'])) {
            $updateOffer->refURL =  \BackEnd_Helper_viewHelper::stripSlashesFromString($params['offerRefUrl']);
        } else {
            $updateOffer->refURL =  '';
        }
        
        if (!isset($params['newsCheckbox']) && @$params['newsCheckbox'] != "news") {
            $startDate = date('Y-m-d', strtotime($params['offerStartDate']))
            .' '.date(
                'H:i',
                strtotime($params['offerstartTime'])
            );
            $endDate = date('Y-m-d', strtotime($params['offerEndDate']))
            .' '.date(
                'H:i',
                strtotime($params['offerendTime'])
            );
            $updateOffer->startDate = new \DateTime($startDate);
            $updateOffer->endDate = new \DateTime($endDate);
        }

        if (isset($params['extendedoffercheckbox'])) {
            $updateOffer->extendedOffer = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedoffercheckbox']);
            $updateOffer->extendedTitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferTitle']);
            $updateOffer->extendedoffertitle = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedTitle']);
            $updateOffer->extendedUrl = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['extendedOfferRefurl']);
            $updateOffer->extendedMetaDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString(
                $params['extendedOfferMetadesc']
            );
            $updateOffer->extendedFullDescription = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['couponInfo']);
        } else {
            $updateOffer->extendedOffer = 0;
            $updateOffer->extendedTitle = '';
            $updateOffer->extendedUrl = '';
            $updateOffer->extendedMetaDescription = '';
            $updateOffer->extendedFullDescription = '';
        }

        $updateOffer->exclusiveCode=$updateOffer->editorPicks=0;
        if (isset($params['exclusivecheckbox'])) {
            $updateOffer->exclusiveCode=1;
        }

        if (isset($params['editorpickcheckbox'])) {
            $updateOffer->editorPicks=1;
        }

        $updateOffer->maxlimit=$updateOffer->maxcode='0';

        if (isset($params['maxoffercheckbox'])) {
            $updateOffer->maxlimit='1';
            $updateOffer->maxcode=$params['maxoffertxt'];
        }
        
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('o')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->where('o.id = '.$params['offerId']);
        $getcategory = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if (!empty($getcategory)) {
            $extendedUrl = mysqli_real_escape_string(
                \FrontEnd_Helper_viewHelper::getDbConnectionDetails(),
                \BackEnd_Helper_viewHelper::stripSlashesFromString(
                    $getcategory[0]['extendedUrl']
                )
            );
            $query = $queryBuilder
                ->select('rp')
                ->from('\Core\Domain\Entity\RoutePermalink', 'rp')
                ->setParameter(1, $queryBuilder->expr()->literal($extendedUrl))
                ->where('rp.permalink = ?1')
                ->setParameter(2, 'EXTOFFER')
                ->andWhere('rp.type = ?2');
            $getRouteLink = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if (empty($getRouteLink)) {
                $getRouteLink = 'empty';
            }
           
        } else {
            $updateRouteLink = new \Core\Domain\Entity\RoutePermalink();
        }

        if (isset($params['memberonlycheckbox']) && isset($params['existingShopCheckbox'])) {
            $updateOffer->shopOffers =  $entityManagerLocale->find('\Core\Domain\Entity\Shop', $params['selctedshop']);
        }
        if (isset($params['fromWhichShop']) && $params['fromWhichShop']== 0) {
            $updateOffer->shopExist = 0;
        } else {
            $updateOffer->shopExist = 1;
        }
        if (isset($params['memberonlycheckbox']) && isset($params['notExistingShopCheckbox'])) {
            $saveNewShop = new \Core\Domain\Entity\Shop();
            $saveNewShop->name = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newShop']);
            $saveNewShop->permaLink = @\BackEnd_Helper_viewHelper::stripSlashesFromString($params['newShop']);
            $saveNewShop->status = 1;
            $saveNewShop->status = 1;
            $saveNewShop->howToUse = 0;
            $saveNewShop->screenshotId = 0;
            $saveNewShop->deleted = 0;
            $saveNewShop->created_at = new \DateTime('now');
            $saveNewShop->updated_at = new \DateTime('now');
            $saveNewShop->displayExtraProperties = 1;
            $saveNewShop->showSignupOption = 0;
            $saveNewShop->addtosearch = 0;
            $saveNewShop->showSimliarShops = 0;
            $saveNewShop->showChains = 0;
            $saveNewShop->strictConfirmation = 0;

            if (isset($_FILES['logoFile']['name']) && $_FILES['logoFile']['name'] != '') {

                $fileName = self::uploadShopLogo('logoFile');
                $shopImage  = new \Core\Domain\Entity\Image();
                $shopImage->ext =   \BackEnd_Helper_viewHelper::stripSlashesFromString(
                    \BackEnd_Helper_viewHelper::getImageExtension($fileName)
                );
                $shopImage->path = 'images/upload/shop/';
                $shopImage->name = $fileName;
                $shopImage->deleted = 0;
                $shopImage->type = "LG";
                $shopImage->created_at = new \DateTime('now');
                $shopImage->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($shopImage);
                $entityManagerLocale->flush();
            } else {
                return false;
            }
            $saveNewShop->logoId = $shopImage->getId();
            $entityManagerLocale->persist($saveNewShop);
            $entityManagerLocale->flush();
            $updateOffer->shopOffers = $entityManagerLocale->find('\Core\Domain\Entity\Shop', $saveNewShop->__get('id'));

        }
        $updateOffer->deleted = 0;
        $updateOffer->created_at = $updateOffer->created_at;
        $updateOffer->updated_at = new \DateTime('now');
        $updateOffer->userGenerated = $updateOffer->userGenerated;
        $updateOffer->approved = '0';
        $updateOffer->offline = 0;
        $entityManagerLocale->persist($updateOffer);
        $entityManagerLocale->flush();     // New code Ends

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete('\Core\Domain\Entity\TermAndCondition', 'tc')
            ->setParameter(1, $params['offerId'])
            ->where('tc.termandcondition = ?1')
            ->getQuery();
        $query->execute();

        if (trim($params['termsAndcondition'])!='') {
            $offerTerms  = new \Core\Domain\Entity\TermAndCondition();
            $offerTerms->content = \BackEnd_Helper_viewHelper::stripSlashesFromString($params['termsAndcondition']);
            $offerTerms->deleted = 0;
            $offerTerms->termandcondition = $entityManagerLocale->find('\Core\Domain\Entity\Offer', $params['offerId']);
            $offerTerms->created_at = new \DateTime('now');
            $offerTerms->updated_at = new \DateTime('now');
            $entityManagerLocale->persist($offerTerms);
            $entityManagerLocale->flush();
        }

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete('\Core\Domain\Entity\RefOfferPage', 'rop')
            ->setParameter(1, $params['offerId'])
            ->where('rop.refoffers = ?1')
            ->getQuery();
        $query->execute();

        if (isset($params['attachedpages'])) {
            foreach ($params['attachedpages'] as $pageId) {
                $offerPage  = new \Core\Domain\Entity\RefOfferPage();
                $offerPage->created_at = new \DateTime('now');
                $offerPage->updated_at = new \DateTime('now');
                $offerPage->offers = $entityManagerLocale->find('\Core\Domain\Entity\Page', $pageId);
                $offerPage->refoffers = $entityManagerLocale->find('\Core\Domain\Entity\Offer', $params['offerId']);
                $entityManagerLocale->persist($offerPage);
                $entityManagerLocale->flush();
            }
        }

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->delete('\Core\Domain\Entity\RefOfferCategory', 'roc')
        ->setParameter(1, $params['offerId'])
        ->where('roc.offers = ?1')
        ->getQuery();
        $query->execute();
        if (isset($params['selectedcategories'])) {
            foreach ($params['selectedcategories'] as $categories) {
                $offerCategory  = new \Core\Domain\Entity\RefOfferCategory();
                $offerCategory->created_at = new \DateTime('now');
                $offerCategory->updated_at = new \DateTime('now');
                $offerCategory->categories = $entityManagerLocale->find('\Core\Domain\Entity\Category', $categories);
                $offerCategory->offers = $entityManagerLocale->find('\Core\Domain\Entity\Offer', $params['offerId']);
                $entityManagerLocale->persist($offerCategory);
                $entityManagerLocale->flush();

            }
        }

        try {
            $lId = $params['offerId'];
            $offerId = @$params['offerId'];
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('\Core\Domain\Entity\OfferNews', 'n')
            ->where('n.offerId=' . $offerId)
            ->getQuery();
            $query->execute();
            if (isset($params['newsCheckbox']) && @$params['newsCheckbox'] == "news") {
                $newsloop = @$params['newsTitle'];
                for ($n=0; $n<count($newsloop); $n++) {
                    $savenews = new \Core\Domain\Entity\OfferNews();
                    $savenews->shop = @$entityManagerLocale->find('\Core\Domain\Entity\Shop', $params['selctedshop']);
                    $savenews->offerId = @$offerId;
                    $savenews->title = @$newsloop[$n] != "" ?
                             \BackEnd_Helper_viewHelper::stripSlashesFromString($newsloop[$n]) : "";
                    $savenews->url = @$params['newsrefUrl'][$n] != "" ?
                                    \BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsrefUrl'][$n]) : "";
                    $savenews->content = @$params['newsDescription'][$n] != "" ?
                        \BackEnd_Helper_viewHelper::stripSlashesFromString($params['newsDescription'][$n]) : "";
                    $savenews->linkstatus = @$params['newsdeepLinkStatus'][$n];
                    $entityManagerLocale->persist($savenews);
                    $entityManagerLocale->flush();
                }
            }

            $offerID = $params['offerId'];
            $authorId = self::getAuthorId($offerID);

            $uid = $authorId[0]['authorId'];
            $popularcodekey ="all_". "popularcode".$uid ."_list";
            $newcodekey ="all_". "newestcode".$uid ."_list";

            $key = '6_topOffers'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $shophowtokey = '6_topOffersHowto'  . intval($params['selctedshop']) . '_list';
            //FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($shophowtokey);
            $key = '4_shopLatestUpdates_'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'shop_expiredOffers'  .intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_topthreeexpiredoffers'  . intval($params['selctedshop']) . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'offersAdded_'.intval($params['selctedshop']).'_shop';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'extendedTopOffer_of_'.intval($params['selctedshop']);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'shop_similarShopsAndSimilarCategoriesOffers'. intval($params['selctedshop'])  . '_list';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'extended_'.
                \FrontEnd_Helper_viewHelper::getPermalinkAfterRemovingSpecialChracter($params['extendedOfferRefurl']).
                '_couponDetails';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);

            $key = 'offer_'.$offerID.'_details';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            $key = 'futurecode_'.intval($params['selctedshop']).'_shop';
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_offer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newOffer_list');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('new_offersPageHeader_image');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('error_specialPage_offers');
            \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
            self::clearSpecialPagesCache($offerID);
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

    public static function getExtendedUrl($url)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
        ->select('o.id, o.extendedUrl')
        ->from('\Core\Domain\Entity\Offer', 'o')
        ->setParameter(1, $queryBuilder->expr()->literal(urlencode($url)))
        ->Where('o.extendedUrl = ?1');
        $rp = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $rp;
    }

    public static function getOfferDetailOnShop($id)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('o, s, l, t, img, terms, vot')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('o.logo', 'l')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->leftJoin('o.votes', 'vot')
            ->leftJoin('o.offerTiles', 't')
            ->where('o.deleted = 0')
            ->andWhere('s.deleted = 0')
            ->andWhere('o.id='.$id)
            ->setMaxResults(1);
        $offers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offers;
    }

    public static function getFutureOffersDatesByShopId($shopId)
    {
        $currentDate = date("Y-m-d H:i");
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('o.startDate')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.deleted = 0')
            ->andWhere('o.userGenerated = 0')
            ->andWhere($queryBuilder->expr()->gt('o.startDate', $queryBuilder->expr()->literal($currentDate)))
            ->andWhere($queryBuilder->expr()->eq('o.discountType', $queryBuilder->expr()->literal('CD')))
            ->andWhere('s.deleted = 0')
            ->andWhere('s.status = 1')
            ->andWhere('s.id = '. $shopId)
            ->orderBy('o.startDate', 'ASC');
        $futureOffersCount = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $futureOffersCount;
    }

    public static function getOffers($type, $limit, $homeSection = '')
    {
        $dateTimeFormat = 'Y-m-d H:i:s';
        $currentDate = date($dateTimeFormat);
        $past3Days = date($dateTimeFormat, strtotime('-3 day' . $currentDate));
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select(
            's, o, img, terms'
        )
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'img')
            ->leftJoin('o.offertermandcondition', 'terms')
            ->setParameter(10, 0)
            ->where('o.deleted = ?10')
            ->andWhere(
                "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM \Core\Domain\Entity\CouponCode cc WHERE
                cc.offer = o.id and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
            )
            ->setParameter(8, 0)
            ->andWhere('s.deleted = ?8')
            ->setParameter(9, 1)
            ->andWhere('s.status = ?9')
            ->setParameter(2, 'CD')
            ->andWhere('o.discountType = ?2')
            ->setParameter(3, 'NW')
            ->andWhere('o.discountType != ?3')
            ->setParameter(5, 'MEM')
            ->andWhere('o.Visability != ?5')
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'");
        if ($type == 'UserGeneratedOffers') {
            $query->andWhere('o.userGenerated=1 and o.approved="0"');
        } else {
            $query->andWhere('o.userGenerated=0');
        }

        if ($type == 'totalViewCount') {
            $query->andWhere('o.popularityCount != 0')
                ->orderBy('o.popularityCount', 'DESC');
        } else {
            $query->orderBy('o.startDate', 'DESC');
        }

        if ($homeSection != '') {
            $query->groupBy('s.id');
        }
        $query = $query->setMaxResults($limit);
        $newestCouponCodes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $newestCouponCodes;
    }

    public static function getOffersForNewsletterCache($type, $limit)
    {
        $dateTimeFormat = 'Y-m-d H:i:s';
        $currentDate = date($dateTimeFormat);
        $past3Days = date($dateTimeFormat, strtotime('-3 day' . $currentDate));
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $entityManagerUser->select('o.id')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->setParameter(10, 0)
            ->where('o.deleted = ?10')
            ->andWhere(
                "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM \Core\Domain\Entity\CouponCode cc WHERE
                cc.offer = o.id and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
            )
            ->setParameter(2, 'CD')
            ->andWhere('o.discountType = ?2')
            ->setParameter(3, 'NW')
            ->andWhere('o.discountType != ?3')
            ->setParameter(5, 'MEM')
            ->andWhere('o.Visability != ?5')
            ->andWhere('o.userGenerated=0')
            ->andWhere('o.endDate >'."'".$currentDate."'")
            ->andWhere('o.startDate <='."'".$currentDate."'");

        if ($type == 'totalViewCount') {
            $query->andWhere('o.popularityCount != 0')
                ->orderBy('o.popularityCount', 'DESC');
        } else {
            $query->orderBy('o.startDate', 'DESC');
        }

        $query = $query->setMaxResults($limit);
        $newestCouponCodes = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $newestCouponCodes;
    }

    public static function getTopCouponCodesForNewsletterCache($limit)
    {
        $currentDate = date("Y-m-d H:i");
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $topCouponCodes = $entityManagerLocale->select('o.id')
        ->from('\Core\Domain\Entity\PopularCode', 'p')
        ->leftJoin('p.popularcode', 'o')
        ->where('o.deleted = 0')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM \Core\Domain\Entity\CouponCode cc WHERE
            cc.offer = o.id and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
        )
        ->andWhere('o.offline = 0')
        ->andWhere('o.endDate >'."'".$currentDate."'")
        ->andWhere('o.startDate <='."'".$currentDate."'")
        ->andWhere("o.discountType = 'CD'")
        ->andWhere('o.userGenerated = 0')
        ->andWhere("o.Visability != 'MEM'")
        ->orderBy('p.position', 'ASC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $topCouponCodes;
    }
}

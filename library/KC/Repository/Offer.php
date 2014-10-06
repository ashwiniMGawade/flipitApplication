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
                    "(SELECT count(id) FROM CouponCode WHERE offerid = o.id and status=1) as totalAvailableCodes"
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
                ->setParameter(3, $date)
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
            ->addSelect("(SELECT count(id) FROM CouponCode WHERE offerid = o.id and status=1) as totalAvailableCodes")
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
            ->setParameter(7, $date)
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
            "(couponCodeType = 'UN' AND (SELECT count(id)  FROM CouponCode cc WHERE
            cc.offerid = o.id and status=1)  > 0) or couponCodeType = 'GN'"
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
            ->setParameter(6, $currentDate)
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

}
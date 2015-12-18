<?php

namespace KC\Repository;

class PopularCode extends \Core\Domain\Entity\PopularCode
{
    public static function deleteExpiredPopularCode($date, $flagForCache)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $popIds = $entityManagerLocale
            ->select('offer.id as popularcode, p.position')
            ->from('\Core\Domain\Entity\PopularCode', 'p')
            ->leftJoin('p.popularcode offer')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        
        foreach ($popIds as $popId):
            $popIdsToDelete = $entityManagerLocale
                ->select('o.id')
                ->from('\Core\Domain\Entity\Offer', 'o')
                ->where('o.id ='.$popId['popularcode'])
                ->andWhere('o.endDate <'."'".$date."'")
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if ($popIdsToDelete):
                self::deletePopular($popId['popularcode'], $popId['position'], $flagForCache);
            endif;
        endforeach;

        return true;
    }

    public static function searchTopTenOffer($keyword, $flag)
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder
            ->select('o.title as title')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.deleted=0')
            ->andWhere('s.deleted = 0')
            ->andWhere('s.status = 1')
            ->andWhere('o.offline = 0')
            ->andWhere('o.endDate >'."'".$date."'")
            ->andWhere('o.startDate <='."'".$date."'")
            ->andWhere("o.title LIKE '$keyword%'")
            ->setParameter(4, 'CD')
            ->andWhere('o.discountType = ?4')
            ->setParameter(5, 'MEM')
            ->andWhere('o.Visability != ?5')
            ->andWhere('o.userGenerated = 0')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function searchAllOffer($listOfPopularCode)
    {
        $format = 'Y-m-j H:i:s';
        $date = date($format);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('o.title as title, o.id as id, o.userGenerated')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.deleted = 0')
            ->andWhere('s.deleted = 0')
            ->andWhere('o.offline = 0')
            ->andWhere('o.endDate >'."'".$date."'")
            ->andWhere('o.startDate <='."'".$date."'")
            ->setParameter(4, 'CD')
            ->andWhere('o.discountType = ?4')
            ->setParameter(5, 'MEM')
            ->andWhere('o.Visability != ?5')
            ->andWhere('o.userGenerated = 0');
        if (!empty($listOfPopularCode)) {
            $query = $query
                ->setParameter(1, $listOfPopularCode)
                ->andWhere($queryBuilder->expr()->notIn('o.id', '?1'));
        }
        $data = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function getPopularCode($limit = 27, $type = 'MN')
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder
            ->select('p.id,o.title, p.type, p.position,o.id as offerId')
            ->from('\Core\Domain\Entity\PopularCode', 'p')
            ->leftJoin('p.popularcode', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.deleted = 0')
            ->andWhere("o.userGenerated = 0")
            ->andWhere('s.deleted = 0')
            ->andWhere('o.offline = 0')
            ->andWhere('p.type = '."'".$type."'")
            ->andWhere('o.endDate >'."'".$date."'")
            ->andWhere('o.startDate <='."'".$date."'")
            ->setMaxResults($limit)
            ->orderBy('p.position', 'ASC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function gethomePopularvoucherCode($flag)
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder
            ->select('o.title, o.id as offerId, o.couponCodeType,o.extendedOffer,o.startDate,o.endDate,o.extendedUrl,o.couponCode as couponcode ,o.exclusiveCode as exclusivecode ,o.discount,o.discountvalueType,s.name as shopName, s.id as shopId, s.permaLink,l.path,l.name, p.type, p.position')
            ->from('\Core\Domain\Entity\PopularCode', 'p')
            ->leftJoin('p.popularcode', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'l')
            ->where('o.deleted = 0')
            ->andWhere(
                "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM \Core\Domain\Entity\CouponCode cc WHERE
                cc.offer = o.id and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
            )
            ->andWhere('s.deleted = 0')
            ->andWhere('o.offline = 0')
            ->andWhere('s.status = 1')
            ->andWhere('o.endDate >'."'".$date."'")
            ->andWhere('o.startDate <='."'".$date."'")
            ->setParameter(4, 'CD')
            ->andWhere('o.discountType = ?4')
            ->setParameter(5, 'MEM')
            ->andWhere('o.Visability != ?5')
            ->andWhere('o.userGenerated = 0')
            ->orderBy('p.position', 'ASC')
            ->setMaxResults($flag)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function gethomePopularvoucherCodeForMarktplaatFeeds($flag, $id = array(6))
    {
        $date = date('Y-m-d H:i:s');
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $data = $queryBuilder
            ->select('p,o,s,l')
            ->from('\Core\Domain\Entity\PopularCode', 'p')
            ->leftJoin('p.popularcode', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'l')
            ->leftJoin('o.offertermandcondition', 'term')
            ->where('o.deleted =0')
            ->andWhere(
                "(o.couponCodeType = 'UN' AND (SELECT count(cc.id)  FROM \Core\Domain\Entity\CouponCode cc WHERE
                cc.offer = o.id and cc.status=1)  > 0) or o.couponCodeType = 'GN'"
            )
            ->andWhere('s.deleted=0')
            ->setParameter(1, $id)
            ->andWhere($queryBuilder->expr()->notIn('s.id', '?1'))
            ->andWhere('o.offline = 0')
            ->andWhere('s.status = 1')
            ->andWhere('o.endDate >'."'".$date."'")
            ->andWhere('o.startDate <='."'".$date."'")
            ->setParameter(4, 'CD')
            ->andWhere('o.discountType = ?4')
            ->setParameter(5, 'MEM')
            ->andWhere('o.Visability != ?5')
            ->andWhere('o.userGenerated = 0')
            ->orderBy('p.position', 'ASC')
            ->setMaxResults($flag)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $data;
    }

    public static function addOfferInList($id, $type = "MN")
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $offer = $queryBuilder
            ->select('o')
            ->from('\Core\Domain\Entity\Offer', 'o')
            ->where('o.id =' . $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $flag = '0';

        if (sizeof($offer) > 0) {
            //check offer exist or not
            $queryBuilderPopularCode = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $pc = $queryBuilderPopularCode
                ->select('pc')
                ->from('\Core\Domain\Entity\PopularCode', 'pc')
                ->where('pc.popularcode =' . $id)
                ->andWhere('pc.type = '."'".$type."'")
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (sizeof($pc) > 0) {
                $flag = '2';
            } else {
                $flag = '1';
                //find last postion  from database
                $queryBuilderPosition = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $data = $queryBuilderPosition
                    ->select('p.position')
                    ->from('\Core\Domain\Entity\PopularCode', 'p')
                    ->Where('p.type = '."'".$type."'")
                    ->orderBy('p.position', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (sizeof($data) > 0) {
                    $NewPos = $data[0]['position'];
                } else {
                    $NewPos =  0 ;
                }
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $pc = new \Core\Domain\Entity\PopularCode();
                $pc->type = strtoupper($type);
                $pc->popularcode = $entityManagerLocale->find('\Core\Domain\Entity\Offer', $id);
                $pc->position = (intval($NewPos) + 1);
                $pc->deleted = 0;
                $pc->status = 1;
                $pc->created_at = new \DateTime('now');
                $pc->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($pc);
                $entityManagerLocale->flush();
                
                $flag  = array(
                    'id' => $pc->id,
                    'type' => $type,
                    'offerId' => $id,
                    'position' => (intval($NewPos) + 1),
                    'title' => $offer[0]['title']
                );
            }
        }

        self::clearTop20Cache();
        return $flag;
    }

    public static function deletePapularCode($id, $position, $type = "MN")
    {
        if ($id) {
            $queryBuilderPopularOffer = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $offerDetail = $queryBuilderPopularOffer
                ->select('offer.id')
                ->from('\Core\Domain\Entity\PopularCode', 'pcode')
                ->leftJoin('pcode.popularcode', 'offer')
                ->where('pcode.id=' . $id)
                ->setMaxResults(1)
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            $queryBuilderOffer = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilderOffer
                ->update('\Core\Domain\Entity\Offer', 'o')
                ->set('o.editorPicks', $queryBuilderOffer->expr()->literal('o.editorPicks - 1'))
                ->where('o.id = '.$offerDetail[0]['id'])
                ->where('o.editorPicks > 1')
                ->getQuery()
                ->execute();

            $queryBuilderOffer
                ->update('\Core\Domain\Entity\Offer', 'o')
                ->set('o.editorPicks', '0')
                ->where('o.id = '.$offerDetail[0]['id'])
                ->where('o.editorPicks <= 1')
                ->getQuery()
                ->execute();

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('\Core\Domain\Entity\PopularCode', 's')
                ->where('s.id ='.$id)
                ->getQuery()
                ->execute();

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $queryBuilder ->update('\Core\Domain\Entity\PopularCode', 'pc')
                ->set('pc.position', $queryBuilder->expr()->literal('pc.position -1'))
                ->where('pc.position > '.$position)
                ->Where('pc.type = '."'".$type."'")
                ->getQuery()
                ->execute();

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $newOfferList = $queryBuilder
                ->select('popularcode')
                ->from('\Core\Domain\Entity\PopularCode', 'popularcode')
                ->Where('popularcode.type = '."'".$type."'")
                ->getQuery()
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $newPos = 1;

            foreach ($newOfferList as $newOffer) {
                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $queryBuilder ->update('\Core\Domain\Entity\PopularCode', 'popularCode')
                    ->set('popularCode.position', $newPos)
                    ->where('popularCode.id ='.$newOffer['id'])
                    ->getQuery()->execute();
                $newPos++;
            }

            self::clearTop20Cache();
            return true;
        }
        return false;
    }

    public static function deletePopular($id, $position, $flagForCache)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->delete('\Core\Domain\Entity\PopularCode', 's')
                ->where('s.id ='.$id)
                ->getQuery()
                ->execute();

            if ($flagForCache==true) {
                $queryBuilder
                    ->update('\Core\Domain\Entity\PopularCode', 'pc')
                    ->set('pc.position', 'pc.position -1')
                    ->where('pc.position > '.$position)
                    ->getQuery()
                    ->execute();

                self::clearTop20Cache();
            }
        }
    }

    public static function savePopularOffersPosition($offerId, $type = "MN")
    {
        if (!empty($offerId)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->delete('\Core\Domain\Entity\PopularCode', 's')
                ->where('s.id > 0')
                ->andWhere('s.type = '."'".$type."'")
                ->getQuery()
                ->execute();
            $offerId = explode(',', $offerId);
            $i = 1;
            foreach ($offerId as $offerIdValue) {
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $popularCode = new \Core\Domain\Entity\PopularCode();
                $popularCode->popularcode = $entityManagerLocale->find('\Core\Domain\Entity\Offer', $offerIdValue);
                $popularCode->position = $i;
                $popularCode->type = strtoupper($type);
                $popularCode->status = 1;
                $popularCode->deleted = 0;
                $popularCode->created_at = new \DateTime('now');
                $popularCode->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($popularCode);
                $entityManagerLocale->flush();
                $i++;
            }
        }
        self::clearTop20Cache();
    }

    protected static function clearTop20Cache()
    {
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularOffersHome_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('5_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShops_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('10_popularShopsHome_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('20_topOffers_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularVoucherCodesList_feed');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularvaouchercode_list_shoppage');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_newpopularcode_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('shop_popularShopForWidget_list');
    }
}

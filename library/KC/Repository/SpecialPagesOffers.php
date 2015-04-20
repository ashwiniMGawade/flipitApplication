<?php

namespace KC\Repository;

class SpecialPagesOffers extends \KC\Entity\SpecialPagesOffers
{
    public static function getSpecialPageOffersByPageIdForFrontEnd($pageId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDate = date("Y-m-d H:i");
        $query = $queryBuilder
        ->select('op, o, terms, s, l')
        ->from('KC\Entity\SpecialPagesOffers', 'op')
        ->leftJoin('op.offers', 'o')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(cc.id) FROM KC\Entity\CouponCode cc WHERE cc.offer = o.id and o.status=1)  > 0)
            or o.couponCodeType = 'GN'"
        )
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('s.logo', 'l')
        ->where('op.pages = '.$pageId)
        ->andWhere('o.endDate >'.$queryBuilder->expr()->literal($currentDate))
        ->andWhere('o.startDate <='.$queryBuilder->expr()->literal($currentDate))
        ->andWhere('o.deleted = 0')
        ->andWhere('s.deleted = 0')
        ->andWhere('s.status = 1')
        ->andWhere($queryBuilder->expr()->neq('o.Visability', $queryBuilder->expr()->literal("MEM")))
        ->orderBy('op.position');
        $specialPageOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return self::removeDuplicateOffers($specialPageOffers);
    }

    public static function removeDuplicateOffers($specialPageOffers)
    {
        $specialOffersWithoutDuplication = array();
        if (count($specialPageOffers) > 0) {
            $countOfSpecialPageOffers = count($specialPageOffers);
            for ($offerIndex = 0; $offerIndex < $countOfSpecialPageOffers; $offerIndex++) {
                $specialOffersWithoutDuplication[$offerIndex] = $specialPageOffers[$offerIndex]['offers'];
            }
        }
        return $specialOffersWithoutDuplication;
    }

    public static function getSpecialPageOfferById($pageId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p.id, o.title, p.position, o.id as offerId, page.id as pageId')
            ->from('KC\Entity\SpecialPagesOffers', 'p')
            ->leftJoin('p.offers', 'o')
            ->leftJoin('p.pages', 'page')
            ->where('p.pages ='.$pageId)
            ->orderBy('p.position');
        $specialPageOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialPageOffers;
    }

    public static function addOfferInList($offerId, $pageId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $specialPageOffersQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $specialPageOffersPositionQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('o')
            ->from('KC\Entity\Offer', 'o')
            ->where('o.id=' . $offerId)
            ->setMaxResults(1);
        $offer = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $result = '0';
        if (sizeof($offer) > 0) {
                $query = $specialPageOffersQueryBuilder
                    ->select('sl')
                    ->from('KC\Entity\SpecialPagesOffers', 'sl')
                    ->where('sl.offers=' . $offerId)
                    ->andWhere('sl.pages=' .$pageId);
                $specialPageOffer = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (!empty($specialPageOffer)) {
                $result = '2';
            } else {
                $result = '1';
                $query = $specialPageOffersPositionQueryBuilder
                    ->select('p.position')
                    ->from('KC\Entity\SpecialPagesOffers', 'p')
                    ->where('p.pages=' .$pageId)
                    ->orderBy('p.position', 'DESC')
                    ->setMaxResults(1);
                $maxPosition = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (!empty($maxPosition)) {
                    $newPosition = $maxPosition[0]['position'];
                } else {
                    $newPosition =  0 ;
                }
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $specialPageOffer = new \KC\Entity\SpecialPagesOffers();
                $specialPageOffer->offers = $entityManagerLocale->find('KC\Entity\Offer', $offerId);
                $specialPageOffer->pages = $entityManagerLocale->find('KC\Entity\Page', $pageId);
                $specialPageOffer->position = (intval($newPosition) + 1);
                $specialPageOffer->deleted = 0;
                $specialPageOffer->created_at = new \DateTime('now');
                $specialPageOffer->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($specialPageOffer);
                $entityManagerLocale->flush();
                $result  = array(
                    'id'=>$specialPageOffer->id,
                    'type'=>'MN',
                    'offerId'=>$offerId,
                    'position'=>(intval($newPosition) + 1),
                    'title'=>$offer['title']
                );
            }
        }
        self::clearCacheOfSpecialPagesOffers($pageId);
        return $result;
    }


    public static function deleteCode($id, $position, $pageId)
    {
        if ($id) {
            $queryBuilderDelete = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderDelete
                ->delete('KC\Entity\SpecialPagesOffers', 'spl')
                ->where('spl.id ='.$id)
                ->getQuery();
            $query->execute();
            
            $queryBuilderUpdate = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderUpdate
                ->update('KC\Entity\SpecialPagesOffers', 'sp')
                ->set('sp.position', $queryBuilderUpdate->expr()->literal('p.position -1'))
                ->where('sp.position > '.$position)
                ->andWhere('sp.pages='. $pageId)
                ->getQuery();
            $query->execute();

            $queryBuilderSelect = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderSelect
                ->select('spo')
                ->from('KC\Entity\SpecialPagesOffers', 'spo')
                ->where('spo.pages='. $pageId)
                ->orderBy('spo.position', 'ASC');
            $newOffersList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            $newPosition = 1;
            $queryBuilderSpecialPage = \Zend_Registry::get('emLocale')->createQueryBuilder();
            foreach ($newOffersList as $newOffer) {
                $query = $queryBuilderSpecialPage
                    ->update('KC\Entity\SpecialPagesOffers', 'p')
                    ->set('p.position', $newPosition)
                    ->where('p.id = '.$newOffer['id'])
                    ->getQuery();
                $query->execute();
                $newPosition++;
            }
            self::clearCacheOfSpecialPagesOffers($pageId);
            return true;
        }
        return false;
    }

    public static function savePosition($offerIds, $pageId)
    {
        if (!empty($offerIds)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->delete('KC\Entity\SpecialPagesOffers', 'spl')
                ->where('spl.pages ='.$pageId)
                ->getQuery();
            $query->execute();
            $offerIds = explode(',', $offerIds);
            $i = 1;
            foreach ($offerIds as $offerId) {
                $entityManagerLocale  = \Zend_Registry::get('emLocale');
                $specialPageOffer = new \KC\Entity\SpecialPagesOffers();
                $specialPageOffer->offers = $entityManagerLocale->find('KC\Entity\Offer', $offerId);
                $specialPageOffer->pages = $entityManagerLocale->find('KC\Entity\Page', $pageId);
                $specialPageOffer->position = $i;
                $specialPageOffer->deleted = 0;
                $specialPageOffer->created_at = new \DateTime('now');
                $specialPageOffer->updated_at = new \DateTime('now');
                $entityManagerLocale->persist($specialPageOffer);
                $entityManagerLocale->flush();
                $i++;
            }
        }
        self::clearCacheOfSpecialPagesOffers($pageId);
    }

    public static function clearCacheOfSpecialPagesOffers($id)
    {
        $key = 'error_specialPage'.$id.'_offers';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_count');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
    }
}

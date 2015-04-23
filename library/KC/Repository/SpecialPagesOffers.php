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

    public static function getSpecialPageOfferById($pageId, $limit = 0)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select(
                'p.id, o.title, p.position, o.id as offerId, o, page.id as pageId,
                s.permaLink as permalink, s.name, l'
            )
            ->from('KC\Entity\SpecialPagesOffers', 'p')
            ->leftJoin('p.offers', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->leftJoin('s.logo', 'l')
            ->leftJoin('p.pages', 'page')
            ->andWhere($queryBuilder->expr()->in('p.pages', $pageId))
            ->orderBy('p.position')
            ->setMaxResults($limit);
        $specialPageOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialPageOffers;
    }

    public static function addOfferInList($offerId, $pageId, $type = '')
    {
        $offer = self::offerExistance($offerId);
        $result = '0';
        if (sizeof($offer) > 0) {
            $specialPageOffers = self::getSpecialPageOffers($offerId, $pageId);
            if (!empty($specialPageOffer)) {
                $result = '2';
            } else {
                $result = '1';
                $specialPageOffermaxPosition = self::getSpecialPageMaxPosition($pageId);
                if (!empty($specialPageOffermaxPosition)) {
                    $newPosition = $specialPageOffermaxPosition[0]['position'];
                } else {
                    $newPosition =  0 ;
                }
                $specialPageOfferId = self::saveSpecialPageOffers($offerId, $pageId, $newPosition);
                $result  = array(
                    'id'=>$specialPageOffer->id,
                    'type'=>'MN',
                    'offerId'=>$offerId,
                    'position'=>(intval($newPosition) + 1),
                    'title'=>$offer['title']
                );
            }
        }
        if ($type != 'cron') {
            self::clearCacheOfSpecialPagesOffers($pageId);
        }
        return $result;
    }

    public static function offerExistance($offerId)
    {
        $offer = array();
        if (!empty($offerId)) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->select('o')
                ->from('KC\Entity\Offer', 'o')
                ->where('o.id=' . $offerId)
                ->setMaxResults(1);
            $offer = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $offer;
    }

    public static function getSpecialPageOffers($offerId, $pageId)
    {
        $specialPageOffers = array();
        if (!empty($offerId) && !empty($pageId)) {
            $specialPageOffersQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $specialPageOffersQueryBuilder
                ->select('sl')
                ->from('KC\Entity\SpecialPagesOffers', 'sl')
                ->where('sl.offers=' . $offerId)
                ->andWhere('sl.pages=' .$pageId);
            $specialPageOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $specialPageOffers;
    }

    public static function getSpecialPageMaxPosition($pageId)
    {
        if (!empty($pageId)) {
            $specialPageOffersPositionQueryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $specialPageOffersPositionQueryBuilder
                ->select('p.position')
                ->from('KC\Entity\SpecialPagesOffers', 'p')
                ->where('p.pages=' .$pageId)
                ->orderBy('p.position', 'DESC')
                ->setMaxResults(1);
            $maxPosition = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $maxPosition;
    }

    public static function saveSpecialPageOffers($offerId, $pageId, $newPosition)
    {
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
        return $specialPageOffer->id;
    }

    public static function deleteSpecialPageOffer($id)
    {
        if (!empty($id)) {
            $queryBuilderDelete = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderDelete
                ->delete('KC\Entity\SpecialPagesOffers', 'spl')
                ->where('spl.id ='.$id)
                ->getQuery();
            $query->execute();
        }
        return true;
    }

    public static function updateSpecialPageOfferPosition($position, $pageId)
    {
        if (!empty($position) && !empty($pageId)) {
            $queryBuilderUpdate = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderUpdate
                ->update('KC\Entity\SpecialPagesOffers', 'sp')
                ->set('sp.position', $queryBuilderUpdate->expr()->literal('p.position -1'))
                ->where('sp.position > '.$position)
                ->andWhere('sp.pages='. $pageId)
                ->getQuery();
            $query->execute();
        }
        return true;
    }

    public static function getNewOfferList($pageId)
    {
        $newOffersList = array();
        if (!empty($pageId)) {
            $queryBuilderSelect = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilderSelect
                ->select('spo')
                ->from('KC\Entity\SpecialPagesOffers', 'spo')
                ->where('spo.pages='. $pageId)
                ->orderBy('spo.position', 'ASC');
            $newOffersList = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        }
        return $newOffersList;
    }

    public static function updateWithNewPosition($newPosition, $newOffer)
    {
        $query = $queryBuilderSpecialPage
            ->update('KC\Entity\SpecialPagesOffers', 'p')
            ->set('p.position', $newPosition)
            ->where('p.id = '.$newOffer['id'])
            ->getQuery();
        $query->execute();
        return true;
    }

    public static function deleteCode($id, $position, $pageId)
    {
        if ($id) {
            self::deleteSpecialPageOffer($id);
            self::updateSpecialPageOfferPosition($position, $pageId);
            $newOffersList = self::getNewOfferList($pageId);
            $newPosition = 1;
            $queryBuilderSpecialPage = \Zend_Registry::get('emLocale')->createQueryBuilder();
            foreach ($newOffersList as $newOffer) {
                self::updateWithNewPosition($newPosition, $newOffer);
                $newPosition++;
            }
            if ($type != 'cron') {
                self::clearCacheOfSpecialPagesOffers($pageId);
            }
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

    public static function deleteExpiredOffers()
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('p')
            ->from('KC\Entity\SpecialPagesOffers', 'p')
            ->where('p.deleted = 0')
            ->orderBy('p.position ASC');
        $specialPageOffersDetails = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if (!empty($specialPageOffersDetails)) {
            foreach ($specialPageOffersDetails as $specialPageOfferDetail) {
                $expiredStatus = \KC\Repository\Offer::checkOfferExpired($specialPageOfferDetail['offers']['id']);
                if ($expiredStatus) {
                    self::deleteCode(
                        $specialPageOfferDetail['id'],
                        $specialPageOfferDetail['position'],
                        $specialPageOfferDetail['pages']['id'],
                        'cron'
                    );
                }
            }
        }
        return true;
    }

    public static function addNewSpecialPageOffers()
    {
        $currentDate = date("Y-m-d H:i");
        $specialListPages = \KC\Repository\SpecialList::getSpecialPages();
        if (!empty($specialListPages)) {
            foreach ($specialListPages as $specialListPage) {
                \KC\Repository\SpecialList::updateTotalOffersAndTotalCoupons(
                    $specialListPage['totalOffers'],
                    $specialListPage['totalCoupons'],
                    $specialListPage['specialpageId']
                );
                foreach ($specialListPage['page'] as $page) {
                    $pageRelatedOffers = \KC\Repository\Offer::getSpecialOffersByPage($page['id'], $currentDate);
                    $constraintsRelatedOffers = \KC\Repository\Offer::getOffersByPageConstraints($page, $currentDate);
                    $pageRelatedOffersAndPageConstraintsOffers = array_merge($pageRelatedOffers, $constraintsRelatedOffers);
                    foreach ($pageRelatedOffersAndPageConstraintsOffers as $pageRelatedOffersAndPageConstraintsOffer) {
                        self::addOfferInList($pageRelatedOffersAndPageConstraintsOffer['id'], $page['id'], 'cron');
                    }
                }
            }
        }
        return true;
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

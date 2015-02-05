<?php

namespace KC\Repository;

class SpecialPagesOffers extends \KC\Entity\SpecialPagesOffers
{
    public static function getSpecialPageOffersByPageIdForFrontEnd($pageId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $currentDate = date("Y-m-d H:i");
        $query = $queryBuilder
        ->select(
            'op,o.couponCodeType,o.totalViewcount as clicks,o.title,o.refURL,o.refOfferUrl,
            o.discountType,o.startDate,o.endDate,o.authorId,o.authorName,o.Visability,o.couponCode,o.exclusiveCode,
            o.editorPicks,o.discount,o.discountvalueType,o.startDate as startdate,o.extendedOffer,o.extendedUrl,
            o.updated_at as lastUpdate,s.name,s.refUrl,
            s.actualUrl,s.permaLink as permalink,s.views,l,fv,terms.content'
        )
        ->from('KC\Entity\SpecialPagesOffers', 'op')
        ->leftJoin('op.offers', 'o')
        ->leftJoin('o.offertermandcondition', 'terms')
        ->andWhere(
            "(o.couponCodeType = 'UN' AND (SELECT count(id) FROM KC\Entity\CouponCode cc WHERE cc.offer = o.id and o.status=1)  > 0)
            or couponCodeType = 'GN'"
        )
        ->leftJoin('o.shopOffers', 's')
        ->leftJoin('o.votes', 'vot')
        ->leftJoin('s.logo', 'l')
        ->leftJoin('s.favoriteshops', 'fv')
        ->where('op.pages = '.$pageId)
        ->andWhere('o.endDate >'.$queryBuilder->expr()->literal($currentDate))
        ->andWhere('o.startDate <='.$queryBuilder->expr()->literal($currentDate))
        ->andWhere('o.deleted = 0')
        ->andWhere('s.deleted = 0')
        ->andWhere('s.status = 1')
        ->andWhere('o.Visability!='.$queryBuilder->expr()->literal("MEM"))
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
            ->select('p.id, p.pageId, p.offerId, o.title, p.position')
            ->from('KC\Entity\SpecialPagesOffers', 'p')
            ->leftJoin('p.offers', 'o')
            ->where('p.pages ='.$pageId)
            ->orderBy('p.position');
        $specialPageOffers = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $specialPageOffers;
    }

    public static function addOfferInList($offerId, $pageId)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder
            ->select('o')
            ->from('KC\Entity\Offer', 'o')
            ->where('id=' . $offerId)
            ->setMaxResults(1);
        $offer = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $result = '0';
        if (sizeof($offer) > 0) {
                $query = $queryBuilder
                    ->select('sl')
                    ->from('KC\Entity\SpecialPagesOffers', 'sl')
                    ->where('sl.offers=' . $offerId)
                    ->andWhere('sl.pages=' .$pageId)
                    ->setMaxResults(1);
                $specialPageOffer = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            if (!empty($specialPageOffer)) {
                $result = '2';
            } else {
                $result = '1';
                $query = $queryBuilder
                    ->select('p.position')
                    ->from('KC\Entity\SpecialPagesOffers', 'p')
                    ->where('pages=' .$pageId)
                    ->orderBy('p.position', 'DESC')
                    ->setMaxResults(1);
                $maxPosition = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (!empty($maxPosition)) {
                    $newPosition = $maxPosition[0]['position'];
                } else {
                    $newPosition =  0 ;
                }
                $specialPageOffer = new KC\Repository\SpecialPagesOffers();
                $specialPageOffer->offers = $offerId;
                $specialPageOffer->pages = $pageId;
                $specialPageOffer->position = (intval($newPosition) + 1);
                $specialPageOffer->save();
                $result  = array(
                    'id'=>$specialPageOffer->id,
                    'type'=>'MN',
                    'offerId'=>$offerId,
                    'position'=>(intval($newPosition) + 1),
                    'title'=>$offer[0]['title']
                );
            }
        }
        self::clearCacheOfSpecialPagesOffers($pageId);
        return $result;
    }


    public static function deleteCode($id, $position, $pageId)
    {
        if ($id) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder
                ->delete('KC\Entity\SpecialPagesOffers', 'spl')
                ->where('spl.id ='.$id)
                ->getQuery();
            $query->execute();

            $query = $queryBuilder
                ->update('KC\Entity\SpecialPagesOffers', 'p')
                ->set('p.position', $queryBuilder->expr()->literal('p.position -1'))
                ->where('p.position', $queryBuilder->expr()->gt($position))
                ->andWhere('p.pages='. $pageId)
                ->getQuery();
            $query->execute();
             
            $query = $queryBuilder
                ->select('p')
                ->from('KC\Entity\SpecialPagesOffers', 'p')
                ->where('p.pages='. $pageId)
                ->orderBy('p.position', 'ASC');
            $newOffersList = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            $newPosition = 1;
            foreach ($newOffersList as $newOffer) {
                $query = $queryBuilder
                    ->update('KC\Entity\SpecialPagesOffers', 'p')
                    ->set('p.position', $newPosition)
                    ->where('p.id = ', $newOffer['id'])
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
                $specialPageOffer = new KC\Entity\SpecialPagesOffers();
                $specialPageOffer->offers = $offerId;
                $specialPageOffer->pages = $pageId;
                $specialPageOffer->position = $i;
                $specialPageOffer->save();
                $i++;
            }
        }
        self::clearCacheOfSpecialPagesOffers($pageId);
    }

    public static function clearCacheOfSpecialPagesOffers($id)
    {
        $key = 'error_specialPage'.$id.'_offers';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPagesHome_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_count');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_specialPages_list');
    }
}

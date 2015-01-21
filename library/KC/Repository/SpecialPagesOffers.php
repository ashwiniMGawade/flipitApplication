<?php

namespace KC\Repository;

class SpecialPagesOffers extends \KC\Entity\SpecialPagesOffers
{
    public static function getSpecialPageOffersByPageIdForFrontEnd($pageId)
    {
        $currentDate = date("Y-m-d H:i");
        $specialPageOffers = Doctrine_Query::create()
        ->select(
            'op.pageId,op.offerId,o.couponCodeType,o.totalViewcount as clicks,o.title,o.refURL,o.refOfferUrl,
            o.discountType,o.startDate,o.endDate,o.authorId,o.authorName,o.Visability,o.couponCode,o.exclusiveCode,
            o.editorPicks,o.discount,o.discountvalueType,o.startdate,o.extendedOffer,o.extendedUrl,
            o.updated_at as lastUpdate,s.name,s.refUrl,
            s.actualUrl,s.permaLink as permalink,s.views,l.*,fv.id,fv.visitorId,fv.shopId,vot.id,vot.vote, ologo.path,
            ologo.name,terms.content'
        )
        ->from('SpecialPagesOffers op')
        ->leftJoin('op.offers o')
        ->leftJoin('o.logo ologo')
        ->leftJoin('o.termandcondition terms')
        ->andWhere(
            "(couponCodeType = 'UN' AND (SELECT count(id) FROM CouponCode cc WHERE cc.offerid = o.id and status=1)  > 0)
            or couponCodeType = 'GN'"
        )
        ->leftJoin('o.shop s')
        ->leftJoin('o.vote vot')
        ->leftJoin('s.logo l')
        ->leftJoin('s.favoriteshops fv')
        ->where('op.pageId = '.$pageId)
        ->andWhere('o.enddate > "'.$currentDate.'"')
        ->andWhere('o.startdate <= "'.$currentDate.'"')
        ->andWhere('o.deleted = 0')
        ->andWhere('s.deleted = 0')
        ->andWhere('s.status = 1')
        ->andWhere('o.Visability!="MEM"')
        ->orderBy('op.position')
        ->fetchArray();
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
        $specialPageOffers = Doctrine_Query::create()
            ->select('p.id, p.pageId, p.offerId, o.title, p.position')
            ->from('SpecialPagesOffers p')
            ->leftJoin('p.offers o')
            ->where('p.pageId ='.$pageId)
            ->orderBy('p.position')
            ->fetchArray();
        return $specialPageOffers;
    }

    public static function addOfferInList($offerId, $pageId)
    {
        $offer = Doctrine_query::create()
            ->from('Offer')
            ->where('id=' . $offerId)->limit(1)
            ->fetchArray();
        $result = '0';
        if (sizeof($offer) > 0) {
            $specialPageOffer = Doctrine_query::create()
                ->from('SpecialPagesOffers')
                ->where('offerId=' . $offerId)
                ->andWhere('pageId=' .$pageId)
                ->limit(1)
                ->fetchArray();
            if (!empty($specialPageOffer)) {
                $result = '2';
            } else {
                $result = '1';
                $maxPosition = Doctrine_Query::create()
                    ->select('p.position')
                    ->from('SpecialPagesOffers p')
                    ->where('pageId=' .$pageId)
                    ->orderBy('p.position DESC')
                    ->limit(1)
                    ->fetchArray();
                if (!empty($maxPosition)) {
                    $newPosition = $maxPosition[0]['position'];
                } else {
                    $newPosition =  0 ;
                }
                $specialPageOffer = new SpecialPagesOffers();
                $specialPageOffer->offerId = $offerId;
                $specialPageOffer->pageId = $pageId;
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
            $deleteCode = Doctrine_Query::create()
            ->delete('SpecialPagesOffers')
            ->where('id=' . $id)
            ->execute();
            $updatePosition = Doctrine_Query::create()
                ->update('SpecialPagesOffers p')
                ->set('p.position', 'p.position -1')
                ->where('p.position >' . $position)
                ->andWhere('p.pageId='. $pageId)
                ->execute();
            $newOffersList = Doctrine_Query::create()
                ->select('p.*')
                ->from('SpecialPagesOffers p')
                ->where('p.pageId='. $pageId)
                ->orderBy('p.position ASC')
                ->fetchArray();
            $newPosition = 1;
            foreach ($newOffersList as $newOffer) {
                $updateWithNewPosition = Doctrine_Query::create()
                    ->update('SpecialPagesOffers p')
                    ->set('position', $newPosition)
                    ->where('p.id = ?', $newOffer['id']);
                $updateWithNewPosition->execute();
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
            $deleteCode = Doctrine_Query::create()
                ->delete('SpecialPagesOffers p')
                ->where('p.pageId=' . $pageId)
                ->execute();
            $offerIds = explode(',', $offerIds);
            $i = 1;
            foreach ($offerIds as $offerId) {
                $specialPageOffer = new SpecialPagesOffers();
                $specialPageOffer->offerId = $offerId;
                $specialPageOffer->pageId = $pageId;
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

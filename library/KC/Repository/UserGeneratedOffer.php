<?php
namespace KC\Repository;

class UserGeneratedOffer extends \KC\Entity\Offer
{
    public static function getOffersList($parameters)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $searchShop = $parameters["shopText"]!='undefined' ? $parameters["shopText"] : '';
        $searchCoupon = @$parameters["shopCoupon"]!='undefined' ? @$parameters["shopCoupon"] : '';
        $deletedStatus = $parameters['flag'];
        $getOffersQuery = $queryBuilder
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.deleted ='. $deletedStatus)
            ->andWhere("o.userGenerated = 1")
            ->andWhere($queryBuilder->expr()->eq("o.approved", $queryBuilder->expr()->literal("0")));
        if ($searchShop!='') {
            $getOffersQuery->andWhere($queryBuilder->expr()->like('s.name', $queryBuilder->expr()->literal('%'.$searchShop.'%')));
        }
        if ($searchCoupon!='') {
            $getOffersQuery->andWhere($queryBuilder->expr()->like('o.couponCode', $queryBuilder->expr()->literal('%'.$searchCoupon.'%')));
        }

        $request  = \DataTable_Helper::createSearchRequest(
            $parameters,
            array('s.name','o.couponCode','o.startDate','o.endDate')
        );
        $builder  = new \NeuroSYS\DoctrineDatatables\TableBuilder(\Zend_Registry::get('emLocale'), $request);
        $builder
            ->setQueryBuilder($getOffersQuery)
            ->add('text', 's.name')
            ->add('text', 'o.couponCode')
            ->add('number', 'o.startDate')
            ->add('number', 'o.endDate');
        $offersList = $builder->getTable()->getResponseArray();
        return $offersList;
    }

    public static function searchToFiveOffer($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $offers = $queryBuilder
            ->select('o.title as title')
            ->from("KC\Entity\Offer", "o")
            ->where($queryBuilder->expr()->eq('o.deleted', $queryBuilder->expr()->literal($flag)))
            ->andWhere('o.offline = 0')
            ->andWhere($queryBuilder->expr()->like('o.title', $queryBuilder->expr()->literal($keyword.'%')))
            ->andWhere($queryBuilder->expr()->eq("o.userGenerated = '1'"))
            ->orderBy("o.title", "ASC")
            ->setMaxResults(5)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $offers;
    }

    public static function searchToFiveShop($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $shops = $queryBuilder
            ->select('o.id,s.name as name')
            ->from("KC\Entity\Offer", "o")
            ->leftJoin('o.shopOffers', 's')
            ->where('o.deleted = '.$flag)
            ->andWhere('s.status = 1')
            ->andWhere($queryBuilder->expr()->like('s.name', $queryBuilder->expr()->literal($keyword.'%')))
            ->andWhere("(o.userGenerated = 1 and o.approved='0')")
            ->orderBy("s.id", "ASC")
            ->groupBy('s.name')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shops;
    }

    public static function searchToFiveCoupon($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $coupons = $queryBuilder
            ->select('o.title as title, o.id, o.couponCode')
            ->from("KC\Entity\Offer", "o")
            ->where('o.deleted = '.$flag)
            ->andWhere($queryBuilder->expr()->like('o.couponCode', $queryBuilder->expr()->literal($keyword.'%')))
            ->andWhere("(o.userGenerated=1 and o.approved='0')")
            ->orderBy("o.id", "ASC")
            ->groupBy('o.couponCode')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $coupons;
    }


    public static function saveApprovedStatus($offerId, $status)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $offer = $entityManagerLocale->find('\KC\Entity\Offer', $offerId);
        if (!empty($status)) {
            $offer->approved = 1;
            $authorId = \KC\Repository\Offer::getAuthorId($offerId);
            if (empty($authorId[0]['authorId'])) {
                $offer->authorId = \Auth_StaffAdapter::getIdentity()->id;
                $offer->authorName = \Auth_StaffAdapter::getIdentity()->firstName . " "
                    . \Auth_StaffAdapter::getIdentity()->lastName;
            }
        } else {
            $offer->approved = 0;
        }
        $entityManagerLocale->persist($offer);
        $entityManagerLocale->flush();
        return $offer;
    }

    public static function addOffer($socialParameters)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $offer  = new \KC\Entity\Offer();
        $offer->shopOffers =  $entityManagerLocale->find('KC\Entity\Shop', Shop::checkShop(\FrontEnd_Helper_viewHelper::sanitize($socialParameters['shops'])));
        $offer->couponCode = \FrontEnd_Helper_viewHelper::sanitize($socialParameters['code']);
        $offer->endDate = new \DateTime(\FrontEnd_Helper_viewHelper::sanitize($socialParameters['expireDate']));
        $offer->startDate =  new \DateTime('now');
        $offer->userGenerated = true;
        if (\Auth_VisitorAdapter::hasIdentity()) {
            $offer->authorId = \FrontEnd_Helper_viewHelper::sanitize(\Auth_VisitorAdapter::getIdentity()->id);
            $offer->authorName =
                \FrontEnd_Helper_viewHelper::sanitize(\Auth_VisitorAdapter::getIdentity()->firstName). " "
                . \FrontEnd_Helper_viewHelper::sanitize(\Auth_VisitorAdapter::getIdentity()->lastName);
        }
        $offer->Visability = 'DE';
        $offer->discountType = 'CD';
        $offer->extendedoffertitle = '';
        $offer->extendedOffer = 0;
        $offer->extendedTitle = '';
        $offer->extendedUrl = '';
        $offer->extendedMetaDescription = '';
        $offer->extendedFullDescription = '';
        $offer->exclusiveCode= 0;
        $offer->editorPicks = 0;
        $offer->maxlimit = 0;
        $offer->maxcode = 0;
        $offer->shopExist = 0;
        $offer->deleted = 0;
        $offer->approved = true;
        $offer->offline = 0;
        $offer->created_at = new \DateTime('now');
        $offer->updated_at = new \DateTime('now');
        $entityManagerLocale->persist($offer);
        $entityManagerLocale->flush();

        if (isset($offer->id)) {
            $offerTerms  = new \KC\Entity\TermAndCondition();
            $offerTerms->content = \FrontEnd_Helper_viewHelper::sanitize($socialParameters['offerDetails']);
            $offerTerms->deleted = 0;
            $offerTerms->termandcondition = $entityManagerUser->find('KC\Entity\Offer', $offer->id);
            $offerTerms->created_at = new \DateTime('now');
            $offerTerms->updated_at = new \DateTime('now');
            \Zend_Registry::get('emLocale')->persist($offerTerms);
            \Zend_Registry::get('emLocale')->flush();
        }

        return true;
    }
}

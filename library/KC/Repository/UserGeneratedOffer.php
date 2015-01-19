<?php
namespace KC\Repository;

class UserGeneratedOffer extends \KC\Entity\Offer
{
    public static function getofferList($parameters)
    {
        $userRole           = \Auth_StaffAdapter::getIdentity()->users->id;
        $searchOffer        = $parameters["offerText"]!='undefined' ? $parameters["offerText"] : '';
        $searchShop         = $parameters["shopText"]!='undefined' ? $parameters["shopText"] : '';
        $searchCoupon       = @$parameters["shopCoupon"]!='undefined' ? @$parameters["shopCoupon"] : '';
        $searchCouponType   = $parameters["couponType"]!='undefined' ? $parameters["couponType"] : '';
        $deletedStatus      = $parameters['flag'];
        $entityManagerUser = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $getOffersQuery = $entityManagerUser
            ->from('KC\Entity\Offer', 'o')
            ->leftJoin('o.shopOffers', 's')
            ->where('o.deleted ='. $deletedStatus)
            ->andWhere("o.userGenerated = 1")
            ->andWhere("o.approved = '0'");
        if ($userRole=='4') {
            $getOffersQuery->andWhere('o.Visability ="DE"');
        }
        if ($searchOffer != '') {
            $getOffersQuery->andWhere("o.title LIKE '%$searchOffer%'");
        }
        if ($searchShop!='') {
            $getOffersQuery->andWhere("s.name LIKE '%$searchShop%'");
        }
        if ($searchCoupon!='') {
            $getOffersQuery->andWhere("o.couponcode LIKE ?", "%".$searchCoupon."%");
        }
        if ($searchCouponType!='') {
            $getOffersQuery->andWhere('o.discountType ='."'".$searchCouponType."'");
        }

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
           
        $offersList = $builder->getTable()->getResultQueryBuilder()->getQuery()->getArrayResult();
        $offersList = \DataTable_Helper::getResponse($offersList, $request);
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
            ->where($queryBuilder->expr()->eq('o.deleted', $queryBuilder->expr()->literal($flag)))
            ->andWhere('s.status = 1')
            ->andWhere($queryBuilder->expr()->like('s.name', $queryBuilder->expr()->literal($keyword.'%')))
            ->andWhere($queryBuilder->expr()->eq("o.userGenerated='1'"))
            ->orderBy("s.id", "ASC")
            ->setMaxResults(5)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return $shops;
    }

    public static function searchToFiveCoupon($keyword, $flag)
    {
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $coupons = $queryBuilder
            ->select('o.title as title')
            ->from("KC\Entity\Offer", "o")
            ->where($queryBuilder->expr()->eq('o.deleted', $queryBuilder->expr()->literal($flag)))
            ->andWhere($queryBuilder->expr()->like('o.couponcode', $queryBuilder->expr()->literal($keyword.'%')))
            ->andWhere("o.userGenerated = '1'")
            ->orderBy("s.id", "ASC")
            ->setMaxResults(5)
            ->fetchArray();
        return $coupons;
    }


    public static function saveApprovedStatus($offerId, $status)
    {
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $offer = $entityManagerLocale->find('\KC\Entity\Offer', $params['id']);
        if (!empty($status)) {
            $offer->approved = 1;
            $authorId = KC\Repository\Offer::getAuthorId($offerId);
            if (empty($authorId[0]['authorId'])) {
                $offer->authorId = Auth_StaffAdapter::getIdentity()->id;
                $offer->authorName = Auth_StaffAdapter::getIdentity()->firstName . " "
                    . Auth_StaffAdapter::getIdentity()->lastName;
            }
        } else {
            $offer->approved = 0;
        }
        $entityManagerLocale->persist($offer);
        $entityManagerLocale->flush();
        return $offer;
    }

    public static function addOffer($parameters)
    {
        $offer  = new \KC\Entity\Offer();
        $offer->nickname = $parameters['nickname'];
        $offer->title = $parameters['title'];
        $offer->offerUrl = $parameters['offerUrl'];
        $offer->couponCode = BackEnd_Helper_viewHelper::stripSlashesFromString($parameters['code']);
        $offer->startDate =  date('Y-m-d H:i:s');
        $offer->endDate = date('Y-m-d', strtotime($parameters['expireDate']));
        $offer->termandcondition[]->content = BackEnd_Helper_viewHelper::stripSlashesFromString(
            $parameters['offerDetails']
        );
        $offer->shopId = base64_decode($parameters['shopId']);
        $offer->userGenerated = true;

        if (Auth_VisitorAdapter::hasIdentity()) {
            $offer->authorId = Auth_VisitorAdapter::getIdentity()->id;
            $offer->authorName = Auth_VisitorAdapter::getIdentity()->firstName. " "
                . Auth_VisitorAdapter::getIdentity()->lastName;
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
        $offer->save();
        return true;
    }
}

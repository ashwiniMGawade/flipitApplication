<?php
/**
 * Offer
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class UserGeneratedOffer extends BaseOffer
{
    public static function getOffersList($parameters)
    {
        $userRole           = Auth_StaffAdapter::getIdentity()->roleId;
        $searchOffer        = $parameters["offerText"]!='undefined' ? $parameters["offerText"] : '';
        $searchShop         = $parameters["shopText"]!='undefined' ? $parameters["shopText"] : '';
        $searchCoupon       = @$parameters["shopCoupon"]!='undefined' ? @$parameters["shopCoupon"] : '';
        $searchCouponType   = $parameters["couponType"]!='undefined' ? $parameters["couponType"] : '';
        $deletedStatus      = $parameters['flag'];
        $getOffersQuery = Doctrine_Query::create()
            ->select(
                'o.id,o.id,o.title, s.name,s.accountManagerName as acName,o.totalViewcount as clicks,
                o.discountType,o.Visability,o.extendedOffer,o.startDate,o.endDate,authorName,o.refURL,o.couponcode'
            )
            ->from("UserGeneratedOffer o")
            ->leftJoin('o.shop s')
            ->where('o.deleted='.$deletedStatus)
            ->andWhere("o.userGenerated = 1")
            ->andWhere("o.approved = '0'");
        if ($userRole=='4') {
            $getOffersQuery->andWhere("o.Visability='DE'");
        }
        if ($searchOffer != '') {
            $getOffersQuery->andWhere("o.title LIKE ?", "%".$searchOffer."%");
        }
        if ($searchShop!='') {
            $getOffersQuery->andWhere("s.name LIKE ?", "%".$searchShop."%");
        }
        if ($searchCoupon!='') {
            $getOffersQuery->andWhere("o.couponcode LIKE ?", "%".$searchCoupon."%");
        }
        if ($searchCouponType!='') {
            $getOffersQuery->andWhere("o.discountType='".$searchCouponType."'");
        }
        $offersList = DataTable_Helper::generateDataTableResponse(
            $getOffersQuery,
            $parameters,
            array("__identifier" => 'o.id','o.title','s.name','o.discountType','o.refURL','o.couponcode','o.startDate',
                'o.endDate', 'clicks','authorName'),
            array(),
            array()
        );
        return $offersList;
    }

    public static function searchToFiveOffer($keyword, $flag)
    {
        $offers = Doctrine_Query::create()
        ->select('o.title as title')
        ->from("UserGeneratedOffer o")
        ->where('o.deleted=' . "'$flag'")
        ->andWhere('o.offline = 0')
        ->andWhere("o.title LIKE ?", "$keyword%")
        ->andWhere("o.userGenerated = '1'")
        ->orderBy("o.title ASC")->limit(5)->fetchArray();
        return $offers;
    }
    
    public static function searchToFiveShop($keyword, $flag)
    {
        $shops = Doctrine_Query::create()
        ->select('o.id,s.name as name')
        ->from("UserGeneratedOffer o")->leftJoin('o.shop s')
        ->where('o.deleted=' . "'$flag'")
        ->andWhere('s.status = 1')
        ->andWhere("s.name LIKE '".$keyword."%'")
        ->andWhere("o.userGenerated = '1'")
        ->orderBy("s.id ASC")->limit(5)->fetchArray();
        return $shops;
    }

    public static function searchToFiveCoupon($keyword, $flag)
    {
        $coupons = Doctrine_Query::create()
        ->select()
        ->from("UserGeneratedOffer o")
        ->where('o.deleted=' . "'$flag'")
        ->andWhere("o.couponcode LIKE ?", "$keyword%")
        ->andWhere("o.userGenerated = '1'")
        ->orderBy("o.id ASC")
        ->limit(5)
        ->fetchArray();
        return $coupons;
    }

    public static function saveApprovedStatus($offerId, $status)
    {
        $offer = Doctrine_Core::getTable("Offer")->find($offerId);
        if (!empty($status)) {
            $offer->approved = 1;
            $authorId = Offer::getAuthorId($offerId);
            if (empty($authorId[0]['authorId'])) {
                $offer->authorId = Auth_StaffAdapter::getIdentity()->id;
                $offer->authorName = Auth_StaffAdapter::getIdentity()->firstName . " "
                    . Auth_StaffAdapter::getIdentity()->lastName;
            }
        } else {
            $offer->approved = 0;
        }
        $offer->save();
        return $offer;
    }

    public static function addOffer($socialParameters)
    {
        $offer  = new UserGeneratedOffer();
        $offer->shopId = Shop::checkShop(FrontEnd_Helper_viewHelper::sanitize($socialParameters['shops']));
        $offer->couponCode = FrontEnd_Helper_viewHelper::sanitize($socialParameters['code']);
        $offer->termandcondition[]->content = BackEnd_Helper_viewHelper::stripSlashesFromString(
            FrontEnd_Helper_viewHelper::sanitize($socialParameters['offerDetails'])
        );
        $offer->endDate = date('Y-m-d', strtotime(FrontEnd_Helper_viewHelper::sanitize($socialParameters['expireDate'])));
        $offer->startDate =  date('Y-m-d H:i:s');
        $offer->userGenerated = true;
        if (Auth_VisitorAdapter::hasIdentity()) {
            $offer->authorId = FrontEnd_Helper_viewHelper::sanitize(Auth_VisitorAdapter::getIdentity()->id);
            $offer->authorName =
                FrontEnd_Helper_viewHelper::sanitize(Auth_VisitorAdapter::getIdentity()->firstName). " "
                . FrontEnd_Helper_viewHelper::sanitize(Auth_VisitorAdapter::getIdentity()->lastName);
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

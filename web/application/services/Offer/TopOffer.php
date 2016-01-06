<?php

class Application_Service_Offer_TopOffer extends Application_Service_Offer_OfferListing
{

    private $offerRepository;

    public function __construct($offerRepository, $limit)
    {
        $this->offerRepository = $offerRepository;
    }

    public function execute($limit)
    {
        $topCouponCodes = $this->offerRepository->getTopCouponCodes(array(), $limit, 'TOP50');
        $popularCode = [];
        foreach ($topCouponCodes as $coupon) {
            $popularCode[] = $coupon['popularcode']['id'];
        }
        if (count($topCouponCodes) < $limit) {
            $totalViewCountOffersLimit = $limit - count($topCouponCodes);
            $voucherCodes = $this->getOffersByType($limit, $totalViewCountOffersLimit, $popularCode);
            $topCouponCodes = $this->setVoucherCodesToTopCodes($voucherCodes, $topCouponCodes);
        }
        $topOffers = $this->traverseTopCouponCodes($topCouponCodes);
        return $topOffers;
    }

    private function getOffersByType($limit, $constraintLimit, $exitingCoupons = array())
    {
        $topVoucherCodes = $this->offerRepository->getOffers('totalViewCount', $constraintLimit,  null, $exitingCoupons);
        if (count($topVoucherCodes) < $constraintLimit) {
            $newestCodesLimit = $constraintLimit - count($topVoucherCodes);
            $newestVoucherCodes = $this->offerRepository->getOffers('newest', $newestCodesLimit, null, $exitingCoupons);
            $topVoucherCodes = $topVoucherCodes + $newestVoucherCodes;
        }

        return $topVoucherCodes;
    }

    private function setVoucherCodesToTopCodes($voucherCodes, $topCouponCodes)
    {
        foreach ($voucherCodes as $topVoucherCodeValue) {
            $shopId = isset($topVoucherCodeValue['shopOffers']['id'])
                ? $topVoucherCodeValue['shopOffers']['id']
                : '';
            $shopPermalink = isset($topVoucherCodeValue['shopOffers']['permaLink'])
                ? $topVoucherCodeValue['shopOffers']['permaLink']
                : '';
            $topCouponCodes[] = array(
                'id'=> $shopId,
                'position' => count($topCouponCodes)+1,
                'permaLink' => $shopPermalink,
                'popularcode' => $topVoucherCodeValue
            );
        }
        return $topCouponCodes;
    }

    private function traverseTopCouponCodes($topCouponCodes)
    {
        $topOffers = array();

        if (!empty($topCouponCodes)) {
            foreach ($topCouponCodes as $topCouponCodeValue) {
                $topOffers[] = array_merge(array('top50rank' => $topCouponCodeValue['position']), $topCouponCodeValue['popularcode']);
            }
        }

        return $topOffers;
    }
}

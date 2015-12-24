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
        $topCouponCodes = $this->offerRepository->getTopCouponCodes(array(), $limit);

        if (count($topCouponCodes) < $limit) {
            $totalViewCountOffersLimit = $limit - count($topCouponCodes);
            $voucherCodes = $this->getOffersByType($limit, $totalViewCountOffersLimit);
            $topCouponCodes = $this->setVoucherCodesToTopCodes($voucherCodes, $topCouponCodes);
        }
        $topOffers = $this->traverseTopCouponCodes($topCouponCodes);
        return $topOffers;
    }

    private function getOffersByType($limit, $constraintLimit)
    {
        $topVoucherCodes = $this->offerRepository->getOffers('totalViewCount', $constraintLimit);

        if (count($topVoucherCodes) < $constraintLimit) {
            $newestCodesLimit = $constraintLimit - count($topVoucherCodes);
            $newestVoucherCodes = $this->offerRepository->getOffers('newest', $newestCodesLimit);
            $topVoucherCodes = $topVoucherCodes + $newestVoucherCodes;
        }

        return $topVoucherCodes;
    }

    private function setVoucherCodesToTopCodes($voucherCodes, $topCouponCodes)
    {
        if (!empty($topCouponCodes)) {
            foreach ($voucherCodes as $topVoucherCodeValue) {
                $shopId = isset($topVoucherCodeValue['shopOffers']['id'])
                    ? $topVoucherCodeValue['shopOffers']['id']
                    : '';
                $shopPermalink = isset($topVoucherCodeValue['shopOffers']['permaLink'])
                    ? $topVoucherCodeValue['shopOffers']['permaLink']
                    : '';
                $topCouponCodes[] = array(
                    'id'=> $shopId,
                    'permaLink' => $shopPermalink,
                    'popularcode' => $topVoucherCodeValue
                );
            }
        }
        return $topCouponCodes;
    }

    private function traverseTopCouponCodes($topCouponCodes)
    {
        $topOffers = array();

        if (!empty($topCouponCodes)) {
            foreach ($topCouponCodes as $topCouponCodeValue) {
                $topOffers[] = $topCouponCodeValue['popularcode'];
            }
        }

        return $topOffers;
    }
}


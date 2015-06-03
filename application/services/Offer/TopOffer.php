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
        print_r($topCouponCodes); die;
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
                $topCouponCodes[$topVoucherCodeValue['shopOffers']['id']] = array(
                    'id'=> $topVoucherCodeValue['shopOffers']['id'],
                    'permaLink' => $topVoucherCodeValue['shopOffers']['permaLink'],
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


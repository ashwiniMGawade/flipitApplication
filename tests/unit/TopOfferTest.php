<?php
class TopOfferTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testTopOffers()
    {
        $offerListing = new Application_Service_Offer_TopOffer();
        $offerListing->getTopOffers(20);
    }

}
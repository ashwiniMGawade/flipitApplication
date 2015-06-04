<?php
use Codeception\Util\Stub;
class OfferListingTest extends \Codeception\TestCase\Test
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

    public function testClassExistence()
    {
        $offerListing = new Application_Service_Offer_OfferListing();
    }
}
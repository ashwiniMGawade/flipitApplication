<?php
class SimilarOfferTest extends \Codeception\TestCase\Test
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

    public function testClassExistance()
    {
        $similarOffer = new Application_Service_Offer_SimilarOffer();
    }

}
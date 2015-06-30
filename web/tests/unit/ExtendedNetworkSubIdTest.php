<?php
use Codeception\Util\Stub;

class ExtendedNetworkSubIdTest extends \Codeception\TestCase\Test
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

    public function testInsertExtendedSubId()
    {
        $this->persistExtendedNetworkSubId();
        $this->tester->seeInRepository('\Core\Domain\Entity\AffliateNetwork', ['extendedSubid'=>1234]);
    }

    public function testSavedExtendedSubId()
    {
        $id = $this->persistExtendedNetworkSubId();
        $networkInformation = $this->getNetworkInformation($id);
        $this->tester->assertEquals('1234', $networkInformation[0]['extendedSubid']);
    }

    private function persistExtendedNetworkSubId()
    {
        $entityManager = \Codeception\Module\Doctrine2::$em;
        $obj = new \Core\Domain\Entity\AffliateNetwork();
        $this->tester->persistEntity(
            $obj,
            array(
                'name' => 'zanox',
                'status' => 1,
                'deleted' => 0,
                'subId' => 'zpar0=[[A2ASUBID]]&zpar1=[[GOOGLEANALYTICSTRACKINCID]]',
                'extendedSubid' => 1234,
                'affliate_networks' => $entityManager->find('\Core\Domain\Entity\AffliateNetwork', 1),
                'affliatenetwork' => $entityManager->find('\Core\Domain\Entity\Shop', 1),
                'created_at' => new \DateTime('now'),
                'updated_at' => new \DateTime('now'),
            )
        );
        return $obj->__get('id');
    }

    private function getNetworkInformation($id)
    {
        $affiliateNetworkRepository = new KC\Repository\AffliateNetwork;
        $networkData = $affiliateNetworkRepository->getNetworkForEdit($id);
        return $networkData;
    }
    
}

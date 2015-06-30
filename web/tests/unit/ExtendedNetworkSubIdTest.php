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
        $networkInformation = $this->getNetworkInformation();
        $this->tester->assertEquals('1234', $networkInformation[0]['extendedSubid']);
    }

    private function persistExtendedNetworkSubId()
    {
        $entityManager = \Codeception\Module\Doctrine2::$em;
        $this->tester->persistEntity(
            new \Core\Domain\Entity\AffliateNetwork(),
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
    }

    private function getNetworkInformation()
    {
        $affiliateNetworkRepository = new KC\Repository\AffliateNetwork;
        $networkData = $affiliateNetworkRepository->getNetworkForEdit(1);
        return $networkData;
    }
    
}

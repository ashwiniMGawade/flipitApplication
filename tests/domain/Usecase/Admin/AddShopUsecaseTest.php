<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Shop;
use \Core\Domain\Entity\AffliateNetwork;
use \Core\Domain\Usecase\Admin\AddShopUsecase;

class CreateShopUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testCreateShopWithInvalidAffiliateNetwork()
    {
        $params = array(
            'affliateNetwork' => new AffliateNetwork()
        );
        $this->setExpectedException('Exception', 'Invalid affiliate network');
        $shopRepository = $this->shopRepositoryMock();
        (new AddShopUsecase(
            $shopRepository
        )
        )->execute(new Shop(), $params);
    }

    public function testCreateShopWithoutAffiliateNetwork()
    {
        $shopRepository = $this->shopRepositoryMock();
        (new AddShopUsecase(
            $shopRepository
        )
        )->execute(new Shop());
    }

    private function shopRepositoryMock()
    {
        $shopRepositoryMock = $this->getMock('\Core\Domain\Repository\ShopRepositoryInterface');
        return $shopRepositoryMock;
    }
}

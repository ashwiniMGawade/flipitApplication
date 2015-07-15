<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Shop;
use \Core\Domain\Usecase\Admin\GetShopUsecase;

class GetShopsUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testGetShopUsecaseWithIdNotExist()
    {
        $id = 0;
        $shopRepositoryMock = $this->createShopRepositoryMock();
        $shopRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo('\Core\Domain\Entity\Shop'), $this->equalTo(0));
        $this->setExpectedException('Exception', 'Shop not found');
        $shopUsecase = new GetShopUsecase($shopRepositoryMock);
        $shopUsecase->execute($id);
    }

    public function testGetShopUsecase()
    {
        $id = 1;
        $shop = new Shop();
        $shop->__set('id', 1);
        $shopRepositoryMock = $this->createShopRepositoryMock();
        $shopRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo('\Core\Domain\Entity\Shop'), $this->equalTo(1))
            ->willReturn($shop);
        $shopUsecase = new GetShopUsecase($shopRepositoryMock);
        $shopUsecase->execute($id);
    }

    public function testGetShopUsecaseWithInvalidId()
    {
        $id = 'invalid';
        $shopRepositoryMock = $this->createShopRepositoryMock();
        $this->setExpectedException('Exception', 'Invalid shop Id');
        $shopUsecase = new GetShopUsecase($shopRepositoryMock);
        $shopUsecase->execute($id);
    }

    private function createShopRepositoryMock()
    {
        $shopRepository = $this->getMockBuilder('\Core\Domain\Repository\ShopRepositoryInterface')->getMock();
        return $shopRepository;
    }
}

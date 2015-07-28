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
        $shopRepositoryMock = $this->createShopRepositoryWithFindMethodMock($id, 0);
        $shopUsecase = new GetShopUsecase($shopRepositoryMock);
        $this->setExpectedException('Exception', 'Shop not found');
        $shopUsecase->execute($id);
    }

    public function testGetShopUsecase()
    {
        $id = 1;
        $shop = new Shop();
        $shop->__set('id', $id);
        $shopRepositoryMock = $this->createShopRepositoryWithFindMethodMock($id, $shop);
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

    private function createShopRepositoryWithFindMethodMock($id, $returns)
    {
        $shopRepositoryMock = $this->createShopRepositoryMock();
        $shopRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo('\Core\Domain\Entity\Shop'), $this->equalTo($id))
            ->willReturn($returns);
        return $shopRepositoryMock;
    }
}

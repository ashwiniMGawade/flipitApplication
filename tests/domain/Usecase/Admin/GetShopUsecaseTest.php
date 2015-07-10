<?php
namespace Usecase\Admin;

use \Core\Domain\Usecase\Admin\GetShopUsecase;

class GetShopsUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testGetShopUsecase()
    {
        $id = 1;
        $shopRepositoryMock = $this->createShopRepositoryMock();
        $shopRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo('\Core\Domain\Entity\Shop'), $this->equalTo(1));
        $this->setExpectedException('Exception', 'Shop not found');
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

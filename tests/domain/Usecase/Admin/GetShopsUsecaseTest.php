<?php
namespace Usecase\Admin;

use Core\Domain\Entity\Shop;
use Core\Domain\Service\Purifier;
use Core\Domain\Usecase\Admin\GetShopsUsecase;
use Core\Service\Errors;

class GetShopsUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testGetShopsUsecaseTestWhenConditionsIsNotArray()
    {
        $conditions = 'INVALID';

        $errors = new Errors();
        $errors->setError('Invalid input, unable to find Shops.');

        $shopRepository = $this->createShopRepositoryMock();
        $result = (new GetShopsUsecase($shopRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());

    }

    public function testGetShopsUsecaseReturnsEmptyArrayWhenConditionsDoNotMatch()
    {
        $conditions = array(
            'title' => 'NO-SHOP-LIKE-THIS'
        );
        $expectedResult = array();

        $shopRepository = $this->createShopRepositoryWithFindByMethodMock($expectedResult);
        $result = (new GetShopsUsecase($shopRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertEquals(count($expectedResult), count($result));
    }

    public function testGetShopsUsecaseReturnsArrayOfShopsWhenConditionsMatch()
    {
        $conditions = array(
            'status' => 1
        );
        $shop = new Shop();
        $shop->setStatus(1);

        $expectedResult = array($shop);

        $shopRepository = $this->createShopRepositoryWithFindByMethodMock($expectedResult);
        $result = (new GetShopsUsecase($shopRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertEquals(count($expectedResult), count($result));
    }

    private function createShopRepositoryMock()
    {
        $shopRepository = $this->getMock('\Core\Domain\Repository\ShopRepositoryInterface');
        return $shopRepository;
    }

    private function createShopRepositoryWithFindByMethodMock($returns)
    {
        $shopRepository = $this->createShopRepositoryMock();
        $shopRepository->expects($this->once())
                       ->method('findBy')
                       ->with($this->equalTo('\Core\Domain\Entity\Shop'), $this->isType('array'))
                       ->willReturn($returns);
        return $shopRepository;
    }
}

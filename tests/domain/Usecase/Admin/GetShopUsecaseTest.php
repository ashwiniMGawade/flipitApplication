<?php
namespace Usecase\Admin;

use \Core\Domain\Usecase\Admin\GetShopUsecase;

class GetShopsUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testGetShopUsecase()
    {
        $id = 2;
        $shop = new GetShopUsecase($this->shopRepositoryMock());
        $shop->execute($id);
    }

    private function shopRepositoryMock()
    {
        $pageRepositoryMock = $this
            ->getMock('\Core\Domain\Repository\ShopRepositoryInterface');
        $pageRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo('\Core\Domain\Entity\Shop'), $this->equalTo(2));
        return $pageRepositoryMock;
    }
}

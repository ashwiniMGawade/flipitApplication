<?php
namespace Usecase\Admin;

use \Core\Domain\Usecase\Admin\CreateShopUsecase;

class CreateShopUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testCreateShopUsecase()
    {
        $createShopUsecase = new CreateShopUsecase($this->shopRepositoryMock());
        $createShopUsecase->execute();
    }

    private function shopRepositoryMock()
    {
        $shopRepositoryMock = $this->getMock('\Core\Domain\Repository\ShopRepositoryInterface');
        return $shopRepositoryMock;
    }
}

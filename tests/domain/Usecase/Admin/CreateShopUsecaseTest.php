<?php
namespace Usecase\Admin;

class CreateShopUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testCreateShopUsecase()
    {
        $createShopUsecase = new \Core\Domain\Usecase\Admin\CreateShopUsecase($this->shopRepositoryMock());
        $createShopUsecase->execute(new \Core\Domain\Entity\Shop());
    }

    private function shopRepositoryMock()
    {
        $pageRepositoryMock = $this->getMock('\Core\Domain\Repository\ShopRepositoryInterface');
        return $pageRepositoryMock;
    }
}

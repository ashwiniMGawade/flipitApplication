<?php
namespace Usecase\Admin;

use Core\Domain\Usecase\Admin\CreateShopUsecase;

class CreateShopUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testCreateApiKeyUsecase()
    {
        $this->assertInstanceOf(
            '\Core\Domain\Entity\Shop',
            (new CreateShopUsecase())->execute()
        );
    }
}

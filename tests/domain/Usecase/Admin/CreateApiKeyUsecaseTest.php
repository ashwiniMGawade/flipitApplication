<?php
namespace Usecase\Admin;

use Core\Domain\Usecase\Admin\CreateApiKeyUsecase;

class CreateApiKeyUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testCreateApiKeyUsecase()
    {
        $this->assertInstanceOf(
            '\Core\Domain\Entity\User\ApiKey',
            (new CreateApiKeyUsecase())->execute()
        );
    }
}

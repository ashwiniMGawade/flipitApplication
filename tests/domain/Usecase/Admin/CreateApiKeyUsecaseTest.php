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
        (new CreateApiKeyUsecase())->execute();
    }
}

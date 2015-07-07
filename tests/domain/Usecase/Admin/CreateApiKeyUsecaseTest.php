<?php
namespace Usecase\Admin;

use Core\Domain\Entity\User\ApiKey;
use Core\Domain\Service\ApiKeyGenerator;
use Core\Domain\Usecase\Admin\CreateApiKeyUsecase;

/**
 * Class CreateApiKeyUsecaseTest
 *
 * @package Usecase\Admin
 */
class CreateApiKeyUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    /**
     * @throws \Exception
     */
    public function testCreateApiKeyUsecase()
    {
        $apiKeyRepository = $this->createApiKeyRepositoryMock();
        (new CreateApiKeyUsecase($apiKeyRepository, new ApiKeyGenerator()))->execute(1);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createApiKeyRepositoryMock()
    {
        $apiKeyRepository = $this->getMockBuilder('\Core\Domain\Repository\ApiKeyRepositoryInterface')->getMock();
        $apiKeyRepository
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf('\Core\Domain\Entity\User\ApiKey'))
            ->with($this->equalTo());
        return $apiKeyRepository;
    }
}

<?php
namespace Usecase\Admin;

use \Core\Domain\Usecase\Admin\GetApiKeysUsecase;
use \Core\Domain\Repository\ApiKeyRepositoryInterface;

class GetApiKeysUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testGetApiKeysUsecaseTest()
    {
        $apiKeyRepository = $this->createApiKeyRepositoryMock();
        $GetApiKeyListing = new GetApiKeysUsecase($apiKeyRepository);
        $GetApiKeyListing->execute();
    }

    private function createApiKeyRepositoryMock()
    {
        $apiKeyRepository = $this->getMockBuilder('\Core\Domain\Repository\ApiKeyRepositoryInterface')->getMock();
        $apiKeyRepository->expects($this->once())->method('findBy');
        return $apiKeyRepository;
    }
}

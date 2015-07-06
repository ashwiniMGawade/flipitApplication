<?php
namespace Usecase\Admin;

use \Core\Domain\Usecase\Admin\GetApiKeyListingUsecase;
use \Core\Domain\Repository\ApiKeyRepositoryInterface;

class GetApiKeyListingUsecaseTest extends \Codeception\TestCase\Test
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

    public function testGetApiKeyListingUsecase()
    {
        $apiKeyRepository = $this->createApiKeyRepositoryMock();
        $GetApiKeyListing = new GetApiKeyListingUsecase($apiKeyRepository);
        $GetApiKeyListing->execute();
    }

    private function createApiKeyRepositoryMock()
    {
        $apiKeyRepository = $this->getMockBuilder('\Core\Domain\Repository\ApiKeyRepositoryInterface')
                                    ->getMock();

        $apiKeyRepository->expects($this->once())
                            ->method('findAll');

        return $apiKeyRepository;
    }
}

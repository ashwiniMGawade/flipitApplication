<?php
namespace Usecase\Admin;

use \Core\Domain\Usecase\Admin\GetApiKeyListingUsecase;
use \Core\Domain\Repository\ApiKeyRepositoryInterface;

/**
 * Class GetApiKeyListingUsecaseTest
 *
 * @package Usecase\Admin
 */
class GetApiKeyListingUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    /**
     *
     */
    public function testGetApiKeyListingUsecase()
    {
        $apiKeyRepository = $this->createApiKeyRepositoryMock();
        $GetApiKeyListing = new GetApiKeyListingUsecase($apiKeyRepository);
        $GetApiKeyListing->execute();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createApiKeyRepositoryMock()
    {
        $apiKeyRepository = $this->getMockBuilder('\Core\Domain\Repository\ApiKeyRepositoryInterface')->getMock();
        $apiKeyRepository->expects($this->once())->method('findBy');
        return $apiKeyRepository;
    }
}

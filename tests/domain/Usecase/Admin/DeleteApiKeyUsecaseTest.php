<?php
namespace Usecase\Admin;

use Core\Domain\Entity\User\ApiKey;
use Core\Domain\Usecase\Admin\DeleteApiKeyUsecase;

class DeleteApiKeyUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testDeleteApiKeyUsecaseReturnsErrorWhenIdNotEqualsInteger()
    {
        $apiKeyRepository = $this->createApiKeyRepositoryInterfaceMock(new ApiKey());
        $this->assertEquals('Invalid Id', (new DeleteApiKeyUsecase($apiKeyRepository))->execute('NOT_AN_INTEGER'));
    }

    public function testDeleteApiKeyUsecaseWhenIdEqualsInteger()
    {
        $apiKey = $this->createApiKeyMock();
        $apiKeyRepository = $this->createApiKeyRepositoryInterfaceWithMethodsMock($apiKey);
        $returnValue = (new DeleteApiKeyUsecase($apiKeyRepository))->execute(1);
        $this->assertInstanceOf('\Core\Domain\Entity\User\ApiKey', $returnValue);
        $this->assertObjectHasAttribute('deleted', $returnValue);
        $this->assertEquals(1, $returnValue->getDeleted());
    }

    private function createApiKeyRepositoryInterfaceMock()
    {
        $apiKeyRepository = $this->getMock('\Core\Domain\Repository\ApiKeyRepositoryInterface');
        return $apiKeyRepository;
    }

    private function createApiKeyRepositoryInterfaceWithMethodsMock($returns)
    {
        $apiKeyRepository = $this->createApiKeyRepositoryInterfaceMock();
        $apiKeyRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($returns);
        $apiKeyRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($returns);
        return $apiKeyRepository;
    }

    private function createApiKeyMock()
    {
        $apiKey = $this->getMockBuilder('\Core\Domain\Entity\User\ApiKey')->getMock();
        $apiKey
            ->expects($this->once())
            ->method('setDeleted');
        $apiKey
            ->expects($this->once())
            ->method('getDeleted')
            ->willReturn(1);
        return $apiKey;
    }
}

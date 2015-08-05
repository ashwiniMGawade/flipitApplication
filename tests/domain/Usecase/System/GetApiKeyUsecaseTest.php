<?php
namespace Usecase\System;

use \Core\Domain\Entity\User\ApiKey;
use \Core\Domain\Usecase\System\GetApiKeyUsecase;

class GetApiKeyUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetApiKeyUsecaseWithKeyNotExist()
    {
        $apiKeyRepositoryMock = $this->createApiKeyRepositoryWithFindOneByMethodMock(array('api_key'=>'ddfvfgfgf'),0);
        $apiKeyUsecase = new GetApiKeyUsecase($apiKeyRepositoryMock);
        $apiKeyUsecase->execute('dfdfdf');
    }

    private function createApiKeyRepositoryMock()
    {
        $apiKeyRepository = $this->getMockBuilder('\Core\Domain\Repository\ApiKeyRepositoryInterface')->getMock();
        return $apiKeyRepository;
    }

    private function createApiKeyRepositoryWithFindOneByMethodMock($condition, $returns)
    {
        $apiKeyRepositoryMock = $this->createApiKeyRepositoryMock();
        $apiKeyRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\ApiKey'), array('api_key'=>'dfdfdf'));
        return $apiKeyRepositoryMock;
    }
}

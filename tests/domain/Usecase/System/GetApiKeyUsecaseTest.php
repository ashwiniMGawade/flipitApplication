<?php
namespace Usecase\System;

use \Core\Domain\Entity\User\ApiKey;
use \Core\Domain\Usecase\System\GetApiKeyUsecase;
use \Core\Domain\Service\Purifier;

class GetApiKeyUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetApiKeyUsecaseWithKeyNotExist()
    {
        $apiKeyRepositoryMock = $this->createApiKeyRepositoryWithFindOneByMethodMock(array('api_key'=>'ddfvfgfgf'), 0);
        $apiKeyUsecase = new GetApiKeyUsecase($apiKeyRepositoryMock, new Purifier());
        $apiKeyUsecase->execute(array('api_key'=>'ddfvfgfgf'));
    }

    public function testGetApiKeyUsecaseWithValidKey()
    {
        $key = 'qa!w0%ngK7r#AZxanmXKh1XAv&oUDHh$';
        $apiKey = new ApiKey();
        $apiKey->setApiKey($key);
        $apiKeyRepositoryMock = $this->createApiKeyRepositoryWithFindOneByMethodMock(array('api_key'=>$key), $apiKey);
        $apiKeyUsecase = new GetApiKeyUsecase($apiKeyRepositoryMock, new Purifier());
        $apiKeyUsecase->execute(array('api_key'=>$key));
    }

    public function testGetApiKeyUsecaseWithInValidKeyCondition()
    {
        $apiKeyRepositoryMock = $this->createApiKeyRepositoryMock();
        $apiKeyUsecase = new GetApiKeyUsecase($apiKeyRepositoryMock, new Purifier());
        $this->setExpectedException('Exception', 'Invalid API key.');
        $apiKeyUsecase->execute(array('api_key'=>''));
    }

    public function testGetApiKeyUsecaseWithInValidCondition()
    {
        $apiKeyRepositoryMock = $this->createApiKeyRepositoryMock();
        $apiKeyUsecase = new GetApiKeyUsecase($apiKeyRepositoryMock, new Purifier());
        $this->setExpectedException('Exception', 'Invalid API key condition.');
        $apiKeyUsecase->execute(null);
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
            ->with($this->equalTo('\Core\Domain\Entity\User\ApiKey'), $condition)
            ->willReturn($returns);
        return $apiKeyRepositoryMock;
    }
}

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
        $apiKeyUsecase->execute('ddfvfgfgf');
    }

    public function testGetApiKeyUsecaseWithValidKey()
    {
        $key = 'qa!w0%ngK7r#AZxanmXKh1XAv&oUDHh$';
        $apiKey = new ApiKey();
        $apiKey->setApiKey($key);
        $apiKeyRepositoryMock = $this->createApiKeyRepositoryWithFindOneByMethodMock(array('api_key'=>$key),$apiKey);
        $apiKeyUsecase = new GetApiKeyUsecase($apiKeyRepositoryMock);
        $apiKeyUsecase->execute($key);
    }

    public function testGetApiKeyUsecaseWithInValidKey()
    {
        $apiKeyRepositoryMock = $this->createApiKeyRepositoryMock();
        $apiKeyUsecase = new GetApiKeyUsecase($apiKeyRepositoryMock);
        $this->setExpectedException('Exception', 'Invalid API key');
        $apiKeyUsecase->execute(NULL);
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

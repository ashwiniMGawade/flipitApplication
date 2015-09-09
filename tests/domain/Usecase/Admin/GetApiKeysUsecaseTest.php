<?php
namespace Usecase\Admin;

use Core\Domain\Entity\User\ApiKey;
use Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetApiKeysUsecase;
use \Core\Domain\Repository\ApiKeyRepositoryInterface;
use Core\Service\Errors;

class GetApiKeysUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testGetApiKeysUsecaseReturnsZeroWhenApiKeysDoesNotExist()
    {
        $expectedClickCount = 0;
        $apiKeyRepository = $this->createApiKeyRepositoryWithFindByMethodMock($expectedClickCount);
        $viewCounts = (new GetApiKeysUsecase($apiKeyRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals($expectedClickCount, $viewCounts);
    }

    public function testGetApiKeysUsecaseReturnsArrayWhenApiKeysExist()
    {
        $apiKey = new ApiKey();
        $expectedResult = array($apiKey);
        $apiKeyRepository = $this->createApiKeyRepositoryWithFindByMethodMock($expectedResult);
        $viewCounts = (new GetApiKeysUsecase($apiKeyRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($viewCounts));
    }

    public function testGetOfferClickUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $apiKeyRepository = $this->createApiKeyRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find ApiKeys.');
        $result = (new GetApiKeysUsecase($apiKeyRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createApiKeyRepositoryMock()
    {
        $viewCountRepository = $this->getMock('\Core\Domain\Repository\ApiKeyRepositoryInterface');
        return $viewCountRepository;
    }

    private function createApiKeyRepositoryWithFindByMethodMock($returns)
    {
        $viewCountRepository = $this->createApiKeyRepositoryMock();
        $viewCountRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('\Core\Domain\Entity\User\ApiKey'), $this->isType('array'))
            ->willReturn($returns);
        return $viewCountRepository;
    }
}

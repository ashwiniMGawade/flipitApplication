<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\User\ApiKey;
use \Core\Domain\Entity\User\User;
use \Core\Domain\Service\ApiKeyGenerator;
use \Core\Domain\Usecase\Admin\AddApiKeyUsecase;
use \Core\Domain\Validator\ApiKeyValidator;

/**
 * Class AddApiKeyUsecaseTest
 *
 * @package Usecase\Admin
 */
class AddApiKeyUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    /**
     * @throws \Exception
     */
    public function testCreateApiKeyWithInvalidUser()
    {
        $this->setExpectedException('Exception', 'Invalid User');
        $apiKeyRepository = $this->createApiKeyRepositoryMock();
        $validatorInterface = $this->createValidatorInterfaceMock();
        (new AddApiKeyUsecase(
            $apiKeyRepository,
            new ApiKeyValidator($validatorInterface),
            new ApiKeyGenerator()
        )
        )->execute(new ApiKey(), new User());
    }

    /**
     * @throws \Exception
     */
    public function testCreateApiKeyWithValidParametersAndUniqueApiKey()
    {
        $user = $this->createUserMock();
        $apiKeyRepository = $this->createApiKeyRepositoryWithFindByMethodMock(array());
        $apiKeyValidator = $this->createApiKeyValidatorMock(true);
        (new AddApiKeyUsecase(
            $apiKeyRepository,
            $apiKeyValidator,
            new ApiKeyGenerator()
        )
        )->execute(new ApiKey(), $user);
    }

    /**
     * @throws \Exception
     */
    public function testCreateApiKeyWithVAlidaParamsAndDuplicateApiKey()
    {
        $this->setExpectedException('Exception', 'Api Key already in use');
        $user = $this->createUserMock();
        $apiKeyRepository = $this->createApiKeyRepositoryWithFindByMethodMock(array('API_KEY_ALREADY_EXISTS'));
        $apiKeyValidator = $this->createApiKeyValidatorMock(true);
        (new AddApiKeyUsecase(
            $apiKeyRepository,
            $apiKeyValidator,
            new ApiKeyGenerator()
        )
        )->execute(new ApiKey(), $user);
    }

    /**
     * @throws \Exception
     */
    public function testPersistApiKey()
    {
        $user = $this->createUserMock();
        $apiKeyRepository = $this->createApiKeyRepositoryWithPersistMethodMock();
        $apiKeyValidator = $this->createApiKeyValidatorMock(true);
        (new AddApiKeyUsecase(
            $apiKeyRepository,
            $apiKeyValidator,
            new ApiKeyGenerator()
        )
        )->execute(new ApiKey(), $user);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createApiKeyRepositoryMock()
    {
        $apiKeyRepository = $this->getMockBuilder('\Core\Domain\Repository\ApiKeyRepositoryInterface')->getMock();
        return $apiKeyRepository;
    }

    /**
     * @param $returns
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createApiKeyRepositoryWithFindByMethodMock($returns)
    {
        $apiKeyRepository = $this->createApiKeyRepositoryMock();
        $apiKeyRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn($returns);
        return $apiKeyRepository;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createApiKeyRepositoryWithPersistMethodMock()
    {
        $apiKeyRepository = $this->createApiKeyRepositoryMock();
        $apiKeyRepository
            ->expects($this->once())
            ->method('persist');
        return $apiKeyRepository;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createValidatorInterfaceMock()
    {
        $mockValidatorInterface = $this->getMock('\Core\Domain\Adapter\ValidatorInterface');
        return $mockValidatorInterface;
    }

    /**
     * @param $returns
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createApiKeyValidatorMock($returns)
    {
        $mockApiKeyValidator = $this->getMockBuilder('\Core\Domain\Validator\ApiKeyValidator')
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $mockApiKeyValidator->expects($this->once())
                            ->method('validate')
                            ->with($this->isInstanceOf('\Core\Domain\Entity\User\ApiKey'))
                            ->willReturn($returns);
        return $mockApiKeyValidator;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createUserMock()
    {
        $user = $this->getMockBuilder('\Core\Domain\Entity\User\User')->getMock();
        $user
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        return $user;
    }
}

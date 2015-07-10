<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\User\User;
use \Core\Domain\Repository\ApiKeyRepositoryInterface;
use \Core\Domain\Entity\User\ApiKey;
use \Core\Domain\Service\KeyGenerator;
use \Core\Domain\Validator\ApiKeyValidator;

/**
 * Class AddApiKeyUsecase
 *
 * @package Core\Domain\Usecase\Admin
 */
class AddApiKeyUsecase
{
    /**
     * @var \Core\Domain\Repository\ApiKeyRepositoryInterface
     */
    private $apiKeyRepository;

    /**
     * @var \Core\Domain\Validator\ApiKeyValidator
     */
    protected $apiKeyValidator;

    /**
     * @var \Core\Domain\Service\KeyGenerator
     */
    protected $keyGenerator;

    /**
     * @param \Core\Domain\Repository\ApiKeyRepositoryInterface $apiKeyRepository
     */
    public function __construct(
        ApiKeyRepositoryInterface $apiKeyRepository,
        ApiKeyValidator $apiKeyValidator,
        KeyGenerator $keyGenerator
    ) {
        $this->apiKeyRepository = $apiKeyRepository;
        $this->apiKeyValidator = $apiKeyValidator;
        $this->keyGenerator = $keyGenerator;
    }

    /**
     * @param $properties
     *
     * @return mixed
     * @throws \Exception
     */
    public function execute(ApiKey $apiKey, User $user)
    {
        if (!$user->getId()) {
            throw new \Exception('Invalid User');
        }
        $apiKey->setApiKey($this->keyGenerator->generate(32));
        $apiKey->setUserId($user);
        $apiKey->setCreatedAt(new \DateTime());
        $apiKey->setDeleted(0);

        $validationResult = $this->apiKeyValidator->validate($apiKey);
        if ($validationResult !== true) {
            return $validationResult;
        }

        $keyExists = $this->apiKeyRepository->findBy(
            '\Core\Domain\Entity\User\ApiKey',
            array('api_key' => $apiKey->getApiKey())
        );
        if (count($keyExists)) {
            throw new \Exception('Api Key already in use');
        }
        return $this->apiKeyRepository->persist($apiKey);
    }
}

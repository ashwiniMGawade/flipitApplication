<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\ApiKeyRepositoryInterface;
use \Core\Domain\Entity\User\ApiKey;

/**
 * Class CreateApiKeyUsecase
 *
 * @package Core\Domain\Usecase\Admin
 */
class CreateApiKeyUsecase
{
    /**
     * @var \Core\Domain\Repository\ApiKeyRepositoryInterface
     */
    private $apiKeyRepository;

    /**
     * @param \Core\Domain\Repository\ApiKeyRepositoryInterface $apiKeyRepository
     */
    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository)
    {
        $this->apiKeyRepository = $apiKeyRepository;
    }

    /**
     * @param $properties
     *
     * @return mixed
     * @throws \Exception
     */
    public function execute($properties)
    {
        if (empty($properties)) {
            throw new \Exception('The Properties cannot be empty');
        }
        $apiKey = new ApiKey();
        $apiKey->__set('api_key', $properties['api_key']);
        $apiKey->__set('user_id', $properties['user']);
        $apiKey->__set('created_at', $properties['created_at']);
        $apiKey->__set('deleted', $properties['deleted']);
        return $this->apiKeyRepository->persist($apiKey);
    }
}

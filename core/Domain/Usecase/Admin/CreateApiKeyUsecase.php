<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Repository\ApiKeyRepositoryInterface;
use Core\Domain\Entity\User\ApiKey;

class CreateApiKeyUsecase
{
    private $apiKeyRepository;

    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository)
    {
        $this->apiKeyRepository = $apiKeyRepository;
    }

    public function execute($user)
    {
        $apiKey = new ApiKey();
        $apiKey->__set('api_key', $this->generateApiKey());
        $apiKey->__set('user_id', $user);
        $apiKey->__set('created_at', new \DateTime());
        $apiKey->__set('deleted', 0);
        return $this->apiKeyRepository->persist($apiKey);
    }

    private function generateApiKey($length = 32)
    {
        $apiKey = '';
        $allowedCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_';
        $charactersLength = strlen($allowedCharacters);
        for ($i = 0; $i < $length; $i++) {
            $apiKey .= $allowedCharacters[rand(0, $charactersLength - 1)];
        }
        return $apiKey;
    }
}

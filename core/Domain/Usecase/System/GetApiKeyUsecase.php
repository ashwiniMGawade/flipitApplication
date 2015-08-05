<?php
namespace Core\Domain\Usecase\System;

use \Core\Domain\Repository\ApiKeyRepositoryInterface;

class GetApiKeyUsecase
{
    private $apiKeyRepository;

    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository)
    {
        $this->apiKeyRepository = $apiKeyRepository;
    }

    public function execute($key)
    {
        if (is_null($key)) {
            throw new \Exception('Invalid API key.');
        }
        
        return $this->apiKeyRepository->findOneBy('\Core\Domain\Entity\User\ApiKey', array('api_key' => $key));
    }
}

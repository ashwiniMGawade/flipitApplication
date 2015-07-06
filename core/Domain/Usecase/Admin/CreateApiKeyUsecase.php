<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Repository\ApiKeyRepositoryInterface;

class CreateApiKeyUsecase
{
    private $apiKeyRepository;

    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository)
    {
        $this->apiKeyRepository = $apiKeyRepository;
    }

    public function execute()
    {
        return $this->apiKeyRepository->persist('\Core\Domain\Entity\User\ApiKey');
    }
}

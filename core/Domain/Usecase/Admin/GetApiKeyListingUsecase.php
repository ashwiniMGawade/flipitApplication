<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Repository\ApiKeyRepositoryInterface;

class GetApiKeyListingUsecase
{
    private $apiKeyRepository;

    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository)
    {
        $this->apiKeyRepository = $apiKeyRepository;
    }

    public function execute()
    {
        return $this->apiKeyRepository->findAll('\Core\Domain\Entity\User\ApiKey');
    }
}

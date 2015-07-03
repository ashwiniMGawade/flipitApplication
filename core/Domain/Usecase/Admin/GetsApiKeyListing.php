<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Repository\ApiKeyRepositoryInterface;

class GetsApiKeyListing
{
    private $apiKeyRepository;

    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository)
    {
        $this->apiKeyRepository = $apiKeyRepository;
    }

    public function execute()
    {
        return $this->apiKeyRepository->getAll();
    }
}

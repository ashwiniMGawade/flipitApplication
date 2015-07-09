<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\ApiKeyRepositoryInterface;

class DeleteApiKeyUsecase
{
    private $apiKeyRepository;

    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository)
    {
        $this->apiKeyRepository = $apiKeyRepository;
    }

    public function execute($id)
    {
        $apiKey = $this->apiKeyRepository->find('\Core\Domain\Entity\User\ApiKey', $id);

        $apiKey->setDeleted(1);
        return $this->apiKeyRepository->persist($apiKey);
    }
}

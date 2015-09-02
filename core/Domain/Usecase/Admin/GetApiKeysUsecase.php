<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\ApiKeyRepositoryInterface;

class GetApiKeysUsecase
{
    private $apiKeyRepository;

    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository)
    {
        $this->apiKeyRepository = $apiKeyRepository;
    }

    public function execute()
    {
        return $this->apiKeyRepository->findBy('\Core\Domain\Entity\User\ApiKey', array('deleted' => 0));
    }
}

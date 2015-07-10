<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\ApiKeyRepositoryInterface;

/**
 * Class GetApiKeyListingUsecase
 *
 * @package Core\Domain\Usecase\Admin
 */
class GetApiKeyListingUsecase
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
     * @return mixed
     */
    public function execute()
    {
        return $this->apiKeyRepository->findBy('\Core\Domain\Entity\User\ApiKey', array('deleted' => 0));
    }
}

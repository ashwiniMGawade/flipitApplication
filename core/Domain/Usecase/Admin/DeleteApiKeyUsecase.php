<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\ApiKeyRepositoryInterface;

/**
 * Class DeleteApiKeyUsecase
 *
 * @package Core\Domain\Usecase\Admin
 */
class DeleteApiKeyUsecase
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
     * @param $id
     *
     * @return mixed
     */
    public function execute($id)
    {
        if (!is_int($id)) {
            return('Invalid Id');
        }
        $apiKey = $this->apiKeyRepository->find('\Core\Domain\Entity\User\ApiKey', $id);
        $apiKey->setDeleted(1);
        return $this->apiKeyRepository->save($apiKey);
    }
}

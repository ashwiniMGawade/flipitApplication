<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\ApiKeyRepositoryInterface;
use Core\Service\Errors\ErrorsInterface;

class GetApiKeysUsecase
{
    private $apiKeyRepository;

    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->apiKeyRepository = $apiKeyRepository;
        $this->htmlPurifier = $htmlPurifier;
        $this->errors = $errors;
    }

    public function execute($conditions = array())
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find ApiKeys.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);
        return $this->apiKeyRepository->findBy('\Core\Domain\Entity\User\ApiKey', $conditions);
    }
}

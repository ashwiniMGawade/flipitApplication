<?php
namespace Core\Domain\Usecase\System;

use \Core\Domain\Repository\ApiKeyRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;

class GetApiKeyUsecase
{
    private $apiKeyRepository;

    protected $htmlPurifier;

    public function __construct(ApiKeyRepositoryInterface $apiKeyRepository, PurifierInterface $htmlPurifier)
    {
        $this->apiKeyRepository = $apiKeyRepository;
        $this->htmlPurifier     = $htmlPurifier;
    }

    public function execute($condition)
    {
        $condition = $this->htmlPurifier->purify($condition);

        if (!is_array($condition)) {
            throw new \Exception('Invalid API key condition.');
        }

        if (isset($condition['api_key']) && strlen($condition['api_key'])<1) {
            throw new \Exception('Invalid API key.');
        }

        if (isset($condition['api_key'])) {
            $condition['api_key'] = html_entity_decode($condition['api_key']);
        }
        
        return $this->apiKeyRepository->findOneBy('\Core\Domain\Entity\User\ApiKey', $condition);
    }
}

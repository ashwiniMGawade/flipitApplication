<?php
namespace Core\Persistence\Database\Repository;

use Core\Domain\Repository\ApiKeyRepositoryInterface;
use Core\Domain\Entity\User\IpAddresses;
use Doctrine\ORM\EntityManager;

class ApiKeyRepository implements ApiKeyRepositoryInterface
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAll()
    {
        return $this->entityManager->getRepository('Core\Domain\Entity\User\IpAddresses')->findAll();
    }
}

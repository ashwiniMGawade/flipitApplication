<?php
namespace Core\Persistence\Database\Repository;

use Core\Domain\Repository\ApiKeyRepositoryInterface;
use Core\Domain\Entity\User\IpAddresses;
use Doctrine\ORM\QueryBuilder;

class ApiKeyRepository implements ApiKeyRepositoryInterface
{
    protected $entity = 'Core\Domain\Entity\User\IpAddresses';
    protected $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getAll()
    {
        return $this->queryBuilder
                    ->select("ip")
                    ->from($this->entity, "ip")
                    ->getQuery()
                    ->getResult();
    }
}

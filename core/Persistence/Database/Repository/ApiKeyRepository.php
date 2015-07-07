<?php
namespace Core\Persistence\Database\Repository;

use Core\Domain\Repository\ApiKeyRepositoryInterface;

class ApiKeyRepository extends BaseRepository implements ApiKeyRepositoryInterface
{
    public function persist($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}

<?php
namespace Core\Persistence\Database\Repository;

use Core\Domain\Repository\ApiKeyRepositoryInterface;

/**
 * Class ApiKeyRepository
 *
 * @package Core\Persistence\Database\Repository
 */
class ApiKeyRepository extends BaseRepository implements ApiKeyRepositoryInterface
{
    /**
     * @param $entity
     */
    public function persist($entity)
    {
        $this->em->merge($entity);
        $this->em->flush();
    }
}

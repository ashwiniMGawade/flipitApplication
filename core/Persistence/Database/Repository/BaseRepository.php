<?php

namespace Core\Persistence\Database\Repository;

use \Core\Domain\Repository\BaseRepositoryInterface;

class BaseRepository implements BaseRepositoryInterface
{
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }
    
    public function findOneBy($entity, array $conditions)
    {
        return $this->em->getRepository($entity)->findOneBy($conditions);
    }

    public function find($entity, $id)
    {
        return $this->em->getRepository($entity)->find($id);
    }

    public function findAll($entity)
    {
        return $this->em->getRepository($entity)->findAll();
    }

    public function findBy($entity, array $conditions)
    {
        return $this->em->getRepository($entity)->findBy($conditions);
    }
}

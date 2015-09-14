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

    public function findBy($entity, $conditions = array(), $order = array(), $limit = null, $offset = null)
    {
        return $this->em->getRepository($entity)->findBy($conditions, $order, $limit, $offset);
    }

    public function save($entity)
    {
        $entity = $this->em->merge($entity);
        $this->em->flush();
        return $entity;
    }

    public function remove($entity)
    {
        $result = $this->em->remove($entity);
        $this->em->flush();
        return $result;
    }
}

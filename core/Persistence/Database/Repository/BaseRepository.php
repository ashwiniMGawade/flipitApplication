<?php

namespace Core\Persistence\Database\Repository;

use \Core\Domain\Repository\BaseRepositoryInterface;
use \Doctrine\ORM\Tools\Pagination\Paginator;

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
        $entity = $this->em->merge($entity);
        $result = $this->em->remove($entity);
        $this->em->flush();
        return $result;
    }

    public function findAllPaginated($entity, $conditions = array(), $order = array(), $limit = 100, $offset = 0)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $queryBuilder
            ->select('table')
            ->from($entity, 'table')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if (false == empty($order)) {
            $orderField = key($order);
            $queryBuilder->orderBy("table.$orderField", $order[$orderField]);
        }
        $conditionsCount = 1;
        foreach ($conditions as $field => $value) {
            if ($conditionsCount == 1) {
                $queryBuilder->where("table.$field='$value'");
            } else {
                $queryBuilder->andWhere("table.$field='$value'");
            }
            $conditionsCount++;
        }
        $query = $queryBuilder->getQuery();

        $results['records'] = $query->getResult();
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $results['count'] = count($paginator);
        return  $results;
    }
}

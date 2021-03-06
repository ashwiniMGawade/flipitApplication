<?php

namespace Core\Domain\Repository;

interface BaseRepositoryInterface
{
    public function findOneBy($entity, array $conditions);
    public function find($entity, $id);
    public function findAll($entity);
    public function findBy($entity, $conditions = array(), $order = array(), $limit = null, $offset = null);
    public function save($entity);
    public function remove($entity);
    public function findAllPaginated($entity, $conditions = array(), $order = array(), $limit = null, $offset = null);
}

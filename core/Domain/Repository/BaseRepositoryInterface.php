<?php

namespace Core\Domain\Repository;

interface BaseRepositoryInterface
{
    public function findOneBy($entity, array $conditions);
    public function find($entity, $id);
    public function findAll($entity);
    public function findBy($entity, array $conditions);
    public function save($entity);
}

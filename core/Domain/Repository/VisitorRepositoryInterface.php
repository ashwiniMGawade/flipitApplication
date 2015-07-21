<?php
namespace Core\Domain\Repository;

interface VisitorRepositoryInterface extends BaseRepositoryInterface
{
    public function findVisitors($entity, $conditions, $options);
}

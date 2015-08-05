<?php
namespace Core\Domain\Repository;

interface VisitorRepositoryInterface extends BaseRepositoryInterface
{
    public function findVisitors($conditions, $options);
    public function deactivate($conditions);
}

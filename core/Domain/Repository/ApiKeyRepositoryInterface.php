<?php
namespace Core\Domain\Repository;

interface ApiKeyRepositoryInterface extends BaseRepositoryInterface
{
    public function persist($entity);
}

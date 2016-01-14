<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\BulkEmail;

interface BulkEmailRepositoryInterface
{
    public function save(BulkEmail $entity);
}

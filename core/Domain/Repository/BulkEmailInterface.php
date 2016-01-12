<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\BulkEmail;

interface BulkEmailInterface
{
    public function save(BulkEmail $entity);
}

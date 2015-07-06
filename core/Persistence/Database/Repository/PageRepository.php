<?php

namespace Core\Persistence\Database\Repository;

use \Core\Domain\Repository\PageRepositoryInterface;
use \Core\Domain\Entity\Page;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }
}

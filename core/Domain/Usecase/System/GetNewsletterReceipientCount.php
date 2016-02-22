<?php
namespace Core\Domain\Usecase\System;

use Core\Domain\Repository\VisitorRepositoryInterface;

class GetNewsletterReceipientCount
{
    public function __construct(VisitorRepositoryInterface $visitorRepository)
    {
        $this->visitorRepository = $visitorRepository;
    }

    public function execute()
    {
        $noOfReceipeints = $this->visitorRepository->getNewsletterReceipientCount();
        return $noOfReceipeints;
    }
}

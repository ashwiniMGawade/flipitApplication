<?php
namespace Core\Domain\Usecase\System;

use Core\Domain\Repository\VisitorRepositoryInterface;

class DeactivateSleepingVisitors
{
    public function __construct(VisitorRepositoryInterface $visitorRepository)
    {
        $this->visitorRepository = $visitorRepository;
    }

    public function execute()
    {
        $noOfDeactivatedVisitors = $this->visitorRepository->deactivateSleeper(
            array(
                'lastEmailOpenDate' => strtotime('-3 months')
            )
        );
        return $noOfDeactivatedVisitors;
    }
}

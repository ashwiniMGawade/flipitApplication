<?php
namespace Core\Domain\Usecase\System;

use Core\Domain\Repository\VisitorRepositoryInterface;

class SleepingInactiveVisitorsUsecase
{
    public function __construct(VisitorRepositoryInterface $visitorRepository)
    {
        $this->visitorRepository = $visitorRepository;
    }

    public function execute()
    {
        $noOfInactivatedVisitors = $this->visitorRepository->deactivate(
            array(
                'lastEmailOpenDate' => strtotime('-3 months')
            )
        );
        return $noOfInactivatedVisitors;
    }
}

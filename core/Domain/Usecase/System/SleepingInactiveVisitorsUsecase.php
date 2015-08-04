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
        $visitorData = $this->visitorRepository->deactivate(
            array(
                'deleted' => 0,
                'lastEmailOpenDate' => strtotime('-3 months')
            )
        );
    }
}

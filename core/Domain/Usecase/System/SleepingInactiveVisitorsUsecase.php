<?php
namespace Core\Domain\Usecase\System;

class SleepingInactiveVisitorsUsecase
{

    public function __construct(VisitorRepositoryInterface $visitorRepository)
    {
        $this->visitorRepository = $visitorRepository;
    }

    public function execute()
    {

    }
}

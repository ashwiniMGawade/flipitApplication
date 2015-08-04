<?php
namespace Usecase\System;

use Core\Domain\Usecase\System\SleepingInactiveVisitorsUsecase;

class SleepingInactiveVisitorsUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testSleepingInactiveVisitorsUsecase()
    {
        (new SleepingInactiveVisitorsUsecase($this->createVisitorRepositoryInterfaceWithUpdateVisitorsMethodMock()))->execute();
    }

    private function createVisitorRepositoryInterfaceMock()
    {
        return $this->getMock('\Core\Domain\Repository\VisitorRepositoryInterface');
    }

    private function createVisitorRepositoryInterfaceWithUpdateVisitorsMethodMock()
    {
        $visitorRepository = $this->createVisitorRepositoryInterfaceMock();
        $visitorRepository->expects($this->once())
            ->method('deactivate')
            ->with($this->isType('array'));
        return $visitorRepository;
    }
}

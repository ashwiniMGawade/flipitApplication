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
        (new SleepingInactiveVisitorsUsecase(
            $this->createVisitorRepositoryInterfaceWithDeactivateMethodMock()
        ))->execute();
    }

    private function createVisitorRepositoryInterfaceWithDeactivateMethodMock()
    {
        $visitorRepository = $this->getMock('\Core\Domain\Repository\VisitorRepositoryInterface');
        $visitorRepository->expects($this->once())
            ->method('deactivate')
            ->with($this->isType('array'))
            ->willReturn(20);
        return $visitorRepository;
    }
}

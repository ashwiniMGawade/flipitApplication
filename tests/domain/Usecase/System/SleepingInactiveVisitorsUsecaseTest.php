<?php
namespace Usecase\System;

use Core\Domain\Usecase\System\DeactivateSleepingVisitors;

class DeactivateSleepingVisitorsTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testDeactivateSleepingVisitors()
    {
        (new DeactivateSleepingVisitors(
            $this->createVisitorRepositoryInterfaceWithDeactivateMethodMock()
        ))->execute();
    }

    private function createVisitorRepositoryInterfaceWithDeactivateMethodMock()
    {
        $visitorRepository = $this->getMock('\Core\Domain\Repository\VisitorRepositoryInterface');
        $visitorRepository->expects($this->once())
            ->method('deactivateSleeper')
            ->with($this->isType('array'))
            ->willReturn(20);
        return $visitorRepository;
    }
}

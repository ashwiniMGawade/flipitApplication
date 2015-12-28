<?php
namespace Usecase\System;

use Core\Domain\Usecase\System\GetNewsletterReceipientCount;

class GetNewsletterReceipientCountUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testGetNewsletterReceipientCount()
    {
        (new GetNewsletterReceipientCount(
            $this->createVisitorRepositoryInterfaceWithGetNewsletterReceipientCountMethodMock()
        ))->execute();
    }

    private function createVisitorRepositoryInterfaceWithGetNewsletterReceipientCountMethodMock()
    {
        $visitorRepository = $this->getMock('\Core\Domain\Repository\VisitorRepositoryInterface');
        $visitorRepository->expects($this->once())
            ->method('getNewsletterReceipientCount')
            ->willReturn(array('receipients'=> 20));
        return $visitorRepository;
    }
}

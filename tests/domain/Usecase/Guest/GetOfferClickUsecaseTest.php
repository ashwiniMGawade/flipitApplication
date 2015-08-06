<?php
namespace Usecase\Guest;

use Core\Domain\Usecase\Guest\GetOfferClickUsecase;
use Core\Persistence\Database\Repository\ViewCountRepository;

class GetOfferClickUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    // tests
    public function testGetOfferClickUsecase()
    {
        $offerId = 5101;
        $clientIp = 3232249857;
        $expectedClickCount = 0;
        $viewCountRepository = $this->createViewCountRepositoryMock($expectedClickCount);
        $clickCount = (new GetOfferClickUsecase($viewCountRepository))->execute($offerId, $clientIp);
        $this->assertEquals($expectedClickCount, $clickCount);
    }

    private function createViewCountRepositoryMock($returns)
    {
        $viewCountRepository = $this->getMock('Core\Domain\Repository\ViewCountRepositoryInterface');
        $viewCountRepository->expects($this->once())
                            ->method('getOfferClickCount')
                            ->with($this->isType('integer'), $this->isType('integer'))
                            ->willReturn($returns);
        return $viewCountRepository;
    }
}

<?php
namespace Usecase\Guest;

use Core\Domain\Entity\Offer;
use Core\Domain\Entity\ViewCount;
use Core\Domain\Usecase\Guest\SaveOfferClickUsecase;

class SaveOfferClickUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    // tests
    public function testSaveOfferClickUsecase()
    {
        $offer = new Offer();
        $offer->id = 5101;
        $clientIp = 3232249857;
        $viewCountRepository = $this->createViewCountRepositoryMock();
        (new SaveOfferClickUsecase($viewCountRepository))->execute(new ViewCount(), $offer, $clientIp);
    }

    private function createViewCountRepositoryMock()
    {
        $viewCountRepository = $this->getMock('Core\Domain\Repository\ViewCountRepositoryInterface');
        $viewCountRepository->expects($this->once())
                            ->method('save')
                            ->with($this->isInstanceOf('\Core\Domain\Entity\ViewCount'));
        return $viewCountRepository;
    }
}

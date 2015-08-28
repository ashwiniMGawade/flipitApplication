<?php
namespace Usecase\Guest;

use Core\Domain\Entity\Offer;
use Core\Domain\Entity\ViewCount;
use Core\Domain\Usecase\Guest\AddOfferClickUsecase;

class AddOfferClickUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    // tests
    public function testAddOfferClickUsecase()
    {
        $offer = new Offer();
        $offer->id = 5101;
        $clientIp = 3232249857;
        $viewCountRepository = $this->createViewCountRepositoryMock();
        (new AddOfferClickUsecase($viewCountRepository))->execute(new ViewCount(), $offer, $clientIp);
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

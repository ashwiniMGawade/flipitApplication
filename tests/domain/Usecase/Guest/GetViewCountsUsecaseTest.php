<?php
namespace Usecase\Guest;

use Core\Domain\Entity\Offer;
use Core\Domain\Entity\ViewCount;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Guest\GetViewCountsUsecase;
use \Core\Persistence\Database\Repository\ViewCountRepository;
use \Core\Service\Errors;

class GetViewCountsUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    // tests
    public function testGetOfferClickUsecaseReturnsZeroWhenViewCountDoesNotExist()
    {
        $offer = new Offer();
        $offer->__set('id', 51010);
        $conditions = array(
            'viewcount' => $offer,
            'IP' => 3232249857
        );

        $expectedClickCount = 0;
        $viewCountRepository = $this->createViewCountRepositoryWithFindByMethodMock($expectedClickCount);
        $viewCounts = (new GetViewCountsUsecase($viewCountRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertEquals($expectedClickCount, $viewCounts);
    }

    public function testGetOfferClickUsecaseReturnsArrayWhenViewCountExists()
    {
        $offer = new Offer();
        $offer->__set('id', 100);

        $viewCount = new ViewCount();
        $viewCount->__set('id', 100);
        $viewCount->__set('IP', 3232249857);
        $viewCount->__set('viewcount', $offer);

        $conditions = array(
            'viewcount' => $offer,
            'IP' => 3232249857
        );

        $expectedResult = array($viewCount);

        $viewCountRepository = $this->createViewCountRepositoryWithFindByMethodMock($expectedResult);
        $viewCounts = (new GetViewCountsUsecase($viewCountRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertEquals(count($expectedResult), count($viewCounts));
    }

    public function testGetOfferClickUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $viewCountRepository = $this->createViewCountRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find ViewCount.');
        $result = (new GetViewCountsUsecase($viewCountRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createViewCountRepositoryMock()
    {
        $viewCountRepository = $this->getMock('Core\Domain\Repository\ViewCountRepositoryInterface');
        return $viewCountRepository;
    }

    private function createViewCountRepositoryWithFindByMethodMock($returns)
    {
        $viewCountRepository = $this->createViewCountRepositoryMock();
        $viewCountRepository->expects($this->once())
                            ->method('findBy')
                            ->with($this->equalTo('\Core\Domain\Entity\ViewCount'), $this->isType('array'))
                            ->willReturn($returns);
        return $viewCountRepository;
    }
}

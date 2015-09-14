<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\LandingPages;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetLandingPagesUsecase;
use \Core\Service\Errors;

class GetLandingPagesUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetLandingPagesUsecaseReturnsZeroWhenLandingPageDoesNotExist()
    {
        $expectedLandingPages = 0;
        $landingPageRepository = $this->createLandingPagesRepositoryWithFindByMethodMock($expectedLandingPages);
        $landingPages = (new GetLandingPagesUsecase($landingPageRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals($expectedLandingPages, $landingPages);
    }

    public function testGetLandingPagesUsecaseReturnsArrayWhenLandingPageExist()
    {
        $landingPage = new LandingPages();
        $expectedResult = array($landingPage);
        $landingPageRepository = $this->createLandingPagesRepositoryWithFindByMethodMock($expectedResult);
        $viewCounts = (new GetLandingPagesUsecase($landingPageRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($viewCounts));
    }

    public function testGetLandingPagesUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $landingPageRepository = $this->createLandingPagesRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find page.');
        $result = (new GetLandingPagesUsecase($landingPageRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createLandingPagesRepositoryMock()
    {
        $viewLandingPages = $this->getMock('\Core\Domain\Repository\LandingPagesRepositoryInterface');
        return $viewLandingPages;
    }

    private function createLandingPagesRepositoryWithFindByMethodMock($returns)
    {
        $landingPagesRepository = $this->createLandingPagesRepositoryMock();
        $landingPagesRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('\Core\Domain\Entity\LandingPages'), $this->isType('array'))
            ->willReturn($returns);
        return $landingPagesRepository;
    }
}

<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\LandingPage;
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
        $landingPage = new LandingPage();
        $expectedResult = array($landingPage);
        $landingPageRepository = $this->createLandingPagesRepositoryWithFindByMethodMock($expectedResult);
        $landingPages = (new GetLandingPagesUsecase($landingPageRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($landingPages));
    }

    public function testGetLandingPagesUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $landingPageRepository = $this->createLandingPageRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find page.');
        $result = (new GetLandingPagesUsecase($landingPageRepository, new Purifier(), new Errors()))->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createLandingPageRepositoryMock()
    {
        $landingPageRepository = $this->getMock('\Core\Domain\Repository\LandingPageRepositoryInterface');
        return $landingPageRepository;
    }

    private function createLandingPagesRepositoryWithFindByMethodMock($returns)
    {
        $landingPageRepository = $this->createLandingPageRepositoryMock();
        $landingPageRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('\Core\Domain\Entity\LandingPage'), $this->isType('array'))
            ->willReturn($returns);
        return $landingPageRepository;
    }
}

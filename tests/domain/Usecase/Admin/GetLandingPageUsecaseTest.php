<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\LandingPage;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetLandingPageUsecase;
use \Core\Service\Errors;

class GetLandingPageUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetLandingPageUsecaseWhenIdDoesNotExist()
    {
        $condition = array('id' => 0);
        $landingPageRepositoryMock = $this->createLandingPageRepositoryWithFindMethodMock($condition, 0);
        $landingPageUsecase = new GetLandingPageUsecase($landingPageRepositoryMock, new Purifier(), new Errors());
        $result = $landingPageUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Page not found');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetLandingPageUsecase()
    {
        $condition = array('id' => 0);
        $landingPage = new LandingPage();
        $landingPage->setId(0);
        $landingPageRepositoryMock = $this->createLandingPageRepositoryWithFindMethodMock($condition, $landingPage);
        $landingPageUsecase = new GetLandingPageUsecase($landingPageRepositoryMock, new Purifier(), new Errors());
        $result = $landingPageUsecase->execute($condition);
        $this->assertEquals($landingPage, $result);
    }

    public function testGetLandingPageUsecaseWhenIdIsInvalid()
    {
        $condition = 'invalid';
        $landingPageRepositoryMock = $this->createLandingPageRepositoryMock();
        $landingPageUsecase = new GetLandingPageUsecase($landingPageRepositoryMock, new Purifier(), new Errors());
        $result = $landingPageUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find page.');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createLandingPageRepositoryMock()
    {
        $landingPageRepository = $this->getMockBuilder('\Core\Domain\Repository\LandingPageRepositoryInterface')->getMock();
        return $landingPageRepository;
    }

    private function createLandingPageRepositoryWithFindMethodMock($condition, $returns)
    {
        $landingPageRepositoryMock = $this->createLandingPageRepositoryMock();
        $landingPageRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\LandingPage'), $this->equalTo($condition))
            ->willReturn($returns);
        return $landingPageRepositoryMock;
    }
}

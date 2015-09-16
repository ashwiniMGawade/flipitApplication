<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\LandingPages;
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

    public function testGetWidgetUsecase()
    {
        $condition = array('id' => 0);
        $landingPage = new LandingPages();
        $landingPage->__set('id', 0);
        $landingPageRepositoryMock = $this->createLandingPageRepositoryWithFindMethodMock($condition, $landingPage);
        $landingPageUsecase = new GetLandingPageUsecase($landingPageRepositoryMock, new Purifier(), new Errors());
        $result = $landingPageUsecase->execute($condition);
        $this->assertEquals($landingPage, $result);
    }

    public function testGetLandingPageUsecaseWhenIdIsInvalid()
    {
        $condition = 'invalid';
        $landingPageRepositoryMock = $this->createLandingPagRepositoryMock();
        $landingPageUsecase = new GetLandingPageUsecase($landingPageRepositoryMock, new Purifier(), new Errors());
        $result = $landingPageUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find page.');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createLandingPagRepositoryMock()
    {
        $landingPageRepository = $this->getMockBuilder('\Core\Domain\Repository\LandingPagesRepositoryInterface')->getMock();
        return $landingPageRepository;
    }

    private function createLandingPageRepositoryWithFindMethodMock($condition, $returns)
    {
        $landingPageRepositoryMock = $this->createLandingPagRepositoryMock();
        $landingPageRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\LandingPages'), $this->equalTo($condition))
            ->willReturn($returns);
        return $landingPageRepositoryMock;
    }
}

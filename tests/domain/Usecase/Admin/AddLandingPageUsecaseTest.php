<?php
namespace Usecase\Admin;

use Core\Domain\Entity\LandingPages;
use Core\Domain\Service\Purifier;
use Core\Domain\Usecase\Admin\AddLandingPageUsecase;
use Core\Service\Errors;

class AddLandingPageUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testAddLandingPageUsecaseReturnsErrorWhenParamsAreInvalid()
    {
        $params = array();
        $landingPageRepository = $this->landingPageRepositoryMock();
        $landingPageValidator = $this->createLandingPageValidatorMock(array('title'=>'This field is required.'));
        $result = (new AddLandingPageUsecase(
            $landingPageRepository,
            $landingPageValidator,
            new Purifier(),
            new Errors()
        ))->execute(new LandingPages(), $params);
        $errors = new Errors();
        $errors->setError('This field is required.', 'title');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    private function landingPageRepositoryMock()
    {
        $landingPageRepositoryMock = $this->getMock('\Core\Domain\Repository\LandingPagesRepositoryInterface');
        return $landingPageRepositoryMock;
    }

    private function landingPageRepositoryMockWithSaveMethod()
    {
        $landingPageRepositoryMock = $this->landingPageRepositoryMock();
        $landingPageRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\LandingPages'))
            ->willReturn(new LandingPages());
        return $landingPageRepositoryMock;
    }

    private function createLandingPageValidatorMock($returns)
    {
        $mockLandingPageValidator = $this->getMockBuilder('\Core\Domain\Validator\LandingPageValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $mockLandingPageValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\LandingPages'))
            ->willReturn($returns);
        return $mockLandingPageValidator;
    }
}

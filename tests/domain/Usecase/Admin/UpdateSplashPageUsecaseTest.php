<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\User\SplashPage;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\UpdateSplashPageUsecase;
use \Core\Service\Errors;

class UpdateSplashPageUsecaseTest extends \Codeception\TestCase\Test
{
    public function testUpdateSplashpageUsecaseReturnsErrorsObjectWithInvalidParameters()
    {
        $params = array(
            'content' => null
        );
        $splashPageRepository = $this->splashPageRepositoryMock();
        $splashPageValidator = $this->createSplashPageValidatorMock(array('content'=>'Content should not be blank.'));
        $result = (new UpdateSplashPageUsecase(
            $splashPageRepository,
            $splashPageValidator,
            new Purifier(),
            new Errors()
        )
        )->execute(new SplashPage(), $params);
        $errors = new Errors();
        $errors->setError('Content should not be blank.', 'content');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateSplashpageUsecaseReturnsSplashPageObject()
    {
        $params = array(
            'content' => 'Test text',
            'image' => 'test.jpg',
            'popularShops' => 'Test shops',
            'updatedBy' => 1,
            'updatedAt' => date('Y-m-d'),
            'infoImage' => 'info.jpg',
            'footer' => 'Footer Content',
            'visitorsPerMonthCount' => 1,
            'verifiedActionCount' => 1,
            'newsletterSignupCount' => 1,
            'retailerOnlineCount' => 1
        );
        $splashPageRepository = $this->splashPageRepositoryMockWithSaveMethod();
        $splashPageValidator = $this->createSplashPageValidatorMock(true);
        $result = (new UpdateSplashPageUsecase(
            $splashPageRepository,
            $splashPageValidator,
            new Purifier(),
            new Errors()
        )
        )->execute(new SplashPage(), $params);
        $this->assertEquals(new SplashPage(), $result);
    }

    private function splashPageRepositoryMock()
    {
        $splashPageRepositoryMock = $this->getMock('\Core\Domain\Repository\SplashPageRepositoryInterface');
        return $splashPageRepositoryMock;
    }

    private function splashPageRepositoryMockWithSaveMethod()
    {
        $splashPageRepositoryMock = $this->splashPageRepositoryMock();
        $splashPageRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\User\SplashPage'))
            ->willReturn(new SplashPage());
        return $splashPageRepositoryMock;
    }

    private function createSplashPageValidatorMock($returns)
    {
        $mockSplashPageValidator = $this->getMockBuilder('\Core\Domain\Validator\SplashPageValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSplashPageValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\User\SplashPage'))
            ->willReturn($returns);
        return $mockSplashPageValidator;
    }
}

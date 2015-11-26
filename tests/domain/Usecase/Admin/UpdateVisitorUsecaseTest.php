<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Visitor;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\UpdateVisitorUsecase;
use \Core\Service\Errors;

class UpdateVisitorUsecaseTest extends \Codeception\TestCase\Test
{
    public function testUpdateVisitorUsecaseReturnsErrorsObjectWithInvalidParameters()
    {
        $params = array(
            'email' => null
        );
        $visitorRepository = $this->visitorRepositoryMock();
        $visitorValidator = $this->createVisitorValidatorMock(array('email'=>'email should not be blank.'));
        $result = (new UpdateVisitorUsecase(
            $visitorRepository,
            $visitorValidator,
            new Purifier(),
            new Errors()
        )
        )->execute(new Visitor(), $params);
        $errors = new Errors();
        $errors->setError('email should not be blank.', 'email');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateVisitorUsecaseReturnsVisitorObject()
    {
        $params = array(
            'mailOpenCount' => 1,
            'lastEmailOpenDate' => '2015-06-30 17:01:34',
            'mailClickCount' => 10,
            'mailSoftBounceCount' => 1,
            'mailHardBounceCount' => 3,
            'active' => 1,
            'inactiveStatusReason' => 'Test shops',
            'activeCodeId' => 1,
            'changePasswordRequest' => 1,
            'codeAlert' => 1,
            'codeAlertSendDate' => '2015-06-30 17:01:34',
            'currentLogIn' => 1,
            'content' => 'Test text',
            'dateOfBirth' => '2015-06-30 17:01:34',
            'deleted' => 0,
            'email' => 'new@visitor.com',
            'fashionNewsLetter' => 0,
            'firstName' => 'Visitor First Name',
            'gender' => 1,
            'interested' => 1,
            'lastLogIn' => '2015-06-30 17:01:34',
            'currentLogin' => '2015-06-30 17:01:34',
            'lastName' => 'Last',
            'password' => 1,
            'postalCode' => '111111',
            'profileImg' => 'test.jpg',
            'pwd' => 'shrgdsfjh',
            'status' => 1,
            'travelNewsLetter' => 1,
            'weeklyNewsLetter' => 1,
            'username' => 1,
            'visitorKeyword' => 1
        );
        $visitorRepository = $this->visitorRepositoryMockWithSaveMethod();
        $visitorValidator = $this->createVisitorValidatorMock(true);
        $result = (new UpdateVisitorUsecase(
            $visitorRepository,
            $visitorValidator,
            new Purifier(),
            new Errors()
        )
        )->execute(new Visitor(), $params);
        $this->assertInstanceOf('Core\Domain\Entity\Visitor', $result);
    }

    private function visitorRepositoryMock()
    {
        $visitorRepositoryMock = $this->getMock('\Core\Domain\Repository\VisitorRepositoryInterface');
        return $visitorRepositoryMock;
    }

    private function visitorRepositoryMockWithSaveMethod()
    {
        $visitorRepositoryMock = $this->visitorRepositoryMock();
        $visitorRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'))
            ->willReturn(new Visitor());
        return $visitorRepositoryMock;
    }

    private function createVisitorValidatorMock($returns)
    {
        $mockVisitorValidator = $this->getMockBuilder('\Core\Domain\Validator\VisitorValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $mockVisitorValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'))
            ->willReturn($returns);
        return $mockVisitorValidator;
    }
}

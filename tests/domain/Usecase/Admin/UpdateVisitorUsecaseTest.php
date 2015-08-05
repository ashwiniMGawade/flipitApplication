<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Visitor;
use \Core\Domain\Usecase\Admin\UpdateVisitorUsecase;
use \Core\Domain\Validator\VisitorValidator;

class UpdateVisitorUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    protected $visitorRepository;

    protected function _before()
    {
        $this->visitorRepository = $this->createVisitorRepositoryInterfaceMock();
    }

    public function testUpdateVisitorUsecaseWhenInputNotEqualsArray()
    {
        $this->setExpectedException('Exception', 'Invalid Parameters');
        $invalidInput = 'NOT_ARRAY';
        $validatorInterface = $this->createValidatorInterfaceMock();
        (new UpdateVisitorUsecase($this->visitorRepository, new VisitorValidator($validatorInterface)))->execute($invalidInput);
    }

    public function testUpdateVisitorUsecaseWhenInputEqualsEmptyArray()
    {
        $this->setExpectedException('Exception', 'Invalid Parameters');
        $invalidInput = array();
        $validatorInterface = $this->createValidatorInterfaceMock();
        (new UpdateVisitorUsecase($this->visitorRepository, new VisitorValidator($validatorInterface)))->execute($invalidInput);
    }

    public function testUpdateVisitorUsecaseWhenEmailIsInValid()
    {
        $this->setExpectedException('Exception', 'Invalid Email');
        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'open'
        );
        $validatorInterface = $this->createValidatorInterfaceMock();
        (new UpdateVisitorUsecase($this->visitorRepository, new VisitorValidator($validatorInterface)))->execute($validInput);
    }

    public function testUpdateVisitorUsecaseWhenVisitorObjectIsInvalid()
    {
        $visitor = new Visitor();
        $visitor->setEmail('test@example.com');

        $this->setExpectedException('Exception', 'Invalid Email');
        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'click'
        );
        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);

        $visitorValidator = $this->createVisitorValidatorWithValidateMethodMock('Invalid Email');
        (new UpdateVisitorUsecase($validatorRepository, $visitorValidator))->execute($validInput);
    }

    public function testUpdateVisitorUsecaseWhenVisitorObjectIsValid()
    {
        $visitor = new Visitor();
        $visitor->setEmail('test@example.com');

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'click'
        );
        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);
        $validatorRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'));

        $visitorValidator = $this->createVisitorValidatorWithValidateMethodMock(true);
        (new UpdateVisitorUsecase($validatorRepository, $visitorValidator))->execute($validInput);
    }

    public function testUsecaseThrowsErrorWhenOpensTimeStampDoesNotExist()
    {
        $this->setExpectedException('Exception', 'Invalid Opens Timestamp');
        $visitor = $this->createVisitorMock();
        $initialOpenCount = $visitor->getMailOpenCount();
        $visitor->expects($this->once())->method('getMailOpenCount');
        $visitor->expects($this->once())->method('setMailOpenCount')->with($initialOpenCount + 1);

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);

        $validatorInterface = $this->createValidatorInterfaceMock();

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'open'
        );
        (new UpdateVisitorUsecase($validatorRepository, new VisitorValidator($validatorInterface)))->execute($validInput);
    }

    public function testUsecaseIncrementsOpenCountWhenTryingToUpdateEmailOpenForAnEmailId()
    {
        $visitor = $this->createVisitorMock();
        $initialOpenCount = $visitor->getMailOpenCount();
        $visitor->expects($this->once())->method('getMailOpenCount');
        $visitor->expects($this->once())->method('setMailOpenCount')->with($initialOpenCount + 1);

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);
        $validatorRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'));

        $visitorValidator = $this->createVisitorValidatorWithValidateMethodMock(true);

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'open',
            'opensTimestamp' => 1430805793
        );
        (new UpdateVisitorUsecase($validatorRepository, $visitorValidator))->execute($validInput);
    }

    public function testUsecaseIncrementsClickCountWhenTryingToUpdateEmailClickForAnEmailId()
    {
        $visitor = $this->createVisitorMock();
        $initialClickCount = $visitor->getMailClickCount();
        $visitor->expects($this->once())->method('getMailClickCount');
        $visitor->expects($this->once())->method('setMailClickCount')->with($initialClickCount + 1);

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);
        $validatorRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'));

        $visitorValidator = $this->createVisitorValidatorWithValidateMethodMock(true);

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'click'
        );
        (new UpdateVisitorUsecase($validatorRepository, $visitorValidator))->execute($validInput);
    }

    public function testUsecaseIncrementsSoftBounceCountWhenTryingToUpdateEmailSoftBounceForAnEmailId()
    {
        $visitor = $this->createVisitorMock();
        $initialSoftBounceCount = $visitor->getMailSoftBounceCount();
        $visitor->expects($this->once())->method('getMailSoftBounceCount');
        $visitor->expects($this->once())->method('setMailSoftBounceCount')->with($initialSoftBounceCount + 1);

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);
        $validatorRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'));

        $visitorValidator = $this->createVisitorValidatorWithValidateMethodMock(true);

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'soft_bounce'
        );
        (new UpdateVisitorUsecase($validatorRepository, $visitorValidator))->execute($validInput);
    }

    public function testUsecaseSetsTheVisitorAsInactiveWhenSoftBounceCountIsGreaterThanOrEqualTo6()
    {
        $initialSoftBounceCount = 5;
        $visitor = $this->createVisitorWithGetMailSoftBounceCountMethodMock($initialSoftBounceCount);
        $visitor->expects($this->once())->method('getMailSoftBounceCount');
        $visitor->expects($this->once())->method('setMailSoftBounceCount')->with($initialSoftBounceCount + 1);
        $visitor->expects($this->once())->method('setActive')->with(0);
        $visitor->expects($this->once())->method('setInactiveStatusReason')->with('Soft Bounce');

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);
        $validatorRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'));

        $visitorValidator = $this->createVisitorValidatorWithValidateMethodMock(true);

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'soft_bounce'
        );
        (new UpdateVisitorUsecase($validatorRepository, $visitorValidator))->execute($validInput);
    }

    public function testUsecaseIncrementsHardBounceCountWhenTryingToUpdateEmailHardBounceForAnEmailId()
    {
        $visitor = $this->createVisitorMock();
        $initialHardBounceCount = $visitor->getMailHardBounceCount();
        $visitor->expects($this->once())->method('getMailHardBounceCount');
        $visitor->expects($this->once())->method('setMailHardBounceCount')->with($initialHardBounceCount + 1);

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);
        $validatorRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'));

        $visitorValidator = $this->createVisitorValidatorWithValidateMethodMock(true);

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'hard_bounce'
        );
        (new UpdateVisitorUsecase($validatorRepository, $visitorValidator))->execute($validInput);
    }

    public function testUsecaseSetsTheVisitorAsInactiveWhenHardBounceCountIsGreaterThanOrEqualTo3()
    {
        $initialHardBounceCount = 2;
        $visitor = $this->createVisitorWithGetMailHardBounceCountMethodMock($initialHardBounceCount);
        $visitor->expects($this->once())->method('getMailHardBounceCount');
        $visitor->expects($this->once())->method('setMailHardBounceCount')->with($initialHardBounceCount + 1);
        $visitor->expects($this->once())->method('setActive')->with(0);
        $visitor->expects($this->once())->method('setInactiveStatusReason')->with('Hard Bounce');

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);
        $validatorRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'));

        $visitorValidator = $this->createVisitorValidatorWithValidateMethodMock(true);

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'hard_bounce'
        );
        (new UpdateVisitorUsecase($validatorRepository, $visitorValidator))->execute($validInput);
    }

    public function testUpdateVisitorUsecaseThrowsExceptionWhenEventIsInValid()
    {
        $visitor = $this->createVisitorMock();

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);

        $this->setExpectedException('Exception', 'Invalid Event');
        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'invalid_event'
        );
        $validatorInterface = $this->createValidatorInterfaceMock();
        (new UpdateVisitorUsecase($validatorRepository, new VisitorValidator($validatorInterface)))->execute($validInput);
    }

    private function createVisitorMock()
    {
        $visitor = $this->getMock('\Core\Domain\Entity\Visitor');
        return $visitor;
    }

    private function createVisitorWithGetMailSoftBounceCountMethodMock($count)
    {
        $visitor = $this->createVisitorMock();
        $visitor->expects($this->once())->method('getMailSoftBounceCount')->willReturn($count);
        return $visitor;
    }

    private function createVisitorWithGetMailHardBounceCountMethodMock($count)
    {
        $visitor = $this->createVisitorMock();
        $visitor->expects($this->once())->method('getMailHardBounceCount')->willReturn($count);
        return $visitor;
    }

    private function createVisitorRepositoryInterfaceMock()
    {
        return $this->getMock('\Core\Domain\Repository\VisitorRepositoryInterface');
    }

    private function createVisitorRepositoryWithFindOneByMethodMock($returns)
    {
        $visitorRepository = $this->createVisitorRepositoryInterfaceMock();
        $visitorRepository->expects($this->once())
                          ->method('findOneBy')
                          ->with($this->isType('string', '\Core\Domain\Entity\Visitor'), $this->isType('array'))
                          ->willReturn($returns);
        return $visitorRepository;
    }

    private function createVisitorValidatorMock()
    {
        $visitorValidator = $this->getMockBuilder('\Core\Domain\Validator\VisitorValidator')
            ->disableOriginalConstructor()
            ->getMock();
        return $visitorValidator;
    }

    private function createVisitorValidatorWithValidateMethodMock($returns)
    {
        $visitorValidator = $this->createVisitorValidatorMock();
        $visitorValidator->expects($this->once())
                         ->method('validate')
                         ->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'))
                         ->willReturn($returns);
        return $visitorValidator;
    }

    private function createValidatorInterfaceMock()
    {
        $mockValidatorInterface = $this->getMock('\Core\Domain\Adapter\ValidatorInterface');
        return $mockValidatorInterface;
    }
}

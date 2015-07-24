<?php
namespace Usecase\Admin;

use Core\Domain\Entity\Visitor;
use \Core\Domain\Usecase\Admin\UpdateVisitorUsecase;

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
        (new UpdateVisitorUsecase($this->visitorRepository))->execute($invalidInput);
    }

    public function testUpdateVisitorUsecaseWhenInputEqualsEmptyArray()
    {
        $this->setExpectedException('Exception', 'Invalid Parameters');
        $invalidInput = array();
        (new UpdateVisitorUsecase($this->visitorRepository))->execute($invalidInput);
    }

    public function testUpdateVisitorUsecaseWhenEmailIsInValid()
    {
        $this->setExpectedException('Exception', 'Invalid Email');
        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'open'
        );
        (new UpdateVisitorUsecase($this->visitorRepository))->execute($validInput);
    }

    public function testUsecaseIncrementsOpenCountWhenTryingToUpdateEmailOpenForAnEmailId()
    {
        $visitor = $this->createVisitorMock(1);
        $initialOpenCount = $visitor->getMailOpenCount();
        $visitor->expects($this->once())->method('getMailOpenCount');
        $visitor->expects($this->once())->method('setMailOpenCount')->with($initialOpenCount + 1);

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);
        $validatorRepository->expects($this->once())->method('save')->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'));

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'open'
        );
        (new UpdateVisitorUsecase($validatorRepository))->execute($validInput);
    }

    public function testUsecaseIncrementsClickCountWhenTryingToUpdateEmailClickForAnEmailId()
    {
        $visitor = $this->createVisitorMock(1);
        $initialClickCount = $visitor->getMailClickCount();
        $visitor->expects($this->once())->method('getMailClickCount');
        $visitor->expects($this->once())->method('setMailClickCount')->with($initialClickCount + 1);

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);
        $validatorRepository->expects($this->once())->method('save')->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'));

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'click'
        );
        (new UpdateVisitorUsecase($validatorRepository))->execute($validInput);
    }

    public function testUsecaseIncrementsSoftBounceCountWhenTryingToUpdateEmailSoftBounceForAnEmailId()
    {
        $visitor = $this->createVisitorMock(1);
        $initialSoftBounceCount = $visitor->getMailSoftBounceCount();
        $visitor->expects($this->once())->method('getMailSoftBounceCount');
        $visitor->expects($this->once())->method('setMailSoftBounceCount')->with($initialSoftBounceCount + 1);

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);
        $validatorRepository->expects($this->once())->method('save')->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'));

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'soft_bounce'
        );
        (new UpdateVisitorUsecase($validatorRepository))->execute($validInput);
    }

    public function testUsecaseIncrementsHardBounceCountWhenTryingToUpdateEmailHardBounceForAnEmailId()
    {
        $visitor = $this->createVisitorMock(1);
        $initialHardBounceCount = $visitor->getMailHardBounceCount();
        $visitor->expects($this->once())->method('getMailHardBounceCount');
        $visitor->expects($this->once())->method('setMailHardBounceCount')->with($initialHardBounceCount + 1);

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);
        $validatorRepository->expects($this->once())->method('save')->with($this->isInstanceOf('\Core\Domain\Entity\Visitor'));

        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'hard_bounce'
        );
        (new UpdateVisitorUsecase($validatorRepository))->execute($validInput);
    }

    public function testUpdateVisitorUsecaseThrowsExceptionWhenEventIsInValid()
    {
        $visitor = $this->createVisitorMock(1);

        $validatorRepository = $this->createVisitorRepositoryWithFindOneByMethodMock($visitor);

        $this->setExpectedException('Exception', 'Invalid Event');
        $validInput = array(
            'email' => 'test@example.com',
            'event' => 'invalid_event'
        );
        (new UpdateVisitorUsecase($validatorRepository))->execute($validInput);
    }

    private function createVisitorMock($id)
    {
        $visitor = $this->getMock('\Core\Domain\Entity\Visitor');
        $visitor->setId($id);
        $visitor->setMailOpenCount(1);
        $visitor->setMailClickCount(1);
        $visitor->setMailSoftBounceCount(1);
        $visitor->setMailHardBounceCount(1);
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
}

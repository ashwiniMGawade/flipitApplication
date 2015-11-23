<?php
namespace Usecase\System;

use \Core\Domain\Entity\Visitor;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\System\GetVisitorUsecase;
use \Core\Service\Errors;

class GetVisitorUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetVisitorUsecaseReturnsErrorWhenRecordDoestNotExist()
    {
        $condition = array('id' => 0);
        $visitorRepositoryMock = $this->createVisitorRepositoryWithFindMethodMock($condition, 0);
        $visitorUsecase = new GetVisitorUsecase($visitorRepositoryMock, new Purifier(), new Errors());
        $result = $visitorUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Visitor not found');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetVisitorUsecaseReturnsObjectWhenValidInputPassed()
    {
        $condition = array('id' => 0);
        $visitor = new Visitor();
        $visitor->setId(0);
        $visitorRepositoryMock = $this->createVisitorRepositoryWithFindMethodMock($condition, $visitor);
        $visitorUsecase = new GetVisitorUsecase($visitorRepositoryMock, new Purifier(), new Errors());
        $result = $visitorUsecase->execute($condition);
        $this->assertEquals($visitor, $result);
    }

    public function testGetVisitorUsecaseReturnsErrorWhenInvalidInputPassed()
    {
        $condition = 'invalid';
        $visitorRepositoryMock = $this->createVisitorsRepositoryMock();
        $visitorUsecase = new GetVisitorUsecase($visitorRepositoryMock, new Purifier(), new Errors());
        $result = $visitorUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find record.');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createVisitorsRepositoryMock()
    {
        $visitorRepository = $this->getMockBuilder('\Core\Domain\Repository\VisitorRepositoryInterface')->getMock();
        return $visitorRepository;
    }

    private function createVisitorRepositoryWithFindMethodMock($condition, $returns)
    {
        $visitorRepositoryMock = $this->createVisitorsRepositoryMock();
        $visitorRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\Visitor'), $this->equalTo($condition))
            ->willReturn($returns);
        return $visitorRepositoryMock;
    }
}

<?php
namespace Usecase\Guest;

use \Core\Domain\Entity\Dashboard;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Guest\GetDashboardUsecase;
use \Core\Service\Errors;

class GetDashboardUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetDashboardUsecaseReturnsErrorWhenRecordDoesNotExist()
    {
        $condition = array('id' => 0);
        $dashboardRepositoryMock = $this->createDashboardRepositoryWithFindMethodMock($condition, 0);
        $dashboardUsecase = new GetDashboardUsecase($dashboardRepositoryMock, new Purifier(), new Errors());
        $result = $dashboardUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Dashboard data not found');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetDashboardUsecaseReturnsDataWhenRecordExists()
    {
        $condition = array('id' => 1);
        $Dashboard = new Dashboard();
        $Dashboard->setId(1);
        $dashboardRepositoryMock = $this->createDashboardRepositoryWithFindMethodMock($condition, $Dashboard);
        $dashboardUsecase = new GetDashboardUsecase($dashboardRepositoryMock, new Purifier(), new Errors());
        $result = $dashboardUsecase->execute($condition);
        $this->assertEquals($Dashboard, $result);
    }

    public function testGetDashboardUsecaseReturnsErrorWhenConditionIsInvalid()
    {
        $condition = 'invalid';
        $dashboardRepositoryMock = $this->createDashboardRepositoryMock();
        $dashboardUsecase = new GetDashboardUsecase($dashboardRepositoryMock, new Purifier(), new Errors());
        $result = $dashboardUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find record.');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createDashboardRepositoryMock()
    {
        $dashboardRepository = $this->getMockBuilder('\Core\Domain\Repository\DashboardRepositoryInterface')->getMock();
        return $dashboardRepository;
    }

    private function createDashboardRepositoryWithFindMethodMock($condition, $returns)
    {
        $dashboardRepositoryMock = $this->createDashboardRepositoryMock();
        $dashboardRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\Dashboard'), $this->equalTo($condition))
            ->willReturn($returns);
        return $dashboardRepositoryMock;
    }
}

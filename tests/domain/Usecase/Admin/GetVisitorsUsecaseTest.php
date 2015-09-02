<?php
namespace Admin;

use Core\Domain\Entity\Visitor;
use \Core\Domain\Usecase\Admin\GetVisitorsUsecase;

class GetVisitorsUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testThrowsExceptionWhenParametersAreInvalid()
    {
        $invalidParams = 123;
        $this->setExpectedException('Exception', 'Invalid Parameters');
        $getVisitorsUsecase = new GetVisitorsUsecase($this->createVisitorRepositoryInterfaceMock());
        $getVisitorsUsecase->execute($invalidParams);
    }

    public function testThrowsExceptionWhenParametersAreValidAndVisitorsListIsEmpty()
    {
        $validParams = array(
            'searchtext' => 'sam',
            'email' => '@gmail.com'
        );
        $requestParams = array();
        $visitorRepository = $this->createVisitorRepositoryInterfaceWithFindVisitorsMethodMock(array());
        $getVisitorsUsecase = new GetVisitorsUsecase($visitorRepository);
        $getVisitorsUsecase->execute($validParams, $requestParams);
    }

    public function testGetVisitorsUsecaseReturnsArrayOfVisitorsWhenParametersAreValid()
    {
        $visitorRepository = $this->createVisitorRepositoryInterfaceWithFindVisitorsMethodMock(array(new Visitor()));
        $visitors = (new GetVisitorsUsecase($visitorRepository))->execute();
        $this->assertNotEmpty($visitors);
    }

    private function createVisitorRepositoryInterfaceMock()
    {
        return $this->getMock('\Core\Domain\Repository\VisitorRepositoryInterface');
    }

    private function createVisitorRepositoryInterfaceWithFindVisitorsMethodMock($returns)
    {
        $visitorRepository = $this->createVisitorRepositoryInterfaceMock();
        $visitorRepository->expects($this->once())
                          ->method('findVisitors')
                          ->with($this->isType('array'), $this->isType('array'))
                          ->willReturn(array('visitors' => $returns));
        return $visitorRepository;
    }
}

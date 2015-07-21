<?php
namespace Admin;

use Core\Domain\Entity\Visitor;
use \Core\Domain\Usecase\Admin\GetVisitorListingUsecase;

class GetVisitorListingUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testThrowsExceptionWhenParametersAreInvalid()
    {
        $invalidParams = 123;
        $this->setExpectedException('Exception', 'Invalid Parameters');
        $getVisitorListingUsecase = new GetVisitorListingUsecase($this->createVisitorRepositoryInterfaceMock());
        $visitors = $getVisitorListingUsecase->execute($invalidParams);
    }

    public function testThrowsExceptionWhenParametersAreValidAndVisitorsListIsEmpty()
    {
        $validParams = array(
            'searchtext' => 'sam',
            'email' => '@gmail.com'
        );
        $requestParams = array();
        $visitorRepository = $this->createVisitorRepositoryInterfaceWithFindVisitorsMethodMock(array());
        $getVisitorListingUsecase = new GetVisitorListingUsecase($visitorRepository);
        $visitors = $getVisitorListingUsecase->execute($validParams, $requestParams);
    }

    public function testGetVisitorListingUsecaseReturnsArrayOfVisitorsWhenParametersAreValid()
    {
        $visitorRepository = $this->createVisitorRepositoryInterfaceWithFindVisitorsMethodMock(array(new Visitor()));
        $visitors = (new GetVisitorListingUsecase($visitorRepository))->execute();
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
                          ->with($this->isType('string', '\Core\Domain\Entity\Visitor'), $this->isType('array'))
                          ->willReturn(array('visitors' => $returns));
        return $visitorRepository;
    }
}

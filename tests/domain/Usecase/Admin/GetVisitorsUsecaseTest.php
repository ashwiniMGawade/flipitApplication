<?php
namespace Admin;

use Core\Domain\Entity\Visitor;
use \Core\Domain\Usecase\Admin\GetVisitorsUsecase;
use \Core\Domain\Service\Purifier;
use \Core\Service\Errors;

class GetVisitorsUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testGetVisitorsUsecaseReturnsErrorObjectWhenInvalidParamsPassed()
    {
        $invalidParams = 123;
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find record.');
        $visitorRepository = $this->createVisitorRepositoryInterfaceMock();
        $result = (new GetVisitorsUsecase($visitorRepository, new Purifier(), new Errors()))->execute($invalidParams);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetVisitorsUsecaseReturnsEmptyWhenValidParametersPassed()
    {
        $params = array(
            'firstName' => 'sam',
            'email' => '@gmail.com'
        );
        $visitorRepository = $this->createVisitorRepositoryInterfaceMockWithFindByMethod(array());
        $result = (new GetVisitorsUsecase($visitorRepository, new Purifier(), new Errors()))->execute($params);
        $this->assertEmpty($result);
    }

    public function testGetVisitorsUsecaseReturnsArrayOfVisitorsWithPaginationDataWhenParametersAreValid()
    {
        $visitorRepository = $this->createVisitorRepositoryInterfaceMockWithFindByMethod(array('records' => array(new Visitor())));
        $visitors = (new GetVisitorsUsecase($visitorRepository, new Purifier(), new Errors()))->execute();
        $this->assertNotEmpty($visitors['records']);
    }

    public function testGetVisitorsUsecaseReturnsArrayOfVisitorsObjectWhenParametersAreValid()
    {
        $visitorRepository = $this->createVisitorRepositoryInterfaceMockWithPaginatedMethod(array(new Visitor()));
        $visitors = (new GetVisitorsUsecase($visitorRepository, new Purifier(), new Errors()))->execute(array(), array(), null, null, true);
        $this->assertNotEmpty($visitors);
    }

    private function createVisitorRepositoryInterfaceMock()
    {
        return $this->getMock('\Core\Domain\Repository\VisitorRepositoryInterface');
    }

    private function createVisitorRepositoryInterfaceMockWithPaginatedMethod($returns)
    {
        $visitorRepository = $this->createVisitorRepositoryInterfaceMock();
        $visitorRepository->expects($this->once())
                          ->method('findAllPaginated')
                          ->with('\Core\Domain\Entity\Visitor', $this->isType('array'), $this->isType('array'))
                          ->willReturn($returns);
        return $visitorRepository;
    }

    private function createVisitorRepositoryInterfaceMockWithFindByMethod($returns)
    {
        $visitorRepository = $this->createVisitorRepositoryInterfaceMock();
        $visitorRepository->expects($this->once())
            ->method('findBy')
            ->with('\Core\Domain\Entity\Visitor', $this->isType('array'), $this->isType('array'))
            ->willReturn($returns);
        return $visitorRepository;
    }
}

<?php
namespace Admin;

use Core\Domain\Entity\Visitor;
use \Core\Domain\Usecase\Admin\GetPaginatedVisitorsUsecase;
use \Core\Domain\Service\Purifier;
use \Core\Service\Errors;

class GetPaginatedVisitorsUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testGetPaginatedVisitorsUsecaseReturnsErrorObjectWhenInvalidParamsPassed()
    {
        $invalidParams = 123;
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find record.');
        $visitorRepository = $this->createVisitorRepositoryInterfaceMock();
        $result = (new GetPaginatedVisitorsUsecase($visitorRepository, new Purifier(), new Errors()))->execute($invalidParams);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetPaginatedVisitorsUsecaseReturnsEmptyWhenValidParametersPassed()
    {
        $params = array(
            'firstName' => 'sam',
            'email' => '@gmail.com'
        );
        $visitorRepository = $this->createVisitorRepositoryInterfaceMockWithPaginatedMethod(array());
        $result = (new GetPaginatedVisitorsUsecase($visitorRepository, new Purifier(), new Errors()))->execute($params);
        $this->assertEmpty($result);
    }

    public function testGetPaginatedVisitorsUsecaseReturnsArrayOfVisitorsWhenParametersAreValid()
    {
        $visitorRepository = $this->createVisitorRepositoryInterfaceMockWithPaginatedMethod(array('records' => array(new Visitor())));
        $visitors = (new GetPaginatedVisitorsUsecase($visitorRepository, new Purifier(), new Errors()))->execute();
        $this->assertNotEmpty($visitors['records']);
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
}

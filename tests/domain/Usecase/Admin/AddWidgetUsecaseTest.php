<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Widget;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\AddWidgetUsecase;
use Core\Service\Errors;

class AddWidgetUsecaseTest extends \Codeception\TestCase\Test
{
    public function testAddwidgetUsecaseReturnsErrorsObjectWithInvalidParameters()
    {
        $params = array(
            'title' => null,
            'content' => '',
            'startDate' => date('Y-m-d'),
            'endDate' => date('Y-m-d')
        );
        $widgetRepository = $this->widgetRepositoryMock();
        $widgetValidator = $this->createWidgetValidatorMock(array('title'=>'This field is required.'));
        $result = (new AddWidgetUsecase(
            $widgetRepository,
            $widgetValidator,
            new Purifier()
        )
        )->execute(new Widget(), $params);
        $errors = new Errors();
        $errors->setError('This field is required.', 'title');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testAddwidgetUsecaseReturnsWidgetObject()
    {
        $params = array(
            'title' => null,
            'content' => '',
            'startDate' => date('Y-m-d'),
            'endDate' => date('Y-m-d')
        );
        $widgetRepository = $this->widgetRepositoryMockWithSaveMethod();
        $widgetValidator = $this->createWidgetValidatorMock(true);
        $result = (new AddWidgetUsecase(
            $widgetRepository,
            $widgetValidator,
            new Purifier()
        )
        )->execute(new Widget(), $params);
        $this->assertEquals(new Widget(), $result);
    }

    private function widgetRepositoryMock()
    {
        $widgetRepositoryMock = $this->getMock('\Core\Domain\Repository\WidgetRepositoryInterface');
        return $widgetRepositoryMock;
    }

    private function widgetRepositoryMockWithSaveMethod()
    {
        $widgetRepositoryMock = $this->widgetRepositoryMock();
        $widgetRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Widget'))
            ->willReturn(new Widget());
        return $widgetRepositoryMock;
    }

    private function createWidgetValidatorMock($returns)
    {
        $mockWidgetValidator = $this->getMockBuilder('\Core\Domain\Validator\WidgetValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $mockWidgetValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Widget'))
            ->willReturn($returns);
        return $mockWidgetValidator;
    }
}

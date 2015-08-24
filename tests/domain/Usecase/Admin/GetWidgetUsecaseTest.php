<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Widget;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetWidgetUsecase;
use Core\Service\Errors;

class GetWidgetUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetWidgetUsecaseWhenIdDoesNotExist()
    {
        $condition = array('id' => 0);
        $widgetRepositoryMock = $this->createWidgetRepositoryWithFindMethodMock($condition, 0);
        $widgetUsecase = new GetWidgetUsecase($widgetRepositoryMock, new Purifier(), new Errors());
        $result = $widgetUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Widget not found');
        $this->assertEquals(array('Widget not found'), $result->getErrorsAll());
    }

    public function testGetWidgetUsecase()
    {
        $condition = array('id' => 0);
        $widget = new Widget();
        $widget->__set('id', 0);
        $widgetRepositoryMock = $this->createWidgetRepositoryWithFindMethodMock($condition, $widget);
        $widgetUsecase = new GetWidgetUsecase($widgetRepositoryMock, new Purifier(), new Errors());
        $result = $widgetUsecase->execute($condition);
        $this->assertEquals($widget, $result);
    }

    public function testGetWidgetUsecaseWhenIdIsInvalid()
    {
        $condition = 'invalid';
        $widgetRepositoryMock = $this->createWidgetRepositoryMock();
        $widgetUsecase = new GetWidgetUsecase($widgetRepositoryMock, new Purifier(), new Errors());
        $result = $widgetUsecase->execute($condition);
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find widget.');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createWidgetRepositoryMock()
    {
        $widgetRepository = $this->getMockBuilder('\Core\Domain\Repository\WidgetRepositoryInterface')->getMock();
        return $widgetRepository;
    }

    private function createWidgetRepositoryWithFindMethodMock($condition, $returns)
    {
        $widgetRepositoryMock = $this->createWidgetRepositoryMock();
        $widgetRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\Widget'), $this->equalTo($condition))
            ->willReturn($returns);
        return $widgetRepositoryMock;
    }
}

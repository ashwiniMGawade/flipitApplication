<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Widget;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\AddWidgetUsecase;
use \Core\Domain\Validator\WidgetValidator;

class AddWidgetUsecaseTest extends \Codeception\TestCase\Test
{

    public function testAddWidgetUsecase()
    {
        $params = array();
        $widgetRepository = $this->widgetRepositoryMock();
        $validatorInterface = $this->createValidatorInterfaceMock();
        (new AddWidgetUsecase(
            $widgetRepository,
            new WidgetValidator($validatorInterface),
            new Purifier()
        )
        )->execute(new Widget(), $params);
    }

    private function widgetRepositoryMock()
    {
        $widgetRepositoryMock = $this->getMock('\Core\Domain\Repository\WidgetRepositoryInterface');
        return $widgetRepositoryMock;
    }

    private function createValidatorInterfaceMock()
    {
        $mockValidatorInterface = $this->getMock('\Core\Domain\Adapter\ValidatorInterface');
        return $mockValidatorInterface;
    }
}

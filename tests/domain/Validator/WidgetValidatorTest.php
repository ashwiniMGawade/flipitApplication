<?php
namespace Validator;

use \Core\Domain\Entity\Widget;
use \Core\Domain\Service\Validator;
use \Core\Domain\Validator\WidgetValidator;

class WidgetValidatorTest extends \Codeception\TestCase\Test
{
    public function testWidgetValidatorWithValidOutcome()
    {
        $widgetValidator = new WidgetValidator($this->mockValidatorInterface(true));
        $this->assertTrue($widgetValidator->validate(new Widget()));
    }

    public function testWidgetValidatorWithInvalidOutcome()
    {
        $widgetValidator = new WidgetValidator($this->mockValidatorInterface(false));
        $this->assertFalse($widgetValidator->validate(new Widget()));
    }

    public function testWidgetValidatorWithoutTitle()
    {
        $endDate = new \DateTime('now');
        $endDate->add(new \DateInterval('P10D'));
        $widget = new Widget();
        $widget->setTitle('');
        $widget->setCreatedAt(new \DateTime('now'));
        $widget->setUpdatedAt(new \DateTime('now'));
        $widget->setStartDate(new \DateTime('now'));
        $widget->setEndDate($endDate);
        $widgetValidator = new WidgetValidator(new Validator());
        $result = $widgetValidator->validate($widget);
        $this->assertEquals(array('title'=>array('Title should not be blank.')), $result);
    }

    public function testWidgetValidatorWithStartDateAndWithoutEndDate()
    {
        $widget = new Widget();
        $widget->setTitle('Test');
        $widget->setCreatedAt(new \DateTime('now'));
        $widget->setUpdatedAt(new \DateTime('now'));
        $widget->setStartDate(new \DateTime('now'));
        $widgetValidator = new WidgetValidator(new Validator());
        $result = $widgetValidator->validate($widget);
        $this->assertEquals(array('endDate'=>array('End date should not be blank.')), $result);
    }

    public function testWidgetValidatorWithEndDateAndWithoutStartDate()
    {
        $widget = new Widget();
        $widget->setTitle('Test');
        $widget->setCreatedAt(new \DateTime('now'));
        $widget->setUpdatedAt(new \DateTime('now'));
        $widget->setEndDate(new \DateTime('now'));
        $widgetValidator = new WidgetValidator(new Validator());
        $result = $widgetValidator->validate($widget);
        $this->assertEquals(array('startDate'=>array('Start date should not be blank.')), $result);
    }

    public function testWidgetValidatorWithStartDateGreaterThanEndDate()
    {
        $endDate = new \DateTime();
        $endDate->sub(new \DateInterval('P10D'));
        $widget = new Widget();
        $widget->setTitle('Test');
        $widget->setCreatedAt(new \DateTime('now'));
        $widget->setUpdatedAt(new \DateTime('now'));
        $widget->setStartDate(new \DateTime('now'));
        $widget->setEndDate($endDate);
        $widgetValidator = new WidgetValidator(new Validator());
        $result = $widgetValidator->validate($widget);
        $this->assertEquals(array('endDate'=>array('End date should be greater than start date.')), $result);
    }

    private function mockValidatorInterface($flag)
    {
        $mockValidatorInterface = $this->getMock('\Core\Domain\Adapter\ValidatorInterface');
        $mockValidatorInterface
            ->expects($this->once())
            ->method('validate')
            ->with($this->isType('object'), $this->isType('array'))
            ->willReturn($flag);
        return $mockValidatorInterface;
    }
}

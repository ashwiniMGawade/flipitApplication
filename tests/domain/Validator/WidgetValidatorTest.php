<?php
namespace Validator;

use \Core\Domain\Entity\Widget;
use \Core\Domain\Validator\WidgetValidator;

class WidgetValidatorTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testShopValidatorWithValidOutcome()
    {
        $widgetValidator = new WidgetValidator($this->mockValidatorInterface(true));
        $this->assertTrue($widgetValidator->validate(new Widget()));
    }

    public function testShopValidatorWithInvalidOutcome()
    {
        $widgetValidator = new WidgetValidator($this->mockValidatorInterface(false));
        $this->assertFalse($widgetValidator->validate(new Widget()));
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

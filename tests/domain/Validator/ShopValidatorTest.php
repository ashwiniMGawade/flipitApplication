<?php
namespace Validator;

use \Core\Domain\Entity\Shop;
use \Core\Domain\Validator\ShopValidator;

class ShopValidatorTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testShopValidatorWithValidOutcome()
    {
        $shopValidator = new ShopValidator($this->mockValidatorInterface(true));
        $this->assertTrue($shopValidator->validate(new Shop()));
    }

    public function testShopValidatorWithInvalidOutcome()
    {
        $shopValidator = new ShopValidator($this->mockValidatorInterface(false));
        $this->assertFalse($shopValidator->validate(new Shop()));
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

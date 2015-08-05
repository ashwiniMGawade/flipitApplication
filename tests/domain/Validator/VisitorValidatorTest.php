<?php
namespace Validator;

use \Core\Domain\Entity\Visitor;
use \Core\Domain\Validator\VisitorValidator;

class VisitorValidatorTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testVisitorValidatorWithValidOutcome()
    {
        $visitorValidator = new VisitorValidator($this->mockValidatorInterface(true));
        $this->assertTrue($visitorValidator->validate(new Visitor()));
    }

    public function testVisitorValidatorWithInvalidOutcome()
    {
        $visitorValidator = new VisitorValidator($this->mockValidatorInterface(false));
        $this->assertFalse($visitorValidator->validate(new Visitor()));
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

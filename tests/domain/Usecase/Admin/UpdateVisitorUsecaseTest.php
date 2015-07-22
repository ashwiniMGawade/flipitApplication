<?php
namespace Usecase\Admin;

use \Core\Domain\Usecase\Admin\UpdateVisitorUsecase;

class UpdateVisitorUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testUpdateVisitorUsecaseWhenInputNotEqualsArray()
    {
        $this->setExpectedException('Exception', 'Invalid Parameters');
        $invalidInput = 'NOT_ARRAY';
        (new UpdateVisitorUsecase())->execute($invalidInput);
    }

    public function testUpdateVisitorUsecaseWhenInputEqualsEmptyArray()
    {
        $this->setExpectedException('Exception', 'Invalid Parameters');
        $invalidInput = array();
        (new UpdateVisitorUsecase())->execute($invalidInput);
    }

    public function testUpdateVisitorUsecaseWhenInputArrayIsValid()
    {
        $validInput = array(
            'test@example.com' => 'click'
        );
        (new UpdateVisitorUsecase())->execute($validInput);
    }
}

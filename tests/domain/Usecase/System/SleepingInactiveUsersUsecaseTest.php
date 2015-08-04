<?php
namespace Usecase\System;

use Core\Domain\Usecase\System\SleepingInactiveUsersUsecase;

class SleepingInactiveUsersUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    public function testSleepingInactiveUsersUsecase()
    {
        (new SleepingInactiveUsersUsecase())->execute();
    }
}

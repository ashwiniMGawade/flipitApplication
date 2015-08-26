<?php
namespace Usecase\Admin;

use Core\Domain\Usecase\Admin\CreateWidgetUsecase;

class CreateWidgetUsecaseTest extends \Codeception\TestCase\Test
{
    public function testCreateWidgetUsecase()
    {
        $this->assertInstanceOf(
            '\Core\Domain\Entity\Widget',
            (new CreateWidgetUsecase())->execute()
        );
    }
}

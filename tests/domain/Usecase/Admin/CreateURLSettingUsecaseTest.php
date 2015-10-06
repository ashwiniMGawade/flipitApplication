<?php
namespace Usecase\Admin;

use Core\Domain\Usecase\Admin\CreateURLSettingUsecase;

class CreateURLSettingUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testCreateURLSettingUsecase()
    {
        $URLSetting = (new CreateURLSettingUsecase())->execute();
        $this->assertInstanceOf('\Core\Domain\Entity\URLSetting', $URLSetting);
    }
}

<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Settings;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\UpdateSettingUsecase;

class UpdateSettingUsecaseTest extends \Codeception\TestCase\Test
{
    public function testUpdateSettingUsecaseReturnsSettingsObject()
    {
        $params = array(
            'value' => null
        );
        $settingRepository = $this->settingRepositoryMockWithSaveMethod();
        $result = (new UpdateSettingUsecase(
            $settingRepository,
            new Purifier()
        )
        )->execute(new Settings(), $params);
        $this->assertEquals(new Settings(), $result);
    }

    private function settingRepositoryMock()
    {
        $settingRepositoryMock = $this->getMock('\Core\Domain\Repository\SettingsRepositoryInterface');
        return $settingRepositoryMock;
    }

    private function settingRepositoryMockWithSaveMethod()
    {
        $settingRepositoryMock = $this->settingRepositoryMock();
        $settingRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Settings'))
            ->willReturn(new Settings());
        return $settingRepositoryMock;
    }
}

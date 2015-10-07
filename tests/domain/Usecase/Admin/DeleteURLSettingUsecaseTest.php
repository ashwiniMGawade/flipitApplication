<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\URLSetting;
use \Core\Domain\Usecase\Admin\DeleteURLSettingUsecase;

class DeleteURLSettingUsecaseTest extends \Codeception\TestCase\Test
{

    public function testDeleteURLSettingUsecase()
    {
        $urlSettingRepository = $this->createDeleteURLSettingRepositoryWithRemoveMethodMock(true);
        $this->assertEquals(true, (new DeleteURLSettingUsecase($urlSettingRepository))->execute(new URLSetting()));
    }

    private function createDeleteURLSettingRepositoryWithRemoveMethodMock($returns)
    {
        $urlSettingRepository = $this->getMock('\Core\Domain\Repository\URLSettingRepositoryInterface');
        $urlSettingRepository
            ->expects($this->once())
            ->method('remove')
            ->willReturn($returns);
        return $urlSettingRepository;
    }
}

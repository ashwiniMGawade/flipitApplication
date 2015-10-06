<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\URLSetting;
use \Core\Domain\Service\Purifier;
use Core\Domain\Usecase\Admin\GetURLSettingsUsecase;
use \Core\Service\Errors;

class GetURLSettingsUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetURLSettingsUsecaseReturnsZeroWhenURLSettingDoesNotExist()
    {
        $expectedURLSettings = 0;
        $urlSettingRepository = $this->createURLSettingRepositoryWithFindByMethodMock($expectedURLSettings);
        $urlSettings = (new GetURLSettingsUsecase($urlSettingRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals($expectedURLSettings, $urlSettings);
    }

    public function testGetURLSettingsUsecaseReturnsArrayWhenURLSettingsExist()
    {
        $urlSetting = new URLSetting();
        $expectedResult = array($urlSetting);
        $urlSettingRepository = $this->createURLSettingRepositoryWithFindByMethodMock($expectedResult);
        $urlSettings = (new GetURLSettingsUsecase($urlSettingRepository, new Purifier(), new Errors()))->execute();
        $this->assertEquals(count($expectedResult), count($urlSettings));
    }

    public function testGetURLSettingsUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $conditions = 'invalid';
        $urlSettingRepository = $this->createURLSettingRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find VWO Tag.');
        $result = (new GetURLSettingsUsecase($urlSettingRepository, new Purifier(), new Errors()))
                    ->execute($conditions);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    private function createURLSettingRepositoryMock()
    {
        $urlSettingRepository = $this->getMock('\Core\Domain\Repository\URLSettingRepositoryInterface');
        return $urlSettingRepository;
    }

    private function createURLSettingRepositoryWithFindByMethodMock($returns)
    {
        $urlSettingRepository = $this->createURLSettingRepositoryMock();
        $urlSettingRepository->expects($this->once())
            ->method('findBy')
            ->with($this->equalTo('\Core\Domain\Entity\URLSetting'), $this->isType('array'))
            ->willReturn($returns);
        return $urlSettingRepository;
    }
}

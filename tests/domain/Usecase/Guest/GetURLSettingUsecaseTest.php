<?php
namespace Usecase\Guest;

use \Core\Domain\Entity\URLSetting;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetURLSettingUsecase;
use \Core\Service\Errors;

class GetURLSettingUsecaseTest extends \Codeception\TestCase\Test
{
    public function testGetURLSettingUsecaseReturnsErrorWhenParametersAreInvalid()
    {
        $condition = 'invalid';
        $urlSettingRepository = $this->createURLSettingRepositoryMock();
        $errors = new Errors();
        $errors->setError('Invalid input, unable to find VWO Tag.');
        $result = (new GetURLSettingUsecase($urlSettingRepository, new Purifier(), new Errors()))->execute($condition);
        $this->assertInstanceOf('\Core\Service\Errors', $result);
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetURLSettingUsecaseReturnsErrorWhenIdDoesNotExist()
    {
        $condition = array('id' => 0);
        $urlSettingRepository = $this->createUrlSettingRepositoryWithFindMethodMock(false);
        $result = (new GetURLSettingUsecase($urlSettingRepository, new Purifier(), new Errors()))->execute($condition);
        $errors = new Errors();
        $errors->setError('VWO Tag not found');
        $this->assertEquals($errors->getErrorsAll(), $result->getErrorsAll());
    }

    public function testGetURLSettingUsecaseWhenParamsAreValid()
    {
        $condition = array('id' => 1);
        $urlSetting = new URLSetting();
        $urlSetting->setId(1);
        $urlSettingRepository = $this->createUrlSettingRepositoryWithFindMethodMock($urlSetting);
        $result = (new GetURLSettingUsecase($urlSettingRepository, new Purifier(), new Errors()))->execute($condition);
        $this->assertEquals($urlSetting, $result);
    }

    private function createURLSettingRepositoryMock()
    {
        $urlSettingRepository = $this->getMock('\Core\Domain\Repository\URLSettingRepositoryInterface');
        return $urlSettingRepository;
    }

    private function createUrlSettingRepositoryWithFindMethodMock($returns)
    {
        $urlSettingRepository = $this->createURLSettingRepositoryMock();
        $urlSettingRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo('\Core\Domain\Entity\URLSetting'), $this->isType('array'))
            ->willReturn($returns);
        return $urlSettingRepository;
    }
}

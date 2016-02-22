<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\URLSetting;
use \Core\Domain\Service\Validator;
use \Core\Domain\Usecase\Admin\UpdateURLSettingUsecase;
use \Core\Domain\Validator\UrlSettingValidator;
use \Core\Domain\Service\Purifier;
use \Core\Service\Errors;

class UpdateURLSettingUsecaseTest extends \Codeception\TestCase\Test
{
    public function testUpdateURLSettingUsecaseReturnsErrorWhenParamsAreEmpty()
    {
        $params = array();
        $urlSettingRepository = $this->urlSettingRepositoryMock();
        $urlSettingValidator = new UrlSettingValidator(new Validator());
        $result = (new UpdateURLSettingUsecase(
            $urlSettingRepository,
            $urlSettingValidator,
            new Purifier(),
            new Errors()
        ))->execute(new URLSetting(), $params);
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateURLSettingUsecaseReturnsErrorWhenParamsAreInvalid()
    {
        $params = array(
            'url' => null,
            'status' => null
        );
        $urlSettingRepository = $this->urlSettingRepositoryMock();
        $urlSettingValidator = $this->createUrlSettingValidatorMock(array('url' => 'URL cannot be empty.'));
        $result = (new UpdateURLSettingUsecase(
            $urlSettingRepository,
            $urlSettingValidator,
            new Purifier(),
            new Errors()
        ))->execute(new URLSetting(), $params);
        $errors = new Errors();
        $errors->setError('URL cannot be empty.', 'url');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateURLSettingUsecaseWhenParamsAreValid()
    {
        $params = array(
            'url' => 'this/is/a/valid/url',
            'status' => 1,
            'hotjarStatus' => 1
        );
        $urlSettingRepository = $this->urlSettingRepositoryMockWithSaveMethod(new URLSetting());
        $urlSettingValidator = $this->createUrlSettingValidatorMock(true);
        $result = (new UpdateURLSettingUsecase(
            $urlSettingRepository,
            $urlSettingValidator,
            new Purifier(),
            new Errors()
        ))->execute(new URLSetting(), $params);
        $this->assertInstanceOf('\Core\Domain\Entity\URLSetting', $result);
    }

    private function urlSettingRepositoryMock()
    {
        $urlSettingRepositoryMock = $this->getMock('\Core\Domain\Repository\URLSettingRepositoryInterface');
        return $urlSettingRepositoryMock;
    }

    private function urlSettingRepositoryMockWithSaveMethod($returns)
    {
        $urlSettingRepositoryMock = $this->urlSettingRepositoryMock();
        $urlSettingRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\URLSetting'))
            ->willReturn($returns);
        return $urlSettingRepositoryMock;
    }

    private function createUrlSettingValidatorMock($returns)
    {
        $UrlSettingValidator = $this->getMockBuilder('\Core\Domain\Validator\UrlSettingValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $UrlSettingValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\URLSetting'))
            ->willReturn($returns);
        return $UrlSettingValidator;
    }
}

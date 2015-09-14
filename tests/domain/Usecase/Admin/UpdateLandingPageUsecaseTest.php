<?php
namespace Usecase\Admin;

use Core\Domain\Entity\LandingPages;
use Core\Domain\Entity\Shop;
use Core\Domain\Service\Purifier;
use Core\Domain\Service\Validator;
use Core\Domain\Usecase\Admin\UpdateLandingPageUsecase;
use Core\Domain\Validator\LandingPageValidator;
use Core\Service\Errors;

class UpdateLandingPageUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testUpdateLandingPageUsecaseReturnsErrorWhenParamsAreEmpty()
    {
        $params = array();
        $landingPageRepository = $this->landingPageRepositoryMock();
        $landingPageValidator = new LandingPageValidator(new Validator());
        $result = (new UpdateLandingPageUsecase(
            $landingPageRepository,
            $landingPageValidator,
            new Purifier(),
            new Errors()
        ))->execute(new LandingPages(), $params);
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateLandingPageUsecaseReturnsErrorWhenParamsAreInvalid()
    {
        $params = array(
            'title' => null,
            'shop' => null,
            'permalink' => null
        );
        $landingPageRepository = $this->landingPageRepositoryMock();
        $landingPageValidator = $this->createLandingPageValidatorMock(array('title' => 'Title cannot be empty.'));
        $result = (new UpdateLandingPageUsecase(
            $landingPageRepository,
            $landingPageValidator,
            new Purifier(),
            new Errors()
        ))->execute(new LandingPages(), $params);
        $errors = new Errors();
        $errors->setError('Title cannot be empty.', 'title');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testUpdateLandingPageUsecaseReturnsLandingPageObjectWhenParametersAreValid()
    {
        $shop = new Shop();
        $shop->__set('id', 123);
        $params = array(
            'title' => 'Shopname - Landing Page',
            'shop' => $shop,
            'permalink' => 'shopname-landing-page-test',
            'subTitle' => 'Latest Offers',
            'metaTitle' => 'Latest Offers',
            'metaDescription' => '<p>Latest Offers</p>',
            'content' => '<p>Test Content</p>',
            'status' => 0,
            'offlineSince' => new \DateTime('now')
        );

        $landingPageRepository = $this->landingPageRepositoryMockWithSaveMethod(new LandingPages());
        $landingPageValidator = $this->createLandingPageValidatorMock(true);
        $result = (new UpdateLandingPageUsecase(
            $landingPageRepository,
            $landingPageValidator,
            new Purifier(),
            new Errors()
        ))->execute(new LandingPages(), $params);
        $this->assertInstanceOf('\Core\Domain\Entity\LandingPages', $result);
    }

    private function landingPageRepositoryMock()
    {
        $landingPageRepositoryMock = $this->getMock('\Core\Domain\Repository\LandingPagesRepositoryInterface');
        return $landingPageRepositoryMock;
    }

    private function landingPageRepositoryMockWithSaveMethod($returns)
    {
        $landingPageRepositoryMock = $this->landingPageRepositoryMock();
        $landingPageRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\LandingPages'))
            ->willReturn($returns);
        return $landingPageRepositoryMock;
    }

    private function createLandingPageValidatorMock($returns)
    {
        $mockLandingPageValidator = $this->getMockBuilder('\Core\Domain\Validator\LandingPageValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $mockLandingPageValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\LandingPages'))
            ->willReturn($returns);
        return $mockLandingPageValidator;
    }
}

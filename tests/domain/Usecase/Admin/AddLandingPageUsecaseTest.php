<?php
namespace Usecase\Admin;

use Core\Domain\Entity\LandingPage;
use Core\Domain\Entity\Shop;
use Core\Domain\Service\Purifier;
use Core\Domain\Service\Validator;
use Core\Domain\Usecase\Admin\AddLandingPageUsecase;
use Core\Domain\Validator\LandingPageValidator;
use Core\Service\Errors;

class AddLandingPageUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testAddLandingPageUsecaseReturnsErrorWhenParamsAreEmpty()
    {
        $params = array();
        $landingPageRepository = $this->landingPageRepositoryMock();
        $landingPageValidator = new LandingPageValidator(new Validator());
        $result = (new AddLandingPageUsecase(
            $landingPageRepository,
            $landingPageValidator,
            new Purifier(),
            new Errors()
        ))->execute(new LandingPage(), $params);
        $errors = new Errors();
        $errors->setError('Invalid Parameters');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testAddLandingPageUsecaseReturnsErrorWhenParamsAreInvalid()
    {
        $params = array(
            'title' => null,
            'shop' => null,
            'permalink' => null
        );
        $landingPageRepository = $this->landingPageRepositoryMock();
        $landingPageValidator = $this->createLandingPageValidatorMock(array('title' => 'Title cannot be empty.'));
        $result = (new AddLandingPageUsecase(
            $landingPageRepository,
            $landingPageValidator,
            new Purifier(),
            new Errors()
        ))->execute(new LandingPage(), $params);
        $errors = new Errors();
        $errors->setError('Title cannot be empty.', 'title');
        $this->assertEquals($errors->getErrorMessages(), $result->getErrorMessages());
    }

    public function testAddLandingPageUsecaseReturnsLandingPageObjectWhenParametersAreValid()
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

        $landingPageRepository = $this->landingPageRepositoryMockWithSaveMethod(new LandingPage());
        $landingPageValidator = $this->createLandingPageValidatorMock(true);
        $result = (new AddLandingPageUsecase(
            $landingPageRepository,
            $landingPageValidator,
            new Purifier(),
            new Errors()
        ))->execute(new LandingPage(), $params);
        $this->assertInstanceOf('\Core\Domain\Entity\LandingPage', $result);
    }

    private function landingPageRepositoryMock()
    {
        $landingPageRepositoryMock = $this->getMock('\Core\Domain\Repository\LandingPageRepositoryInterface');
        return $landingPageRepositoryMock;
    }

    private function landingPageRepositoryMockWithSaveMethod($returns)
    {
        $landingPageRepositoryMock = $this->landingPageRepositoryMock();
        $landingPageRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('\Core\Domain\Entity\LandingPage'))
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
            ->with($this->isInstanceOf('\Core\Domain\Entity\LandingPage'))
            ->willReturn($returns);
        return $mockLandingPageValidator;
    }
}

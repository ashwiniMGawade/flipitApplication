<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Widget;
use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\AddWidgetUsecase;
use \Core\Domain\Validator\WidgetValidator;

class AddWidgetUsecaseTest extends \Codeception\TestCase\Test
{

    public function testAddWidgetUsecase()
    {
        $params = array();
        $widgetRepository     = $this->widgetRepositoryMock();
        $validatorInterface = $this->createValidatorInterfaceMock();
        (new AddWidgetUsecase(
            $widgetRepository,
            new WidgetValidator($validatorInterface),
            new Purifier()
        )
        )->execute(new Widget(), $params);
    }

   /* public function testCreateShopWithInvalidAffliateNetworkParam()
    {
        $params = array(
            'affliateNetwork'   => 'Invalid Aff',
        );
        $this->setExpectedException('Exception', 'Invalid affliate network');
        $widgetRepository     = $this->widgetRepositoryMock();
        $affliateNetworkRepositoryMock = $this->affliateNetworkRepositoryMock();
        $validatorInterface = $this->createValidatorInterfaceMock();
        (new AddShopUsecase(
            $widgetRepository,
            new ShopValidator($validatorInterface),
            $affliateNetworkRepositoryMock,
            new Purifier()
        )
        )->execute(new Shop(), $params);
    }

    public function testCreateShopWithoutParams()
    {
        $widgetRepository = $this->widgetRepositoryMock();
        $shopValidator = $this->createShopValidatorMock(array(''));
        $affliateNetworkRepositoryMock = $this->affliateNetworkRepositoryMock();
        (new AddShopUsecase(
            $widgetRepository,
            $shopValidator,
            $affliateNetworkRepositoryMock,
            new Purifier()
        )
        )->execute(new Shop());
    }

    public function testCreateShopWithInvalidParam()
    {
        $params = array(
            'name'              => ''
        );
        $widgetRepository     = $this->widgetRepositoryMock();
        $affliateNetworkRepositoryMock = $this->affliateNetworkRepositoryMock();
        $shopValidator = $this->createShopValidatorMock(array());
        (new AddShopUsecase(
            $widgetRepository,
            $shopValidator,
            $affliateNetworkRepositoryMock,
            new Purifier()
        )
        )->execute(new Shop(), $params);
    }

    public function testCreateShopWithValidParams()
    {
        $params = array(
            'name'                  => 'Mock',
            'permaLink'             => 'Mock',
            'overriteTitle'         => 'Mock',
            'metaDescription'       => 'Mock',
            'usergenratedcontent'   => 1,
            'discussions'           => 1,
            'title'                 => 'Mock',
            'subTitle'              => 'Mock',
            'notes'                 => 'Mock',
            'accountManagerName'    => 'Mock',
            'affliateNetwork'       => 'Test',
            'deepLinkStatus'        => 1,
            'refUrl'                => 'Mock',
            'actualUrl'             => 'Mock',
            'shopText'              => 'Mock',
        );

        $widgetRepository = $this->widgetRepositoryMock();
        $shopValidator = $this->createShopValidatorMock(true);
        $affliateNetworkRepositoryMock = $this->createAffliateNetworkRepositoryWithFindOneByMethodMock();
        (new AddShopUsecase(
            $widgetRepository,
            $shopValidator,
            $affliateNetworkRepositoryMock,
            new Purifier()
        )
        )->execute(new Shop(), $params);
    }



    private function createAffliateNetworkRepositoryWithFindOneByMethodMock()
    {
        $affliateNetworkRepositoryMock = $this->affliateNetworkRepositoryMock();
        $affliateNetworkRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($this->createAffliateNetworkMock());
        return $affliateNetworkRepositoryMock;
    }

    private function createAffliateNetworkMock()
    {
        $affliateNetworkMock = $this->getMock('\Core\Domain\Entity\AffliateNetwork');
        return $affliateNetworkMock;
    }



    private function createShopValidatorMock($returns)
    {
        $mockShopValidator = $this->getMockBuilder('\Core\Domain\Validator\ShopValidator')
            ->disableOriginalConstructor()
            ->getMock();
        $mockShopValidator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('\Core\Domain\Entity\Shop'))
            ->willReturn($returns);
        return $mockShopValidator;
    }*/

    private function widgetRepositoryMock()
    {
        $widgetRepositoryMock = $this->getMock('\Core\Domain\Repository\WidgetRepositoryInterface');
        return $widgetRepositoryMock;
    }

    private function createValidatorInterfaceMock()
    {
        $mockValidatorInterface = $this->getMock('\Core\Domain\Adapter\ValidatorInterface');
        return $mockValidatorInterface;
    }
}

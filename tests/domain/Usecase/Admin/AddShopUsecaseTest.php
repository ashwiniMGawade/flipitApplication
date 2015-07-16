<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\Shop;
use \Core\Domain\Entity\AffliateNetwork;
use \Core\Domain\Usecase\Admin\AddShopUsecase;
use \Core\Domain\Validator\ShopValidator;

class AddShopUsecaseTest extends \Codeception\TestCase\Test
{

    protected $tester;

    public function testCreateShopWithInvalidAffliateNetworkParam()
    {
        $params = array(
            'affliateNetwork'   => 'Invalid Aff',
        );
        $this->setExpectedException('Exception', 'Invalid affliate network');
        $shopRepository     = $this->shopRepositoryMock();
        $affliateNetworkRepositoryMock = $this->affliateNetworkRepositoryMock();
        $validatorInterface = $this->createValidatorInterfaceMock();
        (new AddShopUsecase(
            $shopRepository,
            new ShopValidator($validatorInterface),
            $affliateNetworkRepositoryMock
        )
        )->execute(new Shop(), $params);
    }

    public function testCreateShopWithoutParams()
    {
        $shopRepository = $this->shopRepositoryMock();
        $shopValidator = $this->createShopValidatorMock(true);
        $affliateNetworkRepositoryMock = $this->affliateNetworkRepositoryMock();
        (new AddShopUsecase(
            $shopRepository,
            $shopValidator,
            $affliateNetworkRepositoryMock
        )
        )->execute(new Shop());
    }

    public function testCreateShopWithInvalidParam()
    {
        $params = array(
            'name'              => ''
        );
        $shopRepository     = $this->shopRepositoryMock();
        $affliateNetworkRepositoryMock = $this->affliateNetworkRepositoryMock();
        $shopValidatory = $this->createShopValidatorMock(array('sdsd'=>'sdsdsd'));
        (new AddShopUsecase(
            $shopRepository,
            $shopValidatory,
            $affliateNetworkRepositoryMock
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

        $shopRepository = $this->shopRepositoryMock();
        $shopValidator = $this->createShopValidatorMock(true);
        $affliateNetworkRepositoryMock = $this->createAffliateNetworkRepositoryWithFindByMethodMock();
        (new AddShopUsecase(
            $shopRepository,
            $shopValidator,
            $affliateNetworkRepositoryMock
        )
        )->execute(new Shop(), $params);
    }

    private function shopRepositoryMock()
    {
        $shopRepositoryMock = $this->getMock('\Core\Domain\Repository\ShopRepositoryInterface');
        return $shopRepositoryMock;
    }

    private function affliateNetworkRepositoryMock()
    {
        $affliateNetworkRepositoryMock = $this->getMock('\Core\Domain\Repository\AffliateNetworkRepositoryInterface');
        return $affliateNetworkRepositoryMock;
    }

    private function createAffliateNetworkRepositoryWithFindByMethodMock()
    {
        $affliateNetworkRepositoryMock = $this->affliateNetworkRepositoryMock();
        $affliateNetworkRepositoryMock
            ->expects($this->once())
            ->method('findBy')
            //->with('\Core\Domain\Entity\AffliateNetwork',array())
            ->willReturn(array($this->createAffliateNetworkMock()));
        return $affliateNetworkRepositoryMock;
    }

    private function createAffliateNetworkMock()
    {
        $affliateNetworkMock = $this->getMock('\Core\Domain\Entity\AffliateNetwork');
        return $affliateNetworkMock;
    }

    private function createValidatorInterfaceMock()
    {
        $mockValidatorInterface = $this->getMock('\Core\Domain\Adapter\ValidatorInterface');
        return $mockValidatorInterface;
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
    }
}

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
            'affliateNetwork'   => new AffliateNetwork(),
        );
        $this->setExpectedException('Exception', 'Invalid affiliate network');
        $shopRepository     = $this->shopRepositoryMock();
        $validatorInterface = $this->createValidatorInterfaceMock();
        (new AddShopUsecase(
            $shopRepository,
            new ShopValidator($validatorInterface)
        )
        )->execute(new Shop(), $params);
    }

    public function testCreateShopWithoutParams()
    {
        $shopRepository = $this->shopRepositoryMock();
        $shopValidator = $this->createShopValidatorMock(true);
        (new AddShopUsecase(
            $shopRepository,
            $shopValidator
        )
        )->execute(new Shop());
    }

    private function shopRepositoryMock()
    {
        $shopRepositoryMock = $this->getMock('\Core\Domain\Repository\ShopRepositoryInterface');
        return $shopRepositoryMock;
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

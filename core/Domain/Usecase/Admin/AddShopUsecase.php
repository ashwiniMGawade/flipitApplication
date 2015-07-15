<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\Shop;
use \Core\Domain\Repository\ShopRepositoryInterface;

class AddShopUsecase
{
    protected $shopRepository;

    public function __construct(ShopRepositoryInterface $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function execute(Shop $shop, $params = array())
    {
        $affliateNetwork = '';

        if (isset($params['affliateNetwork'])) {
            $affliateNetwork = $params['affliateNetwork'];
        }

        if (is_object($affliateNetwork) && !$affliateNetwork->getId()) {
            throw new \Exception('Invalid affiliate network');
        }
        $shop->setAffliatenetwork($affliateNetwork);
        return $this->shopRepository->persist($shop);
    }
}

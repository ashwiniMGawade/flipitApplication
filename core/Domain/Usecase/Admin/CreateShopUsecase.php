<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\Shop;
use \Core\Domain\Repository\ShopRepositoryInterface;

class CreateShopUsecase
{
    protected $shopRepository;

    public function __construct(ShopRepositoryInterface $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function execute(Shop $shop)
    {

    }
}

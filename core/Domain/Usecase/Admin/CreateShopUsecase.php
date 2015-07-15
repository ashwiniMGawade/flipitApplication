<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\Shop;
use \Core\Domain\Repository\ShopRepositoryInterface;

class CreateShopUsecase
{

    public function execute()
    {
        return new Shop();
    }
}

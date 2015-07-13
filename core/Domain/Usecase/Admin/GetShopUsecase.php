<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\ShopRepositoryInterface;

class GetShopUsecase
{
    private $shopRepository;

    public function __construct(ShopRepositoryInterface $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function execute($id)
    {
        return $this->shopRepository->find('\Core\Domain\Entity\Shop', $id);
    }
}

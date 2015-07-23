<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\ShopRepositoryInterface;

class DeleteShopUsecase
{
    private $shopRepository;

    public function __construct(ShopRepositoryInterface $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    public function execute($id)
    {
        if (is_null($id) || !is_numeric($id)) {
            throw new \Exception('Invalid shop Id', 400);
        }

        $shop = $this->shopRepository->find('\Core\Domain\Entity\Shop', $id);

        if (false === is_object($shop)) {
            throw new \Exception('Shop not found', 404);
        }
        $shop->setDeleted(1);

        return $this->shopRepository->save($shop);
    }
}

<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\ShopRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;

class GetShopUsecase
{
    protected $shopRepository;

    protected $htmlPurifier;

    public function __construct(ShopRepositoryInterface $shopRepository, PurifierInterface $htmlPurifier)
    {
        $this->shopRepository   = $shopRepository;
        $this->htmlPurifier     = $htmlPurifier;
    }

    public function execute($id)
    {
        $id = $this->htmlPurifier->purify($id);
        if (is_null($id) || !is_numeric($id)) {
            throw new \Exception('Invalid shop Id', 400);
        }
        
        $shop = $this->shopRepository->find('\Core\Domain\Entity\Shop', $id);

        if (false === is_object($shop)) {
            throw new \Exception('Shop not found', 404);
        }
        return $shop;
    }
}

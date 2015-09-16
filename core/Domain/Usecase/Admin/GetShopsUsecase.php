<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Adapter\PurifierInterface;
use Core\Domain\Repository\ShopRepositoryInterface;
use Core\Service\Errors\ErrorsInterface;

class GetShopsUsecase
{
    private $shopRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(ShopRepositoryInterface $shopRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->shopRepository = $shopRepository;
        $this->htmlPurifier = $htmlPurifier;
        $this->errors = $errors;
    }

    public function execute($conditions, $order = array())
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find Shops.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);
        return $this->shopRepository->findBy('\Core\Domain\Entity\Shop', $conditions, $order);
    }
}

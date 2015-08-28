<?php
namespace Core\Domain\Usecase\Guest;

use Core\Domain\Entity\Offer;

class GetOfferUsecase
{
    public function execute($offerId)
    {
        return new Offer();
    }
}

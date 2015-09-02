<?php
namespace Core\Domain\Usecase\Guest;

use Core\Domain\Entity\Offer;
use Core\Domain\Entity\ViewCount;
use Core\Domain\Repository\ViewCountRepositoryInterface;

class AddOfferClickUsecase
{
    protected $viewCountRepository;

    public function __construct(ViewCountRepositoryInterface $viewCountRepository)
    {
        $this->viewCountRepository = $viewCountRepository;
    }

    public function execute(ViewCount $viewCount, Offer $offer, $clientIp)
    {
        $viewCount->setViewcount($offer);
        $viewCount->setOnClick(1);
        $viewCount->setOnLoad(0);
        $viewCount->setIP($clientIp);
        $viewCount->setCreatedAt(new \DateTime('now'));
        $viewCount->setUpdatedAt(new \DateTime('now'));
        return $this->viewCountRepository->save($viewCount);
    }
}

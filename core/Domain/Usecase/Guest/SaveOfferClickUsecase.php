<?php
namespace Core\Domain\Usecase\Guest;

use Core\Domain\Entity\Offer;
use Core\Domain\Entity\ViewCount;
use Core\Domain\Repository\ViewCountRepositoryInterface;

class SaveOfferClickUsecase
{
    protected $viewCountRepository;

    public function __construct(ViewCountRepositoryInterface $viewCountRepository)
    {
        $this->viewCountRepository = $viewCountRepository;
    }

    public function execute(ViewCount $viewCount, Offer $offer, $clientIp)
    {
        $viewCount->viewcount = $offer;
        $viewCount->onClick = 1;
        $viewCount->onLoad = 0;
        $viewCount->IP = $clientIp;
        $viewCount->created_at = new \DateTime('now');
        $viewCount->updated_at = new \DateTime('now');
        return $this->viewCountRepository->save($viewCount);
    }
}

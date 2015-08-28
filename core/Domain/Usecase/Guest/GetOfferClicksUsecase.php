<?php
namespace Core\Domain\Usecase\Guest;

use Core\Domain\Repository\ViewCountRepositoryInterface;

class GetOfferClicksUsecase
{
    protected $viewCountRepository;

    public function __construct(ViewCountRepositoryInterface $viewCountRepository)
    {
        $this->viewCountRepository = $viewCountRepository;
    }

    public function execute($offerId, $clientIp)
    {
        return $this->viewCountRepository->getOfferClickCount($offerId, $clientIp);
    }
}

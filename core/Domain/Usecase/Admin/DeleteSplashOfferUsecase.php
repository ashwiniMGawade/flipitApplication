<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\User\Splash;
use \Core\Domain\Repository\SplashOfferRepositoryInterface;

class DeleteSplashOfferUsecase
{

    private $splashOfferRepository;

    public function __construct(SplashOfferRepositoryInterface $splashOfferRepository)
    {
        $this->splashOfferRepository = $splashOfferRepository;
    }

    public function execute(Splash $splashOffer)
    {
        return $this->splashOfferRepository->remove($splashOffer);
    }
}

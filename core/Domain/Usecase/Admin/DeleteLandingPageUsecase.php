<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\LandingPage;
use \Core\Domain\Repository\LandingPageRepositoryInterface;

class DeleteLandingPageUsecase
{

    private $landingPageRepository;

    public function __construct(LandingPageRepositoryInterface $landingPageRepository)
    {
        $this->landingPageRepository = $landingPageRepository;
    }

    public function execute(LandingPage $landingPage)
    {
        return $this->landingPageRepository->remove($landingPage);
    }
}

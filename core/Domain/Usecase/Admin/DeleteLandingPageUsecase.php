<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\LandingPages;
use \Core\Domain\Repository\LandingPagesRepositoryInterface;

class DeleteLandingPageUsecase
{

    private $landingPageRepository;

    public function __construct(LandingPagesRepositoryInterface $landingPageRepository)
    {
        $this->landingPageRepository = $landingPageRepository;
    }

    public function execute(LandingPages $landingPage)
    {
        return $this->landingPageRepository->remove($landingPage);
    }
}

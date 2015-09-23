<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\LandingPage;

class CreateLandingPageUsecase
{
    public function execute()
    {
        return new LandingPage();
    }
}

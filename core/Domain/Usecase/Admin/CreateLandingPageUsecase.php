<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\LandingPages;

class CreateLandingPageUsecase
{
    public function execute()
    {
        return new LandingPages();
    }
}

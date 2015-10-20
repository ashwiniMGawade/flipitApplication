<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Entity\User\Splash;

class CreateSplashOfferUsecase
{
    public function execute()
    {
        return new Splash();
    }
}

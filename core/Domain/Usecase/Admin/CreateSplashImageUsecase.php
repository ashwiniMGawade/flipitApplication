<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Entity\User\SplashImage;

class CreateSplashImageUsecase
{
    public function execute()
    {
        return new SplashImage();
    }
}

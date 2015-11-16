<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Entity\User\SplashImage;
use \Core\Domain\Repository\SplashImageRepositoryInterface;

class DeleteSplashImageUsecase
{
    private $splashImageRepository;

    public function __construct(SplashImageRepositoryInterface $splashImageRepository)
    {
        $this->splashImageRepository = $splashImageRepository;
    }

    public function execute(SplashImage $splashImage)
    {
        return $this->splashImageRepository->remove($splashImage);
    }
}

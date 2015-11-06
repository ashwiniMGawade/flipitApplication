<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\SplashImageRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetSplashImageUsecase
{
    protected $splashImageRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        SplashImageRepositoryInterface $splashImageRepository,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->splashImageRepository = $splashImageRepository;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find image.');
            return $this->errors;
        }

        $splashImage = $this->splashImageRepository->findOneBy('\Core\Domain\Entity\User\SplashImage', $conditions);

        if (false === is_object($splashImage)) {
            $this->errors->setError('Splash image not found');
            return $this->errors;
        }
        return $splashImage;
    }
}

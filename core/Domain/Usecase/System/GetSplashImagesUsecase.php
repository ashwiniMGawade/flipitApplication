<?php
namespace Core\Domain\Usecase\System;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\SplashImageRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetSplashImagesUsecase
{
    private $splashImageRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(SplashImageRepositoryInterface $splashImageRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->splashImageRepository    = $splashImageRepository;
        $this->htmlPurifier             = $htmlPurifier;
        $this->errors                   = $errors;
    }

    public function execute($conditions = array(), $order = array())
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find record.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);

        return $this->splashImageRepository->findBy('\Core\Domain\Entity\User\SplashImage', $conditions, $order);
    }
}

<?php
namespace Core\Domain\Usecase\System;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\SplashOfferRepositoryInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetSplashOffersUsecase
{
    private $splashOfferRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(SplashOfferRepositoryInterface $splashOfferRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->splashOfferRepository    = $splashOfferRepository;
        $this->htmlPurifier             = $htmlPurifier;
        $this->errors                   = $errors;
    }

    public function execute($conditions = array())
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find record.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);

        return $this->splashOfferRepository->findBy('\Core\Domain\Entity\User\Splash', $conditions);
    }
}

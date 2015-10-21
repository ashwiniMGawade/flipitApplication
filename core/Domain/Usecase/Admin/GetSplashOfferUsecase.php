<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\SplashOfferRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetSplashOfferUsecase
{
    protected $splashOfferRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(SplashOfferRepositoryInterface $splashOfferRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->splashOfferRepository    = $splashOfferRepository;
        $this->htmlPurifier             = $htmlPurifier;
        $this->errors                   = $errors;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find record.');
            return $this->errors;
        }

        $splashOffer = $this->splashOfferRepository->findOneBy('\Core\Domain\Entity\User\Splash', $conditions);

        if (false === is_object($splashOffer)) {
            $this->errors->setError('Record not found');
            return $this->errors;
        }
        return $splashOffer;
    }
}

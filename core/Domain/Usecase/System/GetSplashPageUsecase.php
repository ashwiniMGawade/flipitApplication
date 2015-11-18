<?php
namespace Core\Domain\Usecase\System;

use \Core\Domain\Repository\SplashPageRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetSplashPageUsecase
{
    protected $splashPageRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(SplashPageRepositoryInterface $splashPageRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->splashPageRepository = $splashPageRepository;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find splash page.');
            return $this->errors;
        }

        $splashPage = $this->splashPageRepository->findOneBy('\Core\Domain\Entity\User\SplashPage', $conditions);

        if (false === is_object($splashPage)) {
            $this->errors->setError('Splash page not found');
            return $this->errors;
        }
        return $splashPage;
    }
}

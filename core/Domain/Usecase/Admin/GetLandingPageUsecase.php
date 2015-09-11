<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\LandingPagesRepositoryInterface;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Service\Errors\ErrorsInterface;

class GetLandingPageUsecase
{
    protected $landingPageRepository;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(LandingPagesRepositoryInterface $landingPageRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->landingPageRepository    = $landingPageRepository;
        $this->htmlPurifier             = $htmlPurifier;
        $this->errors                   = $errors;
    }

    public function execute($conditions)
    {
        $conditions = $this->htmlPurifier->purify($conditions);
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find page.');
            return $this->errors;
        }

        $landingPage = $this->landingPageRepository->findOneBy('\Core\Domain\Entity\LandingPages', $conditions);

        if (false === is_object($landingPage)) {
            $this->errors->setError('Page not found');
            return $this->errors;
        }
        return $landingPage;
    }
}

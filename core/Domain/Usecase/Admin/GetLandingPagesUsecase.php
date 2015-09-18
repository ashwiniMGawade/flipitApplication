<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\LandingPageRepositoryInterface;
use Core\Service\Errors\ErrorsInterface;

class GetLandingPagesUsecase
{
    private $landingPageRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(LandingPageRepositoryInterface $landingPageRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->landingPageRepository   = $landingPageRepository;
        $this->htmlPurifier             = $htmlPurifier;
        $this->errors                   = $errors;
    }

    public function execute($conditions = array())
    {
        if (!is_array($conditions)) {
            $this->errors->setError('Invalid input, unable to find page.');
            return $this->errors;
        }
        $conditions = $this->htmlPurifier->purify($conditions);

        return $this->landingPageRepository->findBy('\Core\Domain\Entity\LandingPage', $conditions);
    }
}

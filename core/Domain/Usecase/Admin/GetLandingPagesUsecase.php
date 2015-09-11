<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Repository\LandingPagesRepositoryInterface;
use Core\Service\Errors\ErrorsInterface;

class GetLandingPagesUsecase
{
    private $landingPagesRepository;
    private $htmlPurifier;
    private $errors;

    public function __construct(LandingPagesRepositoryInterface $landingPagesRepository, PurifierInterface $htmlPurifier, ErrorsInterface $errors)
    {
        $this->landingPagesRepository   = $landingPagesRepository;
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
        return $this->landingPagesRepository->findBy('\Core\Domain\Entity\LandingPages', $conditions);
    }
}

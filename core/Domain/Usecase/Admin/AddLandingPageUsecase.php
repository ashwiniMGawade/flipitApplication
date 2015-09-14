<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Entity\LandingPages;
use Core\Domain\Repository\LandingPagesRepositoryInterface;
use \Core\Domain\Validator\LandingPageValidator;
use \Core\Service\Errors\ErrorsInterface;

class AddLandingPageUsecase
{
    private $landingPageRepository;

    protected $landingPageValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        LandingPagesRepositoryInterface $landingPageRepository,
        LandingPageValidator $landingPageValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->landingPageRepository = $landingPageRepository;
        $this->landingPageValidator  = $landingPageValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(LandingPages $landingPage, $params = array())
    {
        $params = $this->htmlPurifier->purify($params);

        $validationResult = $this->landingPageValidator->validate($landingPage);

        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
    }
}

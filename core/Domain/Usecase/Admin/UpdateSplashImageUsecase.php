<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\SplashImageRepositoryInterface;
use \Core\Domain\Entity\User\SplashImage;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Validator\SplashImageValidator;
use \Core\Service\Errors\ErrorsInterface;

class UpdateSplashImageUsecase
{
    protected $splashImageRepository;

    protected $splashImageValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        SplashImageRepositoryInterface $splashImageRepository,
        SplashImageValidator $splashImageValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->splashImageRepository = $splashImageRepository;
        $this->splashImageValidator  = $splashImageValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(SplashImage $splashImage, $params = array())
    {
        if (empty($params)) {
            $this->errors->setError('Invalid Parameters');
            return $this->errors;
        }
        $params = $this->htmlPurifier->purify($params);
        if (isset($params['image'])) {
            $splashImage->setImage($params['image']);
        }
        if (isset($params['position'])) {
            $splashImage->setPosition((int)$params['position']);
        }

        $validationResult = $this->splashImageValidator->validate($splashImage);

        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
        return $this->splashImageRepository->save($splashImage);
    }
}

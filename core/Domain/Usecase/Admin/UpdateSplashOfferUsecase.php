<?php
namespace Core\Domain\Usecase\Admin;

use \Core\Domain\Repository\SplashOfferRepositoryInterface;
use \Core\Domain\Entity\User\Splash;
use \Core\Domain\Adapter\PurifierInterface;
use \Core\Domain\Validator\SplashOfferValidator;
use \Core\Service\Errors\ErrorsInterface;

class UpdateSplashOfferUsecase
{
    protected $splashOfferRepository;

    protected $splashOfferValidator;

    protected $htmlPurifier;

    protected $errors;

    public function __construct(
        SplashOfferRepositoryInterface $splashOfferRepository,
        SplashOfferValidator $splashOfferValidator,
        PurifierInterface $htmlPurifier,
        ErrorsInterface $errors
    ) {
        $this->splashOfferRepository = $splashOfferRepository;
        $this->splashOfferValidator  = $splashOfferValidator;
        $this->htmlPurifier     = $htmlPurifier;
        $this->errors           = $errors;
    }

    public function execute(Splash $splashOffer, $params = array())
    {
        if (empty($params)) {
            $this->errors->setError('Invalid Parameters');
            return $this->errors;
        }
        $params = $this->htmlPurifier->purify($params);

        $params = $this->htmlPurifier->purify($params);
        if (isset($params['locale'])) {
            $splashOffer->setLocale($params['locale']);
        }
        if (isset($params['shopId'])) {
            $splashOffer->setShopId($params['shopId']);
        }
        if (isset($params['offerId'])) {
            $splashOffer->setPosition($params['offerId']);
        }
        if (isset($params['position'])) {
            $splashOffer->setPosition($params['position']);
        }
        $splashOffer->setUpdatedAt(new \DateTime('now'));

        $validationResult = $this->splashOfferValidator->validate($splashOffer);

        if (true !== $validationResult && is_array($validationResult)) {
            $this->errors->setErrors($validationResult);
            return $this->errors;
        }
        return $this->splashOfferRepository->save($splashOffer);
    }
}

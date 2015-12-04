<?php
namespace Core\Domain\Factory;

use \Core\Domain\Usecase\System\GetApiKeyUsecase;
use \Core\Domain\Usecase\System\DeactivateSleepingVisitors;
use \Core\Domain\Usecase\System\GetSettingUsecase;
use \Core\Domain\Usecase\System\GetSplashImagesUsecase;
use \Core\Domain\Usecase\System\GetSplashOffersUsecase;
use \Core\Domain\Usecase\Guest\GetOfferUsecase;
use \Core\Domain\Usecase\System\GetSplashPageUsecase;
use \Core\Domain\Usecase\System\GetNewsletterCampaignsUsecase;
use \Core\Persistence\Factory\RepositoryFactory;
use \Core\Domain\Service\Purifier;
use \Core\Service\Errors;

class SystemFactory
{
    public static function deactivateSleepingVisitors()
    {
        return new DeactivateSleepingVisitors(RepositoryFactory::visitor());
    }

    public static function getApiKey()
    {
        return new GetApiKeyUsecase(RepositoryFactory::apiKeys(), new Purifier());
    }

    public static function getSetting()
    {
        return new GetSettingUsecase(RepositoryFactory::settings(), new Purifier(), new Errors());
    }

    public static function getSplashOffers()
    {
        return new GetSplashOffersUsecase(RepositoryFactory::splashOffer(), new Purifier(), new Errors());
    }

    public static function getOffer($locale = '')
    {
        return new GetOfferUsecase(RepositoryFactory::offer($locale), new Purifier(), new Errors());
    }

    public static function getSplashPage()
    {
        return new GetSplashPageUsecase(RepositoryFactory::splashPage(), new Purifier(), new Errors());
    }

    public static function getSplashImages()
    {
        return new GetSplashImagesUsecase(RepositoryFactory::splashImage(), new Purifier(), new Errors());
    }

    public static function getNewsletterCampaigns()
    {
        return new GetNewsletterCampaignsUsecase(RepositoryFactory::newsletterCampaign(), new Purifier(), new Errors());
    }
}

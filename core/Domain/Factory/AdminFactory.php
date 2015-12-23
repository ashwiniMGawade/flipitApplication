<?php
namespace Core\Domain\Factory;

use \Core\Domain\Service\Validator;
use \Core\Domain\Service\KeyGenerator;
use \Core\Domain\Service\Purifier;

use \Core\Domain\Usecase\Admin\AddSplashOfferUsecase;
use \Core\Domain\Usecase\Admin\CreateSplashOfferUsecase;
use \Core\Domain\Usecase\Admin\DeleteSplashOfferUsecase;
use \Core\Domain\Usecase\Admin\GetSplashOfferUsecase;
use \Core\Domain\Usecase\Admin\UpdateSplashOfferUsecase;
use \Core\Domain\Usecase\Admin\UpdateSplashPageUsecase;

use \Core\Domain\Usecase\Admin\AddSplashImageUsecase;
use \Core\Domain\Usecase\Admin\CreateSplashImageUsecase;
use \Core\Domain\Usecase\Admin\GetSplashImageUsecase;
use \Core\Domain\Usecase\Admin\DeleteSplashImageUsecase;
use \Core\Domain\Usecase\Admin\UpdateSplashImageUsecase;

use \Core\Domain\Usecase\Admin\GetApiKeysUsecase;
use \Core\Domain\Usecase\Admin\CreateApiKeyUsecase;
use \Core\Domain\Usecase\Admin\AddApiKeyUsecase;
use \Core\Domain\Usecase\Admin\DeleteApiKeyUsecase;

use \Core\Domain\Usecase\Admin\GetSettingsUsecase;
use \Core\Domain\Usecase\Admin\UpdateSettingUsecase;

use \Core\Domain\Usecase\Admin\GetShopsUsecase;
use \Core\Domain\Usecase\Admin\GetShopUsecase;
use \Core\Domain\Usecase\Admin\CreateShopUsecase;
use \Core\Domain\Usecase\Admin\AddShopUsecase;
use \Core\Domain\Usecase\Admin\UpdateShopUsecase;
use \Core\Domain\Usecase\Admin\DeleteShopUsecase;

use \Core\Domain\Usecase\Admin\GetVisitorsUsecase;
use \Core\Domain\Usecase\Admin\UpdateVisitorUsecase;
use \Core\Domain\Usecase\System\GetVisitorUsecase;

use \Core\Domain\Usecase\Admin\CreateWidgetUsecase;
use \Core\Domain\Usecase\Admin\AddWidgetUsecase;
use \Core\Domain\Usecase\Admin\GetWidgetUsecase;
use \Core\Domain\Usecase\Admin\UpdateWidgetUsecase;

use \Core\Domain\Usecase\Admin\AddLandingPageUsecase;
use \Core\Domain\Usecase\Admin\CreateLandingPageUsecase;
use \Core\Domain\Usecase\Admin\UpdateLandingPageUsecase;
use \Core\Domain\Usecase\Admin\GetLandingPageUsecase;
use \Core\Domain\Usecase\Admin\GetLandingPagesUsecase;
use \Core\Domain\Usecase\Admin\DeleteLandingPageUsecase;

use \Core\Domain\Usecase\Admin\AddURLSettingUsecase;
use \Core\Domain\Usecase\Admin\CreateURLSettingUsecase;
use \Core\Domain\Usecase\Admin\GetURLSettingsUsecase;
use \Core\Domain\Usecase\Admin\GetURLSettingUsecase;
use \Core\Domain\Usecase\Admin\UpdateURLSettingUsecase;
use \Core\Domain\Usecase\Admin\DeleteURLSettingUsecase;

use \Core\Domain\Usecase\Admin\GetNewsletterCampaignUsecase;
use \Core\Domain\Usecase\Admin\CreateNewsletterCampaignUsecase;
use \Core\Domain\Usecase\Admin\AddNewsletterCampaignUsecase;
use \Core\Domain\Usecase\Admin\UpdateNewsletterCampaignUsecase;
use \Core\Domain\Usecase\Admin\DeleteNewsletterCampaignUsecase;

use \Core\Domain\Validator\ApiKeyValidator;
use \Core\Domain\Validator\LandingPageValidator;
use \Core\Domain\Validator\SettingsValidator;
use \Core\Domain\Validator\ShopValidator;
use \Core\Domain\Validator\SplashImageValidator;
use \Core\Domain\Validator\SplashOfferValidator;
use \Core\Domain\Validator\SplashPageValidator;
use \Core\Domain\Validator\VisitorValidator;
use \Core\Domain\Validator\WidgetValidator;
use \Core\Domain\Validator\UrlSettingValidator;
use \Core\Domain\Validator\NewsletterCampaignValidator;

use \Core\Persistence\Factory\RepositoryFactory;

use \Core\Service\Errors;

class AdminFactory
{
    public static function getApiKeys()
    {
        return new GetApiKeysUsecase(RepositoryFactory::apiKeys(), new Purifier(), new Errors());
    }

    public static function createApiKey()
    {
        return new CreateApiKeyUsecase();
    }

    public static function addApiKey()
    {
        return new AddApiKeyUsecase(
            RepositoryFactory::apiKeys(),
            new ApiKeyValidator(new Validator()),
            new KeyGenerator()
        );
    }

    public static function deleteApiKey()
    {
        return new DeleteApiKeyUsecase(RepositoryFactory::apiKeys());
    }


    public static function getVisitors()
    {
        return new GetVisitorsUsecase(RepositoryFactory::visitor(), new Purifier(), new Errors());
    }

    public static function updateVisitor()
    {
        return new UpdateVisitorUsecase(
            RepositoryFactory::visitor(),
            new VisitorValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function getShop()
    {
        return new GetShopUsecase(RepositoryFactory::shop(), new Purifier());
    }

    public static function getShops()
    {
        return new GetShopsUsecase(RepositoryFactory::shop(), new Purifier(), new Errors());
    }

    public static function createShop()
    {
        return new CreateShopUsecase();
    }

    public static function addShop()
    {
        return new AddShopUsecase(
            RepositoryFactory::shop(),
            new ShopValidator(new Validator()),
            RepositoryFactory::affliateNetwork(),
            new Purifier()
        );
    }

    public static function updateShop()
    {
        return new UpdateShopUsecase(
            RepositoryFactory::shop(),
            new ShopValidator(new Validator()),
            RepositoryFactory::affliateNetwork(),
            new Purifier()
        );
    }

    public static function deleteShop()
    {
        return new DeleteShopUsecase(RepositoryFactory::shop());
    }

    public static function createWidget()
    {
        return new CreateWidgetUsecase();
    }

    public static function addWidget()
    {
        return new AddWidgetUsecase(
            RepositoryFactory::widget(),
            new WidgetValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function getWidget()
    {
        return new GetWidgetUsecase(RepositoryFactory::widget(), new Purifier(), new Errors());
    }

    public static function updateWidget()
    {
        return new UpdateWidgetUsecase(
            RepositoryFactory::widget(),
            new WidgetValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function getLandingPages()
    {
        return new GetLandingPagesUsecase(RepositoryFactory::landingPage(), new Purifier(), new Errors());
    }

    public static function getLandingPage()
    {
        return new GetLandingPageUsecase(RepositoryFactory::landingPage(), new Purifier(), new Errors());
    }

    public static function deleteLandingPage()
    {
        return new DeleteLandingPageUsecase(RepositoryFactory::landingPage());
    }

    public static function createLandingPage()
    {
        return new CreateLandingPageUsecase();
    }

    public static function addLandingPage()
    {
        return new AddLandingPageUsecase(
            RepositoryFactory::landingPage(),
            new LandingPageValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function updateLandingPage()
    {
        return new UpdateLandingPageUsecase(
            RepositoryFactory::landingPage(),
            new LandingPageValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function getSettings()
    {
        return new GetSettingsUsecase(
            RepositoryFactory::settings(),
            new Purifier(),
            new Errors()
        );
    }

    public static function updateSetting()
    {
        return new UpdateSettingUsecase(
            RepositoryFactory::settings(),
            new SettingsValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function getURLSettings()
    {
        return new GetURLSettingsUsecase(RepositoryFactory::urlSetting(), new Purifier(), new Errors());
    }

    public static function createURLSetting()
    {
        return new CreateURLSettingUsecase();
    }

    public static function addURLSetting()
    {
        return new AddURLSettingUsecase(
            RepositoryFactory::urlSetting(),
            new UrlSettingValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function getURLSetting()
    {
        return new GetURLSettingUsecase(RepositoryFactory::urlSetting(), new Purifier(), new Errors());
    }

    public static function updateURLSetting()
    {
        return new UpdateURLSettingUsecase(
            RepositoryFactory::urlSetting(),
            new UrlSettingValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function deleteURLSetting()
    {
        return new DeleteURLSettingUsecase(RepositoryFactory::urlSetting());
    }

    public static function createSplashOffer()
    {
        return new CreateSplashOfferUsecase();
    }

    public static function addSplashOffer()
    {
        return new AddSplashOfferUsecase(
            RepositoryFactory::splashOffer(),
            new SplashOfferValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function getSplashOffer()
    {
        return new GetSplashOfferUsecase(RepositoryFactory::splashOffer(), new Purifier(), new Errors());
    }

    public static function deleteSplashOffer()
    {
        return new DeleteSplashOfferUsecase(RepositoryFactory::splashOffer());
    }

    public static function updateSplashOffer()
    {
        return new UpdateSplashOfferUsecase(
            RepositoryFactory::splashOffer(),
            new SplashOfferValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function updateSplashPage()
    {
        return new UpdateSplashPageUsecase(
            RepositoryFactory::splashPage(),
            new SplashPageValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function createSplashImage()
    {
        return new CreateSplashImageUsecase();
    }

    public static function addSplashImage()
    {
        return new AddSplashImageUsecase(
            RepositoryFactory::splashImage(),
            new SplashImageValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function getSplashImage()
    {
        return new GetSplashImageUsecase(RepositoryFactory::splashImage(), new Purifier(), new Errors());
    }

    public static function updateSplashImage()
    {
        return new UpdateSplashImageUsecase(
            RepositoryFactory::splashImage(),
            new SplashImageValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function deleteSplashImages()
    {
        return new DeleteSplashImageUsecase(RepositoryFactory::splashImage());
    }

    public static function getVisitor()
    {
        return new GetVisitorUsecase(RepositoryFactory::visitor(), new Purifier(), new Errors());
    }

    public static function getNewsletterCampaign()
    {
        return new GetNewsletterCampaignUsecase(RepositoryFactory::newsletterCampaign(), new Purifier(), new Errors());
    }

    public static function createNewsletterCampaign()
    {
        return new CreateNewsletterCampaignUsecase();
    }

    public static function addNewsletterCampaign()
    {
        return new AddNewsletterCampaignUsecase(
            RepositoryFactory::newsletterCampaign(),
            new NewsletterCampaignValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function updateNewsletterCampaign()
    {
        return new UpdateNewsletterCampaignUsecase(
            RepositoryFactory::newsletterCampaign(),
            new NewsletterCampaignValidator(new Validator()),
            new Purifier(),
            new Errors()
        );
    }

    public static function deleteNewsletterCampaign()
    {
        return new DeleteNewsletterCampaignUsecase(RepositoryFactory::newsletterCampaign());
    }

    public static function getNewsletterCampaignOffer()
    {
        return new GetNewsletterCampaignOfferUsecase(RepositoryFactory::newsletterCampaignOffer(), new Purifier(), new Errors());
    }
}

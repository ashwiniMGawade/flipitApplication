<?php
namespace Core\Persistence\Factory;

use \Core\Persistence\Database\Repository\DashboardRepository;
use \Core\Persistence\Database\Repository\LocaleSettingRepository;
use \Core\Persistence\Database\Repository\SettingsRepository;
use \Core\Persistence\Database\Repository\SplashImageRepository;
use \Core\Persistence\Database\Repository\SplashOfferRepository;
use \Core\Persistence\Database\Repository\SplashPageRepository;
use \Core\Persistence\Database\Service as Service;
use \Core\Persistence\Database\Repository\OfferRepository;
use \Core\Persistence\Database\Repository\ViewCountRepository;
use \Core\Persistence\Database\Repository\VisitorRepository;
use \Core\Persistence\Database\Repository\PageRepository;
use \Core\Persistence\Database\Repository\ShopRepository;
use \Core\Persistence\Database\Repository\ApiKeyRepository;
use \Core\Persistence\Database\Repository\AffliateNetworkRepository;
use \Core\Persistence\Database\Repository\WidgetRepository;
use \Core\Persistence\Database\Repository\LandingPageRepository;
use \Core\Persistence\Database\Repository\URLSettingRepository;
use \Core\Persistence\Database\Repository\NewsletterCampaignRepository;

class RepositoryFactory
{
    public static function page()
    {
        return new PageRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function shop()
    {
        return new ShopRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function apiKeys()
    {
        return new ApiKeyRepository((new Service\DoctrineManager(new Service\AppConfig()))->getUserEntityManager());
    }

    public static function visitor()
    {
        return new VisitorRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function affliateNetwork()
    {
        return new AffliateNetworkRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function viewCount()
    {
        return new ViewCountRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function widget()
    {
        return new WidgetRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function offer($locale = '')
    {
        return new OfferRepository((new Service\DoctrineManager(new Service\AppConfig($locale)))->getLocaleEntityManager());
    }

    public static function landingPage()
    {
        return new LandingPageRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function settings()
    {
        return new SettingsRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function urlSetting()
    {
        return new URLSettingRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function splashOffer()
    {
        return new SplashOfferRepository((new Service\DoctrineManager(new Service\AppConfig()))->getUserEntityManager());
    }

    public static function dashboard($locale = '')
    {
        return new DashboardRepository((new Service\DoctrineManager(new Service\AppConfig($locale)))->getLocaleEntityManager());
    }
    public static function splashPage()
    {
        return new SplashPageRepository((new Service\DoctrineManager(new Service\AppConfig()))->getUserEntityManager());
    }

    public static function splashImage()
    {
        return new SplashImageRepository((new Service\DoctrineManager(new Service\AppConfig()))->getUserEntityManager());
    }

    public static function newsletterCampaign()
    {
        return new NewsletterCampaignRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }

    public static function localeSetting()
    {
        return new LocaleSettingRepository((new Service\DoctrineManager(new Service\AppConfig()))->getLocaleEntityManager());
    }
}

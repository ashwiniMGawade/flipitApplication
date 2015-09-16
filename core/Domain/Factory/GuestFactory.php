<?php

namespace Core\Domain\Factory;

use \Core\Domain\Service\Purifier;
use \Core\Domain\Usecase\Admin\GetLandingPageUsecase;
use \Core\Domain\Usecase\Guest\GetHomePageUsecase;
use \Core\Domain\Usecase\Guest\GetViewCountsUsecase;
use \Core\Domain\Usecase\Guest\GetOfferUsecase;
use \Core\Persistence\Factory\RepositoryFactory;
use \Core\Service\Errors;

class GuestFactory
{
    public static function getHomePage()
    {
        return new GetHomePageUsecase(RepositoryFactory::page());
    }

    public static function getViewCounts()
    {
        return new GetViewCountsUsecase(RepositoryFactory::viewCount(), new Purifier(), new Errors());
    }

    public static function getOffer()
    {
        return new GetOfferUsecase(RepositoryFactory::offer(), new Purifier(), new Errors());
    }

    public static function getLandingPage()
    {
        return new GetLandingPageUsecase(RepositoryFactory::landingPages(), new Purifier(), new Errors());
    }
}

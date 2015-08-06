<?php

namespace Core\Domain\Factory;

use \Core\Domain\Usecase\Guest\GetHomePageUsecase;
use Core\Domain\Usecase\Guest\GetOfferClickUsecase;
use \Core\Persistence\Factory\RepositoryFactory;

class GuestFactory
{
    public static function getHomePage()
    {
        return new GetHomePageUsecase(RepositoryFactory::page());
    }

    public static function getOfferClick()
    {
        return new GetOfferClickUsecase(RepositoryFactory::viewCount());
    }
}

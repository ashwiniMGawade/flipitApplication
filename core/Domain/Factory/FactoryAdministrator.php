<?php
namespace Core\Domain\Factory;

use Core\Domain\Usecase\Admin\GetsApiKeyListing;
use Core\Domain\Factory\FactoryRepository;

class FactoryAdministrator
{
    public static function getsApikeys()
    {
        return new GetsApiKeyListing(FactoryRepository::getApiKeys());
    }
}

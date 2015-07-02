<?php
namespace Core\Domain\Factory;

use Core\Domain\Usecase\Admin\GetsIpAddressListing;
use Core\Domain\Factory\FactoryRepository;

class FactoryAdministrator
{
	public static function getsIpAddress()
	{
		return new GetsIpAddressListing(FactoryRepository::getIpAdress());
	}
}
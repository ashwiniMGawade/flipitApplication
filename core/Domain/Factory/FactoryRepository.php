<?php
namespace Core\Domain\Factory;

use Core\Persistence\Database\Repository\IpAddressRepository;
use Doctrine\ORM\EntityManager;

class FactoryRepository
{
	public static function getIpAdress()
	{
		return new IpAddressRepository(new EntityManager());
	}
}
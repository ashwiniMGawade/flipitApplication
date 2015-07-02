<?php
namespace Core\Persistence\Database\Repository;

use Core\Domain\Repository\IpAddressRepositoryInterface;
use Doctrine\ORM\EntityManager;

class IpAddressRepository implements IpAddressRepositoryInterface
{
	protected $entityManager;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function getAll()
	{

	}
}
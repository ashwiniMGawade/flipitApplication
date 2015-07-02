<?php
namespace Core\Domain\Usecase\Admin;

use Core\Domain\Repository\IpAddressRepositoryInterface;

class GetsIpAddressListing 
{
	private $ipAddressRepository;

	public function __construct(IpAddressRepositoryInterface $ipAddressRepository) 
	{
		$this->ipAddressRepository = $ipAddressRepository;
	}

	public function execute() 
	{
		return $this->ipAddressRepository->getAll();
	}
}
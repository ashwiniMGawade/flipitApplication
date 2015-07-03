<?php

namespace Core\Domain\Factory;

use \Core\Domain\Usecase\Guest\GetHomePageUsecase;
use \Core\Persistence\Factory\RepositoryFactory;

class GuestFactory
{
	public static function getHomePage()
	{
		return new GetHomePageUsecase(RepositoryFactory::page());
	}
}

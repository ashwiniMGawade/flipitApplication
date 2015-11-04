<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\User\Splash;
use \Core\Domain\Usecase\Admin\DeleteSplashOfferUsecase;

class DeleteSplashOfferUsecaseTest extends \Codeception\TestCase\Test
{

    public function testDeleteSplashOfferUsecase()
    {
        $splashOfferRepository = $this->createDeleteSplashOfferRepositoryInterfaceWithMethodsMock(true);
        $this->assertEquals(true, (new DeleteSplashOfferUsecase($splashOfferRepository))->execute(new Splash()));
    }

    private function createDeleteSplashOfferRepositoryInterfaceWithMethodsMock($returns)
    {
        $splashOfferRepository = $this->getMock('\Core\Domain\Repository\SplashOfferRepositoryInterface');
        $splashOfferRepository
            ->expects($this->once())
            ->method('remove')
            ->willReturn($returns);
        return $splashOfferRepository;
    }
}

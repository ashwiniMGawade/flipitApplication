<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\User\SplashImage;
use \Core\Domain\Usecase\Admin\DeleteSplashImageUsecase;

class DeleteSplashImageUsecaseTest extends \Codeception\TestCase\Test
{

    public function testDeleteSplashImageUsecase()
    {
        $splashImageRepository = $this->createDeleteSplashImageRepositoryInterfaceWithMethodsMock(true);
        $this->assertEquals(true, (new DeleteSplashImageUsecase($splashImageRepository))->execute(new SplashImage()));
    }

    private function createDeleteSplashImageRepositoryInterfaceWithMethodsMock($returns)
    {
        $splashImageRepository = $this->getMock('\Core\Domain\Repository\SplashImageRepositoryInterface');
        $splashImageRepository
            ->expects($this->once())
            ->method('remove')
            ->willReturn($returns);
        return $splashImageRepository;
    }
}

<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\LandingPage;
use \Core\Domain\Usecase\Admin\DeleteLandingPageUsecase;

class DeleteLandingPageUsecaseTest extends \Codeception\TestCase\Test
{

    public function testDeleteLandingPageUsecase()
    {
        $landingPageRepository = $this->createDeleteLandingPageRepositoryInterfaceWithMethodsMock(true);
        $this->assertEquals(true, (new DeleteLandingPageUsecase($landingPageRepository))->execute(new LandingPage()));
    }

    private function createDeleteLandingPageRepositoryInterfaceWithMethodsMock($returns)
    {
        $landingPageRepository = $this->getMock('\Core\Domain\Repository\LandingPageRepositoryInterface');
        $landingPageRepository
            ->expects($this->once())
            ->method('remove')
            ->willReturn($returns);
        return $landingPageRepository;
    }
}

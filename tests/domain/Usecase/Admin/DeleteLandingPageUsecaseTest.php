<?php
namespace Usecase\Admin;

use \Core\Domain\Entity\LandingPages;
use \Core\Domain\Usecase\Admin\DeleteLandingPageUsecase;

class DeleteLandingPageUsecaseTest extends \Codeception\TestCase\Test
{

    public function testDeleteLandingPageUsecase()
    {
        $landingPagesRepository = $this->createDeleteLandingPageRepositoryInterfaceWithMethodsMock(true);
        $this->assertEquals(true, (new DeleteLandingPageUsecase($landingPagesRepository))->execute( new LandingPages()));
    }

    private function landingPageRepositoryInterfaceMock()
    {
        $apiKeyRepository = $this->getMock('\Core\Domain\Repository\LandingPagesRepositoryInterface');
        return $apiKeyRepository;
    }

    private function createDeleteLandingPageRepositoryInterfaceWithMethodsMock($returns)
    {
        $apiKeyRepository = $this->landingPageRepositoryInterfaceMock();
        $apiKeyRepository
            ->expects($this->once())
            ->method('remove')
            ->willReturn($returns);
        return $apiKeyRepository;
    }
}

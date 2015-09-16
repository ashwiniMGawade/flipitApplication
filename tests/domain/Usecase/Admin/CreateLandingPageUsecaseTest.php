<?php
namespace Usecase\Admin;

use Core\Domain\Usecase\Admin\CreateLandingPageUsecase;

class CreateLandingPageUsecaseTest extends \Codeception\TestCase\Test
{
    protected $tester;

    public function testCreateLandingPageUsecase()
    {
        $landingPage = (new CreateLandingPageUsecase())->execute();
        $this->assertInstanceOf('\Core\Domain\Entity\LandingPages', $landingPage);
    }
}

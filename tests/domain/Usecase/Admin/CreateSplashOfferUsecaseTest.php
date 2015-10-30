<?php
namespace Usecase\Admin;

use Core\Domain\Usecase\Admin\CreateSplashOfferUsecase;

class CreateSplashOfferUsecaseTest extends \Codeception\TestCase\Test
{
    public function testCreateSplashOfferUsecase()
    {
        $this->assertInstanceOf(
            '\Core\Domain\Entity\User\Splash',
            (new CreateSplashOfferUsecase())->execute()
        );
    }
}

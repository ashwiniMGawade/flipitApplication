<?php
namespace Usecase\Admin;

use Core\Domain\Usecase\Admin\CreateSplashImageUsecase;

class CreateSplashImageUsecaseTest extends \Codeception\TestCase\Test
{
    public function testCreateSplashOfferUsecase()
    {
        $this->assertInstanceOf(
            '\Core\Domain\Entity\User\SplashImage',
            (new CreateSplashImageUsecase())->execute()
        );
    }
}

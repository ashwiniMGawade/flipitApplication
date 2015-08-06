<?php
namespace Usecase\Guest;

use Core\Domain\Usecase\Guest\GetOfferClickUsecase;

class GetOfferClickUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    // tests
    public function testGetOfferClickUsecase()
    {
        (new GetOfferClickUsecase(new ViewCountRepository()))->execute();
    }
}

<?php
namespace Usecase\Guest;

use \Core\Domain\Usecase\Guest\GetHomePageUsecase;

class GetHomePageUsecaseTest extends \Codeception\TestCase\Test
{
    /**
     * @var \DomainTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testGetHomePage()
    {
        $uri = 'top-20';
        $homepage = new GetHomePageUsecase(
            $this->pageRepositoryMock()
        );
        $homepage->excute($uri);
    }

    private function pageRepositoryMock()
    {
        $pageRepositoryMock = $this
            ->getMock('\Core\Domain\Repository\PageRepositoryInterface');
        $pageRepositoryMock
            ->expects($this->once())
            ->method('findOneBy');
        return $pageRepositoryMock;
    }
}
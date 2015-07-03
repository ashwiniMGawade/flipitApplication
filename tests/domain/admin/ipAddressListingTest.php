<?php
use Core\Domain\Usecase\Admin;
use Core\Persistence\Database\Repository\IpAddressRepository;

class ipAddressListingTest extends \Codeception\TestCase\Test
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

    public function testGetIpAddressListing()
    {
        $ipAddressRepository = $this->_create_ipAddressRepository_mock();
        $GetsIpAddressListing = new Core\Domain\Usecase\Admin\GetsIpAddressListing($ipAddressRepository);
        $GetsIpAddressListing->execute();
    }

    private function _create_ipAddressRepository_mock()
    {
        $ipAddressRepository = $this->getMockBuilder('Core\Persistence\Database\Repository\IpAddressRepository')
                                    ->disableOriginalConstructor()
                                    ->getMock();

        $ipAddressRepository->expects($this->once())
                            ->method('getAll');

        return $ipAddressRepository;
    }
}
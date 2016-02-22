<?php
namespace Usecase\Admin;

use Core\Domain\Entity\BulkEmail;
use \Core\Domain\Usecase\Admin\SendTestNewsletterUsecase;

class SendTestNewsletterUsecaseTest extends \Codeception\TestCase\Test
{
    public function testSendTestNewsletterUsecaseSuccessWhenPassedProperParams()
    {
        $bulkEmail = $this->bulkEmailObject(1, 1, 'in');
        $bulkEmailRepositoryMock = $this->bulkEmailRepositoryInterfaceMock($bulkEmail);
        $bulkEmailUsecase = new SendTestNewsletterUsecase($bulkEmailRepositoryMock);
        $result = $bulkEmailUsecase->execute(1, 1, 'in');
        $this->assertTrue($result);
    }

    private function bulkEmailRepositoryInterfaceMock($expectedBulkEmail)
    {
        $bulkEmailRepositoryMock = $this->getMockBuilder('\Core\Domain\Repository\BulkEmailRepositoryInterface')->getMock();
        $bulkEmailRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($expectedBulkEmail)
            ->willReturn(true);
        return $bulkEmailRepositoryMock;
    }

    private function bulkEmailObject($newsletterCampaignId, $intVisitorId, $local)
    {
        $bulkEmail = new BulkEmail();
        $bulkEmail->setTimeStamp(time()*1000);
        $bulkEmail->setReferenceId($newsletterCampaignId);
        $bulkEmail->setEmailType('testnewsletter');
        $bulkEmail->setLocal($local);
        $bulkEmail->setUserId($intVisitorId);
        return $bulkEmail;
    }
}

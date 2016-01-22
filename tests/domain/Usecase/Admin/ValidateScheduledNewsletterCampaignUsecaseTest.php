<?php
namespace Usecase\Admin;

use \Core\Domain\Usecase\Admin\ValidateScheduledNewsletterCampaignUsecase;

class ValidateScheduledNewsletterCampaignUsecaseTest extends \Codeception\TestCase\Test
{
    public function testValidateScheduledNewsletterCampaignUsecaseReturnsResponseWithValidParameters()
    {
        $params = array(
            'scheduleDate' => new \DateTime('now'),
            'campaignSubject' => "test",
            'campaignHeader' => "test",
            'campaignFooter' => "test",
            'senderName' => "test",
        );
        $result = (new ValidateScheduledNewsletterCampaignUsecase())->execute($params);
        $this->assertEquals($result, array());
    }

    public function testValidateScheduledNewsletterCampaignUsecaseReturnsResponseWithInValidParameters()
    {
        $params = array(
            'scheduleDate' => '',
            'campaignSubject' => "",
            'campaignHeader' => "",
            'campaignFooter' => "",
            'senderName' => "",
        );
        $result = (new ValidateScheduledNewsletterCampaignUsecase())->execute($params);
        $this->assertEquals($result, array(
            'error' => array(
                "scheduleDate" => "Please enter campaign scheduled Date",
                "campaignSubject" => "Please enter campaign subject",
                "campaignHeader" => "Please enter campaign Header",
                "campaignFooter" => "Please enter campaign Footer",
                "senderName" => "Please enter sender Name",
            )
        ));
    }
}

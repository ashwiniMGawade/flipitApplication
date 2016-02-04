<?php
namespace Admin;

use \ApiTester;

class GetNewsletterCampaignsCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = $I->getConfig('apiKey');
    }

    public function testGetNewsletterCampaignsReturnsNewsletterCampaignsWhenRecordExists(ApiTester $I)
    {
        $I->wantTo('Test GET newsletter campaigns returns newsletter campaigns when record exists');
        $this->seedNewsletterCampaignsTable($I, 'test@example.com');
        $I->sendGet('/newslettercampaigns?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(array('senderEmail' => 'test@example.com'));
    }

    public function testGetNewsletterCampaignsReturnsEmptyWhenRecordDoesNotExists(ApiTester $I)
    {
        $I->wantTo('Test GET newsletter campaigns returns empty when record does not exists');
        $I->sendGet('/newslettercampaigns?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->cantSeeResponseContainsJson(array('senderEmail' => 'test@example.com'));
    }

    private function seedNewsletterCampaignsTable($I, $email)
    {
        $I->haveInDatabasePDOSite(
            'newsletterCampaigns',
            array(
                'id' =>1,
                'campaignName' => 'Campaign Name',
                'campaignSubject' => 'Test Subject',
                'senderName' => 'Flipit Data',
                'senderEmail' => $email,
                'header' => 'Header Text Goes here',
                'footer' => 'Footer Text Goes here'
            )
        );
    }
}

<?php
namespace Admin;

use \ApiTester;

class GetNewsletterCampaignCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = $I->getConfig('apiKey');
    }

    public function testGetNewsletterCampaignReturnsNewsletterCampaignWhenRecordExists(ApiTester $I)
    {
        $I->wantTo('Test GET newsletter campaign returns newsletter campaign record when record exists');
        $this->seedNewsletterCampaignsTable($I);
        $I->sendGet('/newslettercampaigns/1?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }

    public function testGetNewsletterCampaignGivesErrorWhenInvalidIdPassed(ApiTester $I)
    {
        $I->wantTo('Test GET newsletter campaign gives error when invalid id passed');
        $I->sendGet('/newslettercampaigns/test?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['messages' => array('Invalid newsletter campaign Id')]);
    }

    public function testGetNewsletterCampaignGivesErrorWhenRecordDoesNotExists(ApiTester $I)
    {
        $I->wantTo('Test GET newsletter campaign gives error when record does not exists');
        $I->sendGet('/newslettercampaigns/0?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['messages' => array('Newsletter Campaign not found')]);
    }

    private function seedNewsletterCampaignsTable($I)
    {
        $I->haveInDatabasePDOSite(
            'newsletterCampaigns',
            array(
                'id' =>1,
                'campaignName' => 'Campaign Name',
                'campaignSubject' => 'Test Subject',
                'senderName' => 'Flipit Data',
                'senderEmail' => 'test@test.com',
                'header' => 'Header Text Goes here',
                'footer' => 'Footer Text Goes here'
            )
        );
    }
}

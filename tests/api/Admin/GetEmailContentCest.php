<?php
namespace Admin;

use \ApiTester;

class GetEmailContentCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = $I->getConfig('apiKey');
    }

    public function testGetEmailContentReturnsEmailContentWhenProperRequestPassed(ApiTester $I)
    {
        $I->wantTo('Test GET emailcontents returns email content when proper request passed');
        $this->seedNewsletterCampaignTable($I);
        $I->sendGet('/emailcontents/newsletter/1?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseJsonMatchesJsonPath('$.content');
    }

    public function testGetEmailContentGivesErrorWhenNewsletterCampaignRecordDoesNotExists(ApiTester $I)
    {
        $I->wantTo('Test GET emailcontents gives error when newsletter campaign record does not exists');
        $I->sendGet('/emailcontents/newsletter/100?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['messages' => array('Newsletter campaign record not found')]);
    }

    public function testGetEmailContentGivesErrorWhenInvalidTypePassed(ApiTester $I)
    {
        $I->wantTo('Test GET emailcontents gives error when invalid type passed');
        $I->sendGet('/emailcontents/test/1?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['messages' => array('Invalid email type')]);
    }

    public function testGetEmailContentGivesErrorWhenInvalidReferenceIdPassed(ApiTester $I)
    {
        $I->wantTo('Test GET emailcontent gives error when invalid type passed');
        $I->sendGet('/emailcontents/newsletter/test?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['messages' => array('Invalid reference Id')]);
    }

    private function seedNewsletterCampaignTable($I)
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

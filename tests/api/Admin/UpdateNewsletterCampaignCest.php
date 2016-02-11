<?php
namespace Admin;

use \ApiTester;

class UpdateNewsletterCampaignCest
{
    protected $apiKey;

    public function _before(ApiTester $I)
    {
        $this->apiKey = $I->getConfig('apiKey');
    }

    public function testPutNewsletterCampaignGivesErrorWhenInvalidIdPassed(ApiTester $I)
    {
        $I->wantTo('Test PUT newsletter campaign gives error when invalid id passed');
        $this->seedNewsletterCampaignsTable($I, 'test@example.com');
        $params = json_encode(array('senderEmail' => 'ramesh@example.com'));
        $expectedResult = array('messages' => array( 'Invalid newsletter campaign Id'));
        $status = 400;
        $this->runTest($I, 'dfdfdf', $params, $expectedResult, $status);
    }

    public function testPutNewsletterCampaignGivesErrorWhenIdNotSpecified(ApiTester $I)
    {
        $I->wantTo('Test PUT newsletter campaign gives error when id does not passed');
        $I->sendPUT('/newslettercampaigns/?api_key='.$this->apiKey);
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['messages' => 'Not found']);
    }

    public function testPutNewsletterCampaignUpdatesNewsletterCampaignWhenValidInputPassed(ApiTester $I)
    {
        $I->wantTo('Test PUT newsletter campaign updates newsletter campaign when valid input passed');
        $this->seedNewsletterCampaignsTable($I, 'test@example.com');
        $params = json_encode(array('senderEmail' => 'flipit@example.com'));
        $expectedResult = array('senderEmail' => 'flipit@example.com');
        $status = 200;
        $this->runTest($I, 1, $params, $expectedResult, $status);
    }

    public function testPutNewsletterCampaignUpdatesNewsletterCampaignWhenValidInputPassed1(ApiTester $I)
    {
        $I->wantTo('Test PUT newsletter campaign updates newsletter campaign when valid input passed');
        $this->seedNewsletterCampaignsTable($I, 'test@example.com');
        $params = json_encode(array('senderEmail' => 'flipit@example.com'));
        $expectedResult = array('Newsletter Campaign not found');
        $status = 404;
        $this->runTest($I, 2, $params, $expectedResult, $status);
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

    private function runTest($I, $id, $params, $expectedResult, $status)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPUT('/newslettercampaigns/'.$id.'?api_key='.$this->apiKey, $params);
        $I->seeResponseCodeIs($status);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson($expectedResult);
    }
}

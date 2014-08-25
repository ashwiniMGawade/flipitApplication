<?php
use \AcceptanceTester;

class clickoutsCest
{
    public function _before()
    {
    }

    public function _after()
    {
    }

    public function sidebarClickout(AcceptanceTester $I)
    {
        echo APPLICATION_ENV; die;
       //$test = $I->grabFromDatabase('category','id', array('name' =>'Software'));
$test = new Varnish();
$test->addUrl('testt');
      //  $I->canSeeInDatabase('offer', array('url' => 'asdasd'));
    //    $I->canSeeInDatabase('offer', array('url' => 'http://www.flipit.com/in/babyoye'));   
        /*$I->haveInDatabase(
            'shop',
            array(
                'name' => 'acceptance shop',
                'permalink' => 'acceptance-shop',
                'title' => 'acceptance shop title',
                'subTitle' => 'acceptance shop title',
                'subTitle' => 'acceptance shop title',                                                                                                                                                                                                                                                                                                                                                                                                         
                'subTitle' => 'acceptance shop title',
                'subTitle' => 'acceptance shop title',
                'subTitle' => 'acceptance shop title',
                'subTitle' => 'acceptance shop title',
                'subTitle' => 'acceptance shop title',
                'subTitle' => 'acceptance shop title',
                'subTitle' => 'acceptance shop title'
            )
        );*/
 
     }
    /*
    public function saleClickout(AcceptanceTester $I)
    {
        $I->amOnPage('/in/acceptance-shop');
        $I->click('Click to Visit Sale');
        $I->executeInSelenium(function (\Webdriver $webdriver) {
            $handles=$webdriver->getWindowHandles();
            $last_window = end($handles);
            $webdriver->switchTo()->window($last_window);
        });
        $I->wait(10);
        $I->seeInCurrentUrl('/in');

        $I->wait(5);
        $I->amOnPage('/in/acceptance-shop');
        $I->click('.clickout-title a');
        $I->wait(5);
        $I->seeInCurrentUrl('/in');
        $I->wait(5);

        $I->amOnPage('/in/acceptance-shop');
        $I->click('.small-code');
        $I->wait(5);
        $I->seeInCurrentUrl('/in');
        $I->wait(5);
    }

    public function printableClickout(AcceptanceTester $I)
    {
        $I->amOnPage('/in/acceptance-shop');
        $I->click('Click to View Information');
        $I->seeInCurrentUrl('/in');
        $I->wait(5);
        $I->amOnPage('/in/acceptance-shop');
        $I->click('.btn-print');
        $I->executeInSelenium(function (\Webdriver $webdriver) {
            $handles=$webdriver->getWindowHandles();
            $last_window = end($handles);
            $webdriver->switchTo()->window($last_window);
        });
        $I->wait(5);
        $I->seeInCurrentUrl('/in');
        $I->wait(5);
        $I->switchToWindow();

        $I->wait(5);
        $I->amOnPage('/in/acceptance-shop');
        $I->click('.clickout-title a');
        $I->wait(5);
        $I->seeInCurrentUrl('/in');
        $I->wait(5);

        $I->amOnPage('/in/acceptance-shop');
        $I->click('img[alt=printable]');
        $I->wait(5);
        $I->seeInCurrentUrl('/in');
        $I->wait(5);
    }

    public function headerLinkClickout(AcceptanceTester $I)
    {
        $I->amOnPage('/in/acceptance-shop');
        $I->click('.header-block-2 .box');
        $I->switchToWindow();
        $I->seeInCurrentUrl('/in');
    }

    public function headerImageClickout(AcceptanceTester $I)
    {
        $I->amOnPage('/in/acceptance-shop');
        $I->click('.icon a');
        $I->switchToWindow();
        $I->seeInCurrentUrl('/in');
    }

    public function expiredClickout(AcceptanceTester $I)
    {
        $I->amOnPage('/in/acceptance-shop');
        $I->click('.line a');
        $I->switchToWindow();
    }

    public function couponCodeClickout(AcceptanceTester $I)
    {
        $I->amOnPage('/in/acceptance-shop');
        $I->click('Get code & Open site');
        $I->executeInSelenium(function (\Webdriver $webdriver) {
            $handles=$webdriver->getWindowHandles();
            $last_window = end($handles);
            $webdriver->switchTo()->window($last_window);
        });
        $I->wait(10);
        $I->canSeeInPageSource('id="code-lightbox"');
        $I->canSeeInPageSource('id="code-button"');
    }*/
}

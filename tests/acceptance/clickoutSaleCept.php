<?php
$I = new AcceptanceTester($scenario);
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

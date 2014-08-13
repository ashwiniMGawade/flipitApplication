<?php
$I = new AcceptanceTester($scenario);
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

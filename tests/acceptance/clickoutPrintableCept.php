<?php
$I = new AcceptanceTester($scenario);
$I->amOnPage('/in/acceptance-shop');
$I->click('Click to View Information');
$I->seeInCurrentUrl('/in');
$I->wait(10);
$I->amOnPage('/in/acceptance-shop');
$I->click('.btn-print');
$I->executeInSelenium(function (\Webdriver $webdriver) {
    $handles=$webdriver->getWindowHandles();
    $last_window = end($handles);
    $webdriver->switchTo()->window($last_window);
});
$I->wait(10);
$I->seeInCurrentUrl('/in');

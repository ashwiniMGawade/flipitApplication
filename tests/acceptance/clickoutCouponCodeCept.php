<?php
$I = new AcceptanceTester($scenario);
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

<?php
$I = new AcceptanceTester($scenario);
$I->amOnPage('/be');
$I->click('img[alt=Zalando]');
$I->amOnPage('/be/zalando');
$I->click('.icon a');
$I->wait(100);
$I->switchToWindow();
$I->wait(10);
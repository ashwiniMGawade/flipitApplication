<?php
$I = new AcceptanceTester($scenario);
$I->amOnPage('/in/acceptance-shop');
$I->click('.icon a');
$I->switchToWindow();
$I->seeInCurrentUrl('/in');

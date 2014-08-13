<?php
$I = new AcceptanceTester($scenario);
$I->amOnPage('/in/acceptance-shop');
$I->maximizeWindow();
$I->click('.web a');
$I->switchToWindow();
$I->seeInCurrentUrl('/in');

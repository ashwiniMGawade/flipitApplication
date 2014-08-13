<?php
$I = new AcceptanceTester($scenario);
$I->amOnPage('/in/acceptance-shop');
$I->click('.header-block-2 .box');
$I->switchToWindow();
$I->seeInCurrentUrl('/in');

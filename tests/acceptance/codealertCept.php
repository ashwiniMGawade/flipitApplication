<?php 
$I = new AcceptanceTester\LoginSteps($scenario);
$I->login();
$I->canSee('Code alert');
$I->click('Code alert');
$I->amOnPage('/admin/email/code-alert');
$I->wait(10);
$I->click('.store-offer a');
$I->seeInCurrentUrl('/admin/offer/editoffer/id');

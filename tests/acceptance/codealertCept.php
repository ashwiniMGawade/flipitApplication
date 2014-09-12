<?php 
$I = new AcceptanceTester\LoginSteps($scenario);
$I->login();
$I->canSee('Code alert');
$I->click('Code alert');
$I->amOnPage('/admin/email/code-alert');
$I->canSee('Code alert settings');
$I->click('Code alert settings');
$I->amOnPage('/admin/email/code-alert-settings');
$I->seeInField('#emailSubject', 'test');
$I->seeInField('#emailHeader', 'test');
$I->seeElement('#sendNewsletter-btn');

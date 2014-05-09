<?php
$I = new WebGuy($scenario);
$I->wantTo('See if the email management is there.');
TestCommons::logInAdmin($I);
$I->click('Emails');
$I->see('Emails');

<?php 
$I = new AcceptanceTester($scenario);
$I->amOnPage('/in');
$I->see('save money');

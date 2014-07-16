<?php 
$I = new FunctionalTester\AdminSteps($scenario);
$I->wantTo('perform actions and see result');
$I->login('kim@web-flight.nl', 'password');
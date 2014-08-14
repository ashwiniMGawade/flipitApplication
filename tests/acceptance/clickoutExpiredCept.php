<?php
$I = new AcceptanceTester($scenario);
$I->amOnPage('/in/acceptance-shop');
$I->click('.line a');
$I->switchToWindow();

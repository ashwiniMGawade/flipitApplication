<?php
$I = new WebGuy($scenario);
$I->wantTo('Validate the layout working fine or not');
Frontend_test_layout_commons::validateLayoutHeader($I);
Frontend_test_layout_commons::validateLayoutTitle($I);
Frontend_test_layout_commons::validateLayoutFooter($I);
Frontend_test_layout_commons::validateOnelocaleSite($I);

<?php
$I = new TestGuy($scenario);
$I->wantTo('Validate layout by wrong url');
Frontend_test_layout_commons::validateLayoutTitle($I);

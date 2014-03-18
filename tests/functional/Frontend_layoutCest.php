<?php
use \TestGuy;

class Frontend_layoutCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
    
    public function loadFlipitHomePageWithWrongUrl(TestGuy $I)
    {
    	$I->wantTo('Validate layout by wrong url');
    	Frontend_test_layout_commons::validateLayoutTitle($I);
    }

}
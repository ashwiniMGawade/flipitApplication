<?php
use \CodeGuy;

class layoutCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
    public function validateLoadFlipitHomePage(CodeGuy $I)
    {
    	$I->wantTo('Validate load flipit function');
    	$I->execute(function () {
    		return FrontEnd_Helper_viewHelper::loadFlipitHomePage('www.flipit.com');
    	});
    	$I->expect('index.phtml');
     }
     public function validateGetAllMaxAccount(CodeGuy $I)
     {
    	$I->wantTo('Validate get all max account');
    	$I->execute(function () {
    		return Signupmaxaccount::getLocaleName();
    	});
    	$I->expect('locale');
     }
}
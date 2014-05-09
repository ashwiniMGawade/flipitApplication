<?php
class Frontend_test_layout_commons
{
    public static function validateLayoutHeader($I)
    {
        $I->see('Flipit.com');
    }

    public static function validateLayoutTitle($I)
    {
        $I->seeInTitle('Flipit.com');
    }

    public static function validateLayoutFooter($I)
    {
        $I->see('Select your country');
        $I->see('Europe');
        $I->see('Asia');
        $I->see('CHANGING THE WAY WE SAVE');
    }

    public static function validateOnelocaleSite($I)
    {
        $I->click('India');
        $I->see('Top Coupons');
    }

    public static function validateCanonical($I)
    {
        $I->see('Top Coupons');
    }
}

<?php class TestCommons 
{
    ## Admin Credentials
    public static $adminUsername = 'kim@web-flight.nl';
    public static $adminPassword = 'password';

    ## Editor Credentials
    public static $editorUsername = 'fernando@acepipe.com';
    public static $editorPassword = 'password';

    public static function logInAdmin($I)
    {
        $I->amOnPage('/admin/auth');
        $I->fillField('uname', self::$adminUsername);
        $I->fillField('pwd', self::$adminPassword);
        $I->click('Login');

    }

    public static function logInEditor($I)
    {
        $I->amOnPage('/admin/auth');
        $I->fillField('uname', self::$editorUsername);
        $I->fillField('pwd', self::$editorPassword);
        $I->click('Login');
    }

    /**
     * @depends logInAdmin
     */
}
?>
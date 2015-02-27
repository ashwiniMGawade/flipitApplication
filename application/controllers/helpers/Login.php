<?php
class Zend_Controller_Action_Helper_Login extends Zend_Controller_Action_Helper_Abstract
{
    public static function setVisitorSession($visitorDetails)
    {
        $dataAdapter = new Auth_VisitorAdapter(
            $visitorDetails["emailAddress"],
            MD5($visitorDetails["password"])
        );
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('front_login'));
        $auth->authenticate($dataAdapter);
        return true;
        
    }

    public static function setUserCookies()
    {
        $visitorId = Auth_VisitorAdapter::getIdentity()->id;
        $visitor = new \KC\Repository\Visitor();
        $visitor->updateLoginTime($visitorId);
        setcookie('registered_user', true, time() + 10 * 365 * 24 * 60 * 60, '/');
        setcookie('kc_unique_user_id', $visitorId, time() + (86400 * 3), '/');
        return true;
    }
}

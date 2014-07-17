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
        
    }

    public static function setUserCookies()
    {
        $visitorId = Auth_VisitorAdapter::getIdentity()->id;
        $visitor = new Visitor();
        $visitor->updateLoginTime($visitorId);
        setcookie('kc_unique_user_id', $visitorId, time() + 2592000, '/');
    }
}
<?php
class Zend_Controller_Action_Helper_Login extends Zend_Controller_Action_Helper_Abstract
{
    public static function setVisitorSession($visitorInformation)
    {
        $data_adapter = new Auth_VisitorAdapter(
            $visitorInformation["emailAddress"],
            MD5($visitorInformation["password"])
        );
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('front_login'));
        $auth->authenticate($data_adapter);
    }

    public static function setUserCookies()
    {
        $visitorId = Auth_VisitorAdapter::getIdentity()->id;
        $obj = new Visitor();
        $obj->updateLoginTime($visitorId);
        setcookie('kc_unique_user_id', $visitorId, time() + 2592000, '/');
    }
}

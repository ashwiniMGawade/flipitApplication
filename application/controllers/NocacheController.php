<?php
# we set a passcache cookie for Flipit and Kortingscode.nl so that admin can bypass varnish
class NocacheController extends Zend_Controller_Action
{
    /**
     * set cookie according to domain
     *
     */
    public function setAction()
    {
        self::settingCookie(time() + 3600*8);
        self::commonFunctionForSetUnsetCookie();

    }

    protected function settingCookie($cookieExpireTime)
    {
        setcookie('passCache', '1' , $cookieExpireTime , '/');
    }

    /**
     * unset cookie from domian
     *
     */
    public function unsetAction()
    {
        self::settingCookie('1');
        self::commonFunctionForSetUnsetCookie();
    }

    protected function redirectUrl()
    {
        $link = 'http://www.flipit.com/admin';
        $this->_redirect($link);
    }

    protected function setUnsetFlipitCookie($setOrUnset)
    {
        $link = 'http://www.flipit.com/NoCache/'.$setOrUnset;
        $this->_redirect($link);
    }

    protected function commonFunctionForSetUnsetCookie()
    {
        $actionName = $this->getRequest()->getActionName();
        if(HTTP_PATH == 'http://www.kortingscode.nl/') {
            self::setUnsetFlipitCookie($actionName);
        }else {
            self::redirectUrl();

        }
    }
}

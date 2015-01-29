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
        setcookie('passCache', '1', $cookieExpireTime, '/');
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
        $refererUrl = \FrontEnd_Helper_viewHelper::getRefererHostUrl();
        $link = 'http://'.$refererUrl.'/admin';
        $this->_redirect($link);
    }

    protected function setUnsetFlipitCookie($setOrUnset)
    {
        $refererUrl = \FrontEnd_Helper_viewHelper::getRefererHostUrl();
        $link = 'http://'.$refererUrl.'/NoCache/'.$setOrUnset;
        $this->_redirect($link);
    }

    protected function commonFunctionForSetUnsetCookie()
    {
        $actionName = $this->getRequest()->getActionName();
        $httpScheme = \FrontEnd_Helper_viewHelper::getServerNameScheme();
        if (HTTP_PATH == 'http://'.$httpScheme.'.kortingscode.nl/') {
            self::setUnsetFlipitCookie($actionName);
        } else {
            self::redirectUrl();

        }
    }
}

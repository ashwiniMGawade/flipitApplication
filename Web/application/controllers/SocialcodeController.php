<?php
require_once 'Zend/Controller/Action.php';
class SocialcodeController extends Zend_Controller_Action
{
    public function init()
    {
        $module = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action = strtolower($this->getRequest()->getActionName());
        if (
            file_exists(
                APPLICATION_PATH . '/modules/' . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml"
            )
        ) {
            $this->view->setScriptPath(APPLICATION_PATH . '/modules/'  . $module . '/views/scripts');
        } else {
            $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        }
    }

    public function socialCodeAction()
    {
        require_once LIBRARY_PATH. "/recaptchalib.php";
        $this->_helper->layout()->disableLayout();
        $baseViewPath = new Zend_View();
        $baseViewPath->setBasePath(APPLICATION_PATH . '/views/');
        $socialCodeForm = new Application_Form_SocialCode();
        $shopId = $this->getRequest()->getParam('id');
        if (isset($shopId)) {
            $shopInformation = \KC\Repository\Shop::getShopInformation(base64_decode(FrontEnd_Helper_viewHelper::sanitize($shopId)));
            if (!empty($shopInformation)) {
                $socialCodeForm->getElement('shops')->setValue($shopInformation[0]['name']);
            }
        }
        if ($this->getRequest()->isPost()) {
            if ($socialCodeForm->isValid($this->getRequest()->getPost())) {
                $socialCodeParameters = $socialCodeForm->getValues();
                $captchaResponse = $this->getRequest()->getParam('g-recaptcha-response');
                $reCaptcha = new ReCaptcha(FrontEnd_Helper_viewHelper::getCaptchaKey('captchaSecretKey'));
                $response = null;
                if ($captchaResponse) {
                    $response = $reCaptcha->verifyResponse(
                        $_SERVER["REMOTE_ADDR"],
                        $captchaResponse
                    );
                }
                if ($response != null && $response->success) {
                    try {
                        \KC\Repository\UserGeneratedOffer::addOffer($socialCodeParameters);
                        echo Zend_Json::encode($baseViewPath->render('socialcode/socialcodethanks.phtml'));
                        exit();
                    } catch (Exception $e) {
                        $baseViewPath->assign('errorMessage', true);
                        $baseViewPath->assign('zendForm', $socialCodeForm);
                        echo Zend_Json::encode($baseViewPath->render('socialcode/social-code.phtml'));
                        exit();
                    }
                } else {
                    $captchaErrorMessage = new Zend_Session_Namespace('captchaErrorMessage');
                    $captchaErrorMessage->captchaErrorMessage =
                        FrontEnd_Helper_viewHelper::__translate('Please prove you are not a robot');
                }
            } else {
                $socialCodeForm->highlightErrorElements();
            }
        }
        $baseViewPath->assign('zendForm', $socialCodeForm);
        echo Zend_Json::encode($baseViewPath->render('socialcode/social-code.phtml'));
        exit();
    }

    public function checkStoreAction()
    {
        $this->_helper->layout()->disableLayout();
        $shopId = \KC\Repository\Shop::checkShop(FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('shops')));
        $shopStatus = $shopId!='' ? true : false;
        echo Zend_Json::encode($shopStatus);
        die;
    }
}

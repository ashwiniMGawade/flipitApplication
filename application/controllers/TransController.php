<?php
class TransController extends Zend_Controller_Action
{
    public function init()
    {
    }

    public function getformdataAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        return $this->getHelper('Transl8')->getFormDataAction();
    }

    public function submitAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $translationModel = new Translations();

        $form = $this->getHelper('Transl8')->_createForm();
        $form->populate($this->getRequest()->getParams());

        if ($this->getRequest()->isPost()) {

            $formValues     = $form->getValues();
            $translationKey = $formValues['translationKey'];
            $translationModel->saveTranslations($formValues);

            if (Zend_Translate::hasCache()) {
                Zend_Translate::clearCache();
            }
        }
    }

    public function startinlinetranslationAction()
    {

        // no varnisch
        setcookie('passCache', '1', time() + 3600*8, '/');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $session 	= new Zend_Session_Namespace('Transl8');
        $storeUrl 	= $this->_getParam('storeUrl', 'http://www.flipit.com');
        $hash 		= $this->_getParam('hash', false);

        if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'flipit.com')) {
            if ($hash == $this->view->inlineTranslationHash()) {
                $session->onlineTranslationActivated = true;
                $this->_redirect($storeUrl);
            } else {
                echo 'Invalid hash, try again from Admin';
            }
        } else {
            $session->onlineTranslationActivated = false;
            echo "This function can only be activated from the admin";
        }
    }

    public function stopinlinetranslationAction()
    {
        setcookie('passCache', '1', '1', '/');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        $languageFile = APPLICATION_PATH.'/../public/language/'.Zend_Registry::get('Zend_Locale').'/translations.csv';
        $csvWritableTranslations = Translations::getCsvWritableTranslations();
        $csvWriter = new Application_Service_CsvWriter($languageFile);
        $csvWriter->writeFromArray($csvWritableTranslations);

        $session = new Zend_Session_Namespace('Transl8');
        $session->onlineTranslationActivated = false;

        $this->_redirect('http://www.flipit.com/admin');
    }

    public function testAction()
    {
    }
}

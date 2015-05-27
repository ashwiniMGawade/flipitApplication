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

        $translationModel = new \KC\Repository\Translations();
        $form = $this->getHelper('Transl8')->_createForm();
        $form->populate($this->getRequest()->getParams());

        if ($this->getRequest()->isPost()) {

            $formValues     = $form->getValues();
            $translationKey = $formValues['translationKey'];
            $translationModel->saveTranslations($formValues);

            if (\Zend_Translate::hasCache()) {
                \Zend_Translate::clearCache();
            }
            
            $locale = new \Zend_Locale(Zend_Registry::get('Zend_Locale'));
            $cache = new Application_Service_Translation_Cache();
            $cache->clearCache($locale);
        }
    }

    public function startinlinetranslationAction()
    {
        // no varnish
        setcookie('passCache', '1', time() + 3600*8, '/');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $session 	= new \Zend_Session_Namespace('Transl8');
        $httpScheme = \FrontEnd_Helper_viewHelper::getServerNameScheme();
        $storeUrl 	= $this->_getParam('storeUrl', 'http://'.$httpScheme.'.flipit.com');
        $hash 		= $this->_getParam('hash', false);

        if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $httpScheme.'.flipit.com')) {
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

        $localLanguageFilePath = APPLICATION_PATH
            .'/../public'
            .(LOCALE == '' ? '' : '/'.LOCALE)
            .'/language/translations.csv';

        self::writeTranslationsToCsv($localLanguageFilePath);
        self::writeCsvToS3($localLanguageFilePath);

        $session = new \Zend_Session_Namespace('Transl8');
        $session->onlineTranslationActivated = false;
        $httpScheme = \FrontEnd_Helper_viewHelper::getServerNameScheme();
        $this->_redirect('http://'.$httpScheme.'.flipit.com/admin');
    }

    protected function writeTranslationsToCsv($localLanguageFilePath)
    {
        $csvWritableTranslations = \KC\Repository\Translations::getCsvWritableTranslations();
        $csvWriter = new \Application_Service_Infrastructure_Csv_Writer($localLanguageFilePath);
        (!empty($csvWritableTranslations) ? $csvWriter->writeFromArray($csvWritableTranslations) : '');
    }

    protected function writeCsvToS3($localLanguageFilePath)
    {
        $cdnLanguageFilePath = '/public'
            .(LOCALE == '' ? '' : '/'.LOCALE)
            .'/language/translations.csv';
        $cdnWriter = new \Application_Service_Infrastructure_Cdn_Writer();
        $cdnWriter->putFile($localLanguageFilePath, $cdnLanguageFilePath);
    }
}

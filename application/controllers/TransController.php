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
        // no varnish
        setcookie('passCache', '1', time() + 3600*8, '/');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $session 	= new Zend_Session_Namespace('Transl8');
        $storeUrl 	= $this->_getParam('storeUrl', 'http://www.flipit.com');
        $hash 		= $this->_getParam('hash', false);

        $sessionForModuleName = new Zend_Session_Namespace('moduleName');
        $moduleName = explode('/', $storeUrl);
        $sessionForModuleName->moduleName = isset($moduleName[3]) ? $moduleName[3] : '';

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

        $sessionForModuleName = new Zend_Session_Namespace('moduleName');

        $localLanguageFilePath = APPLICATION_PATH
            .'/../public/'
            .($sessionForModuleName->moduleName == '' ? '' : $sessionForModuleName->moduleName.'/')
            .'language/translations.csv';

        self::writeTranslationsToCsv($localLanguageFilePath);
        self::writeCsvToS3($localLanguageFilePath, $sessionForModuleName);

        $session = new Zend_Session_Namespace('Transl8');
        $session->onlineTranslationActivated = false;

        $sessionForModuleName->moduleName = '';

        $this->_redirect('http://www.flipit.com/admin');
    }

    protected function writeTranslationsToCsv($localLanguageFilePath)
    {
        $csvWritableTranslations = Translations::getCsvWritableTranslations();
        $csvWriter = new Application_Service_Infrastructure_CsvWriter($localLanguageFilePath);
        $csvWriter->writeFromArray($csvWritableTranslations);
    }

    protected function writeCsvToS3($localLanguageFilePath, $sessionForModuleName)
    {
        $cdnLanguageFilePath = '/public/'
            .($sessionForModuleName->moduleName == '' ? '' : $sessionForModuleName->moduleName.'/')
            .'language/translations.csv';

        $cdn = new Application_Service_Infrastructure_CdnWriter();
        $cdn->putFile($localLanguageFilePath, $cdnLanguageFilePath);
    }
}

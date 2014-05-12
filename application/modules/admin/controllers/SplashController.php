<?php
class Admin_SplashController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $databaseConnection = BackEnd_Helper_viewHelper::addConnection();

        if (! Auth_StaffAdapter::hasIdentity ()) {
            $pageReferer = new Zend_Session_Namespace('referer');
            $pageReferer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect ( '/admin/auth/index' );
        }

        BackEnd_Helper_viewHelper::closeConnection($databaseConnection);
        $this->view->controllerName = $this->getRequest()->getParam ('controller');
        $this->view->action = $this->getRequest()->getParam('action');
        $sessionNamespace = new Zend_Session_Namespace();

        if($sessionNamespace->settings['rights']['administration']['rights'] != '1' && $sessionNamespace->settings['rights']['administration']['rights'] !='2' ) {
            $flashMessenger = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate ('You have no permission to access page');
            $flashMessenger->addMessage(array('error' => $message));
            $this->_redirect('/admin');
        }
    }

    public function indexAction()
    {
        $splashObject = new Splash();
        $splashTableData = $splashObject->getSplashTableData();
     
        if (!empty($splashTableData)) {
            $splashOfferDetails = Doctrine_Core::getTable('Offer')->findOneBy('id', $splashTableData[0]['offerId']);
            $this->view->currentOfferTitle = $splashOfferDetails['title'];
            $this->view->currentOfferLocale = $splashTableData[0]['locale'];
        }

        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $message = $flashMessenger->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';

    }

    public function addOfferAction()
    {
        $urlRequest = $this->getRequest();
        $this->view->websites = Website::getAllWebsites();
        $flashMessenger = $this->_helper->getHelper('FlashMessenger' );
        $message = $flashMessenger->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';

        if ($this->_request->isPost()) {
            $localeId = $urlRequest->getParam('locale' , false);
            $websiteDetails = Website::getWebsiteDetails($localeId);
            $localeName = explode('/', $websiteDetails['name']);
            $locale = isset($localeName[1]) ?  $localeName[1] : "en" ;
            $offerId = $urlRequest->getParam('searchOfferId' , false);
            $splash = new Splash();
            $splashTableData = $splash::getSplashTableData();

            if (!empty($splashTableData)) {
                Doctrine_Query::create()->delete()->from('Splash')->execute();
            }

            $splash->id = 1;
            $splash->offerId = $offerId;
            $splash->locale = $locale;         
            $splash->save();
            $message = $this->view->translate('Offer has been added successfully');
            $flash->addMessage(array('success' => $message));
            $this->_redirect ( HTTP_PATH . 'admin/splash');
        }
            
        
    }

    public function offersListAction()
    {  
        if ($this->_request->isXmlHttpRequest()) {
            $localeId =  intval($this->getRequest()->getParam('locale', false ));
            
            if ($localeId) {
                $websiteDetails = Website::getWebsiteDetails($localeId);
                $localeData = explode('/', $websiteDetails['name']);
                $locale = isset($localeData[1]) ?  $localeData[1] : "en" ;
                $connectionObject = BackEnd_Helper_DatabaseManager::addConnection($locale);
                $offers = new Offer($connectionObject['connName']);
                $offerKeyword = $this->getRequest()->getParam('keyword');
                $activeOffers = $offers->getActiveOfferDetails($offerKeyword);
                BackEnd_Helper_DatabaseManager::closeConnection($connectionObject['adapter']);
                
                if (sizeof($activeOffers) > 0) {
                    foreach ($activeOffers as $offer) {
                        $offers[] = array('name' => ucfirst($offer['title']),
                                         'id' => $offer['id']);
                    }
                } else {
                    $message = $this->view->translate('No Record Found');
                    $offers[] = array('name' => $message);
                }
                $this->_helper->json($offers);
            }
        }
        $this->_redirect ( '/admin' );
    }

    public function deleteOfferAction()
    {
        $this->_helper->layout->disableLayout();
        $flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $message = $flashMessenger->getMessages();
        $this->view->messageSuccess = isset ($message [0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset ($message [0]['error']) ? $message[0]['error'] : '';
        $splashObject = new Splash();
        $splashObject->deleteSplashoffer();
        $message = $this->view->translate('Offer has been deleted successfully');
        $flashMessenger->addMessage(array('success' => $message));
        $this->_redirect ( HTTP_PATH . 'admin/splash');        
    }

}

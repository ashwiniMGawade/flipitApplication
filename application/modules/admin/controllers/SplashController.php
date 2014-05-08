<?php
/**
 * This controller handle all the activisties regarding splash  management of offers
 *
 * @author spsingh1
 *
 */
class Admin_SplashController extends Zend_Controller_Action
{
    /**
     * check authentication before load the page
     */
    public function preDispatch()
    {
        $conn2 = BackEnd_Helper_viewHelper::addConnection ();

        $params = $this->_getAllParams ();
        if (! Auth_StaffAdapter::hasIdentity ()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect ( '/admin/auth/index' );
        }

        BackEnd_Helper_viewHelper::closeConnection ( $conn2 );

        $this->view->controllerName = $this->getRequest ()->getParam ( 'controller' );
        $this->view->action = $this->getRequest ()->getParam ( 'action' );

        $sessionNamespace = new Zend_Session_Namespace();

        if($sessionNamespace->settings['rights']['administration']['rights'] != '1' && $sessionNamespace->settings['rights']['administration']['rights'] !='2' ) {

            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate ( 'You have no permission to access page' );
            $flash->addMessage ( array ('error' => $message ));
            $this->_redirect ( '/admin' );
        }
    }

    public function indexAction()
    {
        $splash = new Splash();
        $currentOfferId = $splash->getOfferId();
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages ();
        $this->view->messageSuccess = isset($message [0] ['success']) ? $message [0] ['success'] : '';
        $this->view->messageError = isset($message [0] ['error']) ? $message [0] ['error'] : '';
        $this->view->currentOffer = $currentOffer;
    }


    public function addOfferAction()
    {

        $request = $this->getRequest();
        $this->view->websites = Website::getAllwebSites();
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';

        if ($this->_request->isPost()) {
            $localeId = $request->getParam('locale' , false);
            $websiteDetails = Website::getWebsiteDetails($localeId);
            $localeData = explode('/', $websiteDetails['name']);
            $locale = isset($localeData[1]) ?  $localeData[1] : "en" ;
            $connectionObject = BackEnd_Helper_DatabaseManager::addConnection($locale);
            $signupMaxObject = new Signupmaxaccount($locale);
            $languageLocale =  $signupMaxObject::getAllMaxAccounts();
            $languageLocale = !empty($languageLocale[0]['locale']) ? $languageLocale[0]['locale'] : 'nl_NL';
            Zend_Registry::set('db_locale', $locale ) ;
            $splash = new Splash();
            $returnedValue = $splash->saveOffer($request,$languageLocale);

            if($returnedValue) {
                $message = $this->view->translate('Offer has been added successfully');
                $flash->addMessage(array('success' => $message));
            } else {
                $message = $this->view->translate('This offer has already been added for this particulat locale');
                $flash->addMessage(array('error' => $message));
            }

            $connectionObject = BackEnd_Helper_DatabaseManager::closeConnection($connectionObject['adapter']);
            Zend_Registry::set('db_locale', false ) ;
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
                $keyword = $this->getRequest()->getParam('keyword');
                $allOffers = $offers->getActiveOfferNames($keyword);
                $connectionObject = BackEnd_Helper_DatabaseManager::closeConnection($connectionObject['adapter']);
                $offers = array();

                if (sizeof($allOffers) > 0) {
                    foreach ($allOffers as $offer) {
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
}

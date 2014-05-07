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
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );
        $message = $flash->getMessages ();
        $this->view->messageSuccess = isset ( $message [0] ['success'] ) ? $message [0] ['success'] : '';
        $this->view->messageError = isset ( $message [0] ['error'] ) ? $message [0] ['error'] : '';
    }


    public function addOfferAction()
    {

        $request = $this->getRequest();
        $chianId = $request->getParam('chain' , false);
     

            $this->view->websites = Website::getAllwebSites();

            $this->view->chainId = $chianId ;

            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $flash->getMessages();
            $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
            $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';


            if ($this->_request->isPost()) {

                $localeId = $request->getParam('locale' , false);

                # get selected locale detail
                $website = Website::getWebsiteDetail($localeId);

                $localeData = explode('/', $website['name']);
                $locale = isset($localeData[1]) ?  $localeData[1] : "en" ;

                # connect to select locale database
                $connObj = BackEnd_Helper_DatabaseManager::addConnection($locale);

                $signMaxObj = new Signupmaxaccount($locale);
                $langLocale =  $signMaxObj::getAllMaxAccounts();
                $langLocale = !empty($langLocale[0]['locale']) ? $langLocale[0]['locale'] : 'nl_NL';


                Zend_Registry::set('db_locale', $locale ) ;

                # save new chain
                $chain = new ChainItem();
                $ret = $chain->saveChain($request,$langLocale);

                # if chain is saved then refresh shop page in varnish
                if($ret) {
                    $message = $this->view->translate ( 'Shop has been added successfully' );
                    $flash->addMessage ( array ('success' => $message ));
                } else {

                    $message = $this->view->translate ( 'This shop has been already added for this particulat locale' );
                    $flash->addMessage ( array ('error' => $message ));
                }


                # close connection
                $connObj = BackEnd_Helper_DatabaseManager::closeConnection($connObj['adapter']);

                Zend_Registry::set('db_locale', false ) ;

                $this->_redirect ( HTTP_PATH . 'admin/chain/chain-item/chain/'. $chianId  );
            }
       

    }

    

}

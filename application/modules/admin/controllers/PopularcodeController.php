<?php

class Admin_PopularcodeController extends Zend_Controller_Action
{
    /**
     * check authentication before load the page
     * @see Zend_Controller_Action::preDispatch()
     * @author kraj
     * @version 1.0
     */
    public function preDispatch()
    {
        //$conn2 = \BackEnd_Helper_viewHelper::addConnection(); // connection
        $params = $this->_getAllParams();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new \Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
       // \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()
                ->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');

    }

    /**
     * Affiliate Helper file for switch the connection
     * @see Zend_Controller_Action::init()
     * @author Er.kundal
     * @version 1.0
     */
    public function init()
    {
        //$qa=  new Doctrine_RawSql();

        /* Initialize action controller here */
    }

    /**
     * get Popular code from database
     * @author kraj
     * @version 1.0
     */
    public function indexAction()
    {
        //get Popular code from database
        $data = \KC\Repository\PopularCode::getPopularCode();
        $neAr = array();
        foreach ($data as $pOffer) {

            $neAr[] = $pOffer['offerId'];
        }
        $allOffer = \KC\Repository\PopularCode::searchAllOffer($neAr);
        $this->view->data = $data;
        $this->view->offer = $allOffer;
        //echo "<pre>";
        //print_r($allOffer);
    }
    /**
     * Search to 10 best offer from database
     * @author kraj
     * @version 1.0
     */
    public function searchtoptenofferAction()
    {
        $srh = addslashes($this->getRequest()->getParam('keyword'));
        $flag = 0;
        //call to seach top 10 offer function in model class


        if($this->getRequest()->getParam('selectedCodes') != '' && $this->getRequest()->getParam('selectedCodes') != 'undefined') {
            $alreadySelected = explode(',', $this->getRequest()->getParam('selectedCodes'));
        }
        $data = \KC\Repository\PopularCode::searchTopTenOffer($srh, $flag);

        $ar = array();
        $i = 0;
        if (sizeof($data) > 0) {
            foreach ($data as $d) {

                if( ! @in_array($d['id'], $alreadySelected) ){
                    $ar[$i]['label'] = ucfirst($d['title']);
                    $ar[$i]['value'] = ucfirst($d['title']);
                    $ar[$i]['id'] = $d['id'];
                    $i++ ;
                }
            }

        }

        if(sizeof($ar) == 0){

            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }

        echo \Zend_Json::encode($ar);
        die();

    }
    /**
     * add manual a offer in popular code
     * @author kraj
     * @version 1.0
     */
    public function addofferAction()
    {
        $data = $this->getRequest()->getParam('id');
        //call to add offer function from model
        $flag = \KC\Repository\PopularCode::addOfferInList($data);

        #if popular code is addedd then update varnsih as well
        if($flag && $flag != "0" && $flag != "1" && $flag != '2') {
            self::updateVarnish();
        }

        echo \Zend_Json::encode($flag);
        die();
    }
    /**
     * delete popular code
     * @author kraj
     * @version 1.0
     */
    public function deletepopularcodeAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated = \KC\Repository\PopularCode::deletePapularCode($id, $position);

        #if popular code is addedd then update varnsih as well
        if($isUpdated) {
            self::updateVarnish();
        }

        //get popular code from database
        $data = \KC\Repository\PopularCode::getPopularCode();
        echo \Zend_Json::encode($data);
        die();
    }
    /**
     * move up one position  popular code list
     * @author kraj
     * @version 1.0
     */
    public function moveupAction()
    {
        $currentCodeId = $this->getRequest()->getParam('id');
        $currentPosition = $this->getRequest()->getParam('pos');
        $previousCodeId = $this->getRequest()->getParam('previousCodeId');
        $previousCodePosition = $this->getRequest()->getParam('previousCodePosition');
        $isUpdated = \KC\Repository\PopularCode::moveUp($currentCodeId, $currentPosition, $previousCodeId, $previousCodePosition);
        
        if ($isUpdated) {
            self::updateVarnish();
        }
        $data = \KC\Repository\PopularCode::getPopularCode();
        echo \Zend_Json::encode($data);
        die();
    }
    /**
     * move down one position  popular code list
     * @author kraj
     * @version 1.0
     */
    public function movedownAction()
    {
        $currentCodeId = $this->getRequest()->getParam('id');
        $currentPosition = $this->getRequest()->getParam('pos');
        $nextCodeId = $this->getRequest()->getParam('nextCodeId');
        $isUpdated  = \KC\Repository\PopularCode::moveDown($currentCodeId, $currentPosition, $nextCodeId);

        if ($isUpdated) {
            self::updateVarnish();
        }

        $data = \KC\Repository\PopularCode::getPopularCode();
        echo \Zend_Json::encode($data);
        die();
    }

    /**
     * lock position  popular code list
     * @author Raman
     * @version 1.0
     */
    public function lockAction()
    {
        $id = $this->getRequest()->getParam('id');
        //call model class function pass position and id
        $isUpdated = \KC\Repository\PopularCode::lockElement($id);


        #if an item is locked then update varnsih as well
        if($isUpdated) {
            self::updateVarnish();
        }


        //get popular code from database
        $data = \KC\Repository\PopularCode::getPopularCode();
        echo \Zend_Json::encode($data);
        die();
    }

    /**
     *  updateVarnish
     *
     *  update varnish table whenever popular code lsit updated
     */
    public function updateVarnish()
    {
        // Add urls to refresh in Varnish
        $varnishObj = new KC\Repository\Varnish();
        $varnishObj->addUrl( rtrim( HTTP_PATH_FRONTEND , '/'  ));


        # make markplaatfeed url's get refreashed only in case of kortingscode
        iF(LOCALE == '')
        {
            $varnishObj->addUrl(  HTTP_PATH_FRONTEND  . 'marktplaatsfeed');
            $varnishObj->addUrl(  HTTP_PATH_FRONTEND . 'marktplaatsmobilefeed' );

        }

    }

    public function savepopularofferspositionAction()
    {
        \KC\Repository\PopularCode::savePopularOffersPosition($this->getRequest()->getParam('offerid'));
        $popularCode = \KC\Repository\PopularCode::getPopularCode();
        echo \Zend_Json::encode($popularCode);
        exit();
    }

    public function addeditorwidgetdataAction()
    {
        $site_name = "kortingscode.nl";
        if (isset($_COOKIE['site_name'])) {
            $site_name =  $_COOKIE['site_name'];
        }
        $users = new \KC\Repository\User();
        $this->view->MangersList = $users->getManagersLists($site_name);
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $message = $flashMessage->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';

        if ($this->_request->isPost()) {
            $parameters = $this->_getAllParams();
            $editorId = $parameters['selecteditors'];
            $type = $parameters['type'];
            $description = $parameters['description'];
            $subTitle = $parameters['subtitle'];
            $status = $parameters['actionType'];
            \KC\Repository\EditorWidget::addEditorWigetData($editorId, $description, $subTitle, $type, $status);
            $message = $this->view->translate('backend_ Editor data has been added successfully.');
            $flashMessage->addMessage(array('success' => $message ));
        }
    }

    public function pagetypedetailAction()
    {
        $editorWidgetData = \KC\Repository\EditorWidget::getEditorWigetData($this->getRequest()->getParam('pageType'));
        $editorWidgetData = !empty($editorWidgetData) ? $editorWidgetData : '';
        echo \Zend_Json::encode($editorWidgetData);
        exit();
    }
}

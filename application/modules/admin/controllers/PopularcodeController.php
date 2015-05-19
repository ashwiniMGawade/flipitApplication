<?php

class Admin_PopularcodeController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $params = $this->_getAllParams();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new \Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        $this->view->controllerName = $this->getRequest()
                ->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
    }

    public function init()
    {
    }

    public function indexAction()
    {
        $data = \KC\Repository\PopularCode::getPopularCode();
        $neAr = array();
        foreach ($data as $pOffer) {
            $neAr[] = $pOffer['offerId'];
        }
        $allOffer = \KC\Repository\PopularCode::searchAllOffer($neAr);
        $this->view->data = $data;
        $this->view->offer = $allOffer;
    }

    public function searchtoptenofferAction()
    {
        $srh = addslashes($this->getRequest()->getParam('keyword'));
        $flag = 0;
        if ($this->getRequest()->getParam('selectedCodes') != '' && $this->getRequest()->getParam('selectedCodes') != 'undefined') {
            $alreadySelected = explode(',', $this->getRequest()->getParam('selectedCodes'));
        }
        $data = \KC\Repository\PopularCode::searchTopTenOffer($srh, $flag);
        $ar = array();
        $i = 0;
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                if (!@in_array($d['id'], $alreadySelected)) {
                    $ar[$i]['label'] = ucfirst($d['title']);
                    $ar[$i]['value'] = ucfirst($d['title']);
                    $ar[$i]['id'] = $d['id'];
                    $i++ ;
                }
            }

        }

        if (sizeof($ar) == 0) {
            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }
        echo \Zend_Json::encode($ar);
        die();
    }
  
    public function addofferAction()
    {
        $data = $this->getRequest()->getParam('id');
        $flag = \KC\Repository\PopularCode::addOfferInList($data);
        if ($flag && $flag != "0" && $flag != "1" && $flag != '2') {
            self::updateVarnish();
        }
        echo \Zend_Json::encode($flag);
        die();
    }
 
    public function deletepopularcodeAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        $isUpdated = \KC\Repository\PopularCode::deletePapularCode($id, $position);
        if ($isUpdated) {
            self::updateVarnish();
        }
        $data = \KC\Repository\PopularCode::getPopularCode();
        echo \Zend_Json::encode($data);
        die();
    }
   
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
    public function lockAction()
    {
        $id = $this->getRequest()->getParam('id');
        $isUpdated = \KC\Repository\PopularCode::lockElement($id);
        if ($isUpdated) {
            self::updateVarnish();
        }
        $data = \KC\Repository\PopularCode::getPopularCode();
        echo \Zend_Json::encode($data);
        die();
    }

    public function updateVarnish()
    {
        // Add urls to refresh in Varnish
        $varnishObj = new KC\Repository\Varnish();
        $varnishObj->addUrl(rtrim( HTTP_PATH_FRONTEND , '/'));
        if (LOCALE == '') {
            $varnishObj->addUrl(HTTP_PATH_FRONTEND  . 'marktplaatsfeed');
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'marktplaatsmobilefeed');
        }
    }

    public function savepopularofferspositionAction()
    {
        \KC\Repository\PopularCode::savePopularOffersPosition($this->getRequest()->getParam('offerid'));
        $popularCode = \KC\Repository\PopularCode::getPopularCode();
        echo \Zend_Json::encode($popularCode);
        exit();
    }

    public function addEditorWidgetDataAction()
    {
        $site_name = "kortingscode.nl";
        if (isset($_COOKIE['site_name'])) {
            $site_name =  $_COOKIE['site_name'];
        }
        $users = new \KC\Repository\User();
        $this->view->managersList = $users->getManagersLists($site_name);
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        $message = $flashMessage->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';

        if ($this->_request->isPost()) {
            $parameters = $this->_getAllParams();
            \KC\Repository\EditorWidget::addEditorWidgetData($parameters);
            $message = $this->view->translate('backend_ Editor data has been updated successfully');
            $flashMessage->addMessage(array('success' => $message ));
            $this->_redirect(HTTP_PATH.'admin/popularcode/add-editor-widget-data');
        }
    }

    public function pageTypeDetailAction()
    {
        $editorWidgetData = \KC\Repository\EditorWidget::getEditorWidgetData($this->getRequest()->getParam('pageType'));
        $editorWidgetData = !empty($editorWidgetData) ? $editorWidgetData : '';
        echo \Zend_Json::encode($editorWidgetData);
        exit();
    }
}

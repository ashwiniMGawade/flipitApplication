<?php

class Admin_VisitorController extends Zend_Controller_Action
{

    /**
     * For switch the connection
     * @author mkaur
     * @version 1.0
     */
    public function preDispatch()
    {
        $conn2 = \BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        $params = $this->_getAllParams();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');


        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new Zend_Session_Namespace();
        $this->_settings  = $sessionNamespace->settings['rights'] ;


        # apply admin level access on all controllers
        if ($this->getRequest()->isXmlHttpRequest()) {

            # add action as new case which needs to be viewed by other users
            switch(strtolower($this->view->action)) {
                case 'searchemails':
                    # no restriction
                    break;
                default:
                    if (
                        $this->_settings['administration']['rights'] != '1'
                        && $this->_settings['administration']['rights'] != '2'
                    ) {

                        $this->getResponse()->setHttpResponseCode(404);
                        $this->_helper->redirector('index', 'index', null) ;
                    }
            }

        } else {
            if (
                $this->_settings['administration']['rights'] != '1'
                && $this->_settings['administration']['rights'] != '2'
            ) {
                $this->_redirect('/admin/auth/index');
            }
        }
    }
    
    public function init()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';
    }


    ############################# Refactored Block ###########
    public function permanentdeleteAction()
    {
        $visitorId = $this->getRequest()->getParam('id');
        if ($visitorId) {
            $entityManagerLocale = \Zend_Registry::get('emLocale');
            $visitorInformation = $entityManagerLocale->find('\KC\Entity\Visitor', $visitorId);
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->delete('KC\Entity\Visitor', 'v')
                ->where("v.id=" .$visitorId)
                ->getQuery()->execute();

            if (!empty($visitorInformation->imageId) && (intval($visitorInformation->imageId)) > 0) {
                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder->delete('KC\Entity\VisitorImage', 'i')
                ->where("i.id=" .$visitorInformation->imageId)
                ->getQuery()->execute();
            }
        } else {
            $visitorId = null;
        }
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate('Visitor has been deleted Permanently.');
        $flash->addMessage(array('success' => $message ));
        echo Zend_Json::encode($visitorId);
        die();
    }
    ############################# Refactored Block ###########

    public function indexAction()
    {

    }
    
    public function getfavoriteshopAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender();
        $data =  \KC\Repository\Visitor::getFavorite($this->getRequest()->getParam('id'));
        echo Zend_Json::encode($data);
        die();
    }

    public function getvisitorlistAction()
    {
        $params = $this->_getAllParams();
        $visitorList = \KC\Repository\Visitor::VisitorList($params);
        echo Zend_Json::encode($visitorList);
        die();
    }

    public function editvisitorAction()
    {
        $id = intval($this->getRequest()->getParam('id'));
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        if (intval($id) > 0) {
            $data = \KC\Repository\Visitor::editVisitor($id);
            $this->view->data = $data ;
            $this->view->id = $id;
            $this->view->userDetail = $data;
        /*    echo "<pre>";
            print_r($this->view->userDetail);
            die;*/
            $this->view->favShopId='';
            foreach ($data['favoritevisitorshops'] as $key => $value) {
                $this->view->favShopId.= $value['id'].',';
            }
            $this->view->favShopId = rtrim($this->view->favShopId, ',');
        }

        /* Date of birth dropdown*/
        $dataMonth='';
        $dataDay='';
        $dataYear='';

        if (@$data['dateOfBirth']!='') {
            list($dataYear, $dataMonth, $dataDay) = @split('-', $data['dateOfBirth']->format('Y-m-d'));
        }
        //echo $dob;die;
        $year_limit = 0;
        $html_output="";

        /*days*/
        $html_output .= '<select name="date_day" class="dateofbirth_visitor" id="day_select">'."\n";

        for ($day = 1; $day <= 31; $day++) {
            $select = ($day==$dataDay) ? "selected=selected" : "";
            $html_output .= '<option '.$select.' value="'.$day.'">' . $day . '</option>'."\n";
        }
        $html_output .= '</select>'."\n";

        /*months*/
        $html_output .= '<select name="date_month" class="dateofbirth_visitor" id="month_select" >'."\n";
        $months = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

        for ($month = 1; $month <= 12; $month++) {
            $select = ($month==$dataMonth) ? "selected=selected" : "";
            $html_output .= '<option value="'.$month .'" '.$select.'>' . $months[$month] . '</option>'."\n";
        }
        $html_output .= '</select>'."\n";

        /*years*/
        $html_output .= '<select name="date_year" class="dateofbirth_visitor" id="year_select">'."\n";
        for ($year = 1900; $year <= (date("Y") - $year_limit); $year++) {
            $select = ($year==$dataYear) ? "selected=selected" : "";
            $html_output .= '<option '.$select.' value="'.$year.'">' . $year . '</option>'."\n";
        }
        $html_output .= '</select>'."\n";
        $this->view->dateofbirth = $html_output;


        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            //var_dump($params);
            if ($params) {
                $visitor =  \KC\Repository\Visitor::updateVisitor($params, true);
            }
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Visitor details has been updated successfully.');
            $flash->addMessage(array('success' => $message ));
            $this->_redirect(HTTP_PATH.'admin/visitor#'.$params['qString']);
        }
    }

    /**
     * Search top five visitors from database based on search text
     * @author mkaur
     */
    public function searchkeyAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $for = $this->getRequest()->getParam('flag');
        $data = \KC\Repository\Visitor::searchKeyword($for, $srh);
        $ar = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                $ar[] = ucfirst($d['firstName']);
            }
        } else {
            $ar[]="No Record Found.";
        }
        echo Zend_Json::encode($ar);
        die;
    }
    /**
     * Search top five visitors from database based on search text
     * @author kraj
     */
    public function searchemailsAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $for = $this->getRequest()->getParam('flag');
        $data = \KC\Repository\Visitor::searchEmails($for, $srh);
        $ar = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                $ar[] = ucfirst($d['email']);
            }
        } else {
            $ar[]="No Record Found.";
        }
        echo Zend_Json::encode($ar);
        die;
    }
    public function deletefavoriteshopAction()
    {
        $params = $this->_getAllParams();
        $success = \KC\Repository\Visitor::delelteFav($params);
        echo Zend_Json::encode($success);
        die();
    }

    public function importvisitorlistAction()
    {
        $params = $this->_getAllParams();
        if ($this->getRequest()->isPost()) {
            if (isset($_FILES['excelFile']['name']) && @$_FILES['excelFile']['name'] != '') {
                $routeRedirectObj = new \KC\Repository\RouteRedirect();
                $result = $routeRedirectObj->uploadExcel($_FILES['excelFile']['name'], true);
                $excelFilePath = $result['path'];
                $excelFile = $excelFilePath.$result['fileName'];
                if ($result['status'] == 200) {

                    chmod($excelFile, 0775);
                    $flash = $this->_helper->getHelper('FlashMessenger');
                    $message = $this->view->translate('Visitors uploaded successfully');
                    $flash->addMessage(array('success' => $message));
                    $this->_redirect(HTTP_PATH . 'admin/visitor/importvisitorlist');
                }

            } else {

                $flash = $this->_helper->getHelper('FlashMessenger');
                $message = $this->view->translate('Problem in your file!!');
                $flash->addMessage(array('error' => $message));
                $this->_redirect(HTTP_PATH . 'admin/visitor/importvisitorlist');
            }
        }

        $importFolder = UPLOAD_EXCEL_PATH.'import/';
        $xlsxFilesToProcess = array();
        foreach (glob($importFolder."*.xlsx") as $xlsxToProcess) {
            $xlsxFilesToProcess[] = $xlsxToProcess;
        }
        $this->view->xlsxFilesToProcess = $xlsxFilesToProcess;
        $filename = $importFolder.'log.txt';
        $this->view->importLog = (file_exists($filename)) ? file_get_contents($filename) : false;
    }

    public function emptyXlxAction()
    {
        # set fiel and its trnslattions
        $file =  APPLICATION_PATH . '/migration/empty_visitor.xlsx' ;
        $fileName =  $this->view->translate($file);

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        # set reponse headers and body
        $this->getResponse()
        ->setHeader('Content-Disposition', 'attachment;filename=' . basename($fileName))
        ->setHeader('Content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->setHeader('Cache-Control', 'max-age=0')
                ->setBody(file_get_contents($fileName));
    }

    public function exportXlxAction()
    {
        # set fiel and its trnslattions
        $locale = LOCALE == '' ? "-NL" : "-".strtoupper(LOCALE);
        $visitorFile = UPLOAD_EXCEL_PATH . "visitorList".$locale.".csv";
        $fileName =  $this->view->translate($visitorFile);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        # set reponse headers and body
        $this->getResponse()
        ->setHeader('Content-Disposition', 'attachment;filename=' . basename($fileName))
        ->setHeader('Content-type', 'text/csv')
        ->setHeader('Cache-Control', 'max-age=0')
        ->setHeader('Pragma', 'no-cache')
        ->setHeader('Expires', '0')
        ->setBody(file_get_contents($fileName));
    }
}
<?php
class Admin_RedirectController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $conn2 = \BackEnd_Helper_viewHelper::addConnection();
        $params = $this->getAllParams();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');

        $sessionNamespace = new \Zend_Session_Namespace();
        if ($sessionNamespace->settings['rights']['administration']['rights'] != 1
            && $sessionNamespace->settings['rights']['administration']['rights'] != 2) {
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate ( 'You have no permission to access page' );
            $flash->addMessage ( array ('error' => $message ));
            $this->redirect ( '/admin' );
        }
    }

    public function init()
    {
    }

    public function indexAction()
    {
    }

    public function addredirectAction()
    {
        $params = $this->getAllParams();
        if ($this->getRequest()->isPost()) {
            $keyword = \KC\Repository\RouteRedirect::addRedirect($params);
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Redirect has been created successfully');
            $flash->addMessage(array('success' => $message));
            $this->redirect(HTTP_PATH.'admin/redirect');
        }
    }

    public function redirectlistAction()
    {
         $params = $this->getAllParams();
         $keywordList =  KC\Repository\RouteRedirect::getRedirect($params);
         echo Zend_Json::encode($keywordList);
         die ;
    }

    public function searchAction()
    {
        $keyword = FrontEnd_Helper_viewHelper::sanitize($this->getRequest()->getParam('keyword'));
        $redirects = \KC\Repository\RouteRedirect::searchRedirects($keyword);
        $response = array();
        if (!empty($redirects)) {
            foreach ($redirects as $redirect) {
                $response[] = array('id' => $redirect['id'], 'label' => $redirect['orignalurl']);
            }
        } else {
            $response[] = array('id' => '', 'label' => $this->view->translate('No Record Found'));
        }
        echo Zend_Json::encode($response);
        die;
    }

    public function editredirectAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        if ($id>0) {
            $searchbar = \KC\Repository\RouteRedirect::getRedirectForEdit($id);
            $this->view->editRedirect = $searchbar;
        }
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            // cal to update keyword function
            $searchbar = \KC\Repository\RouteRedirect::updateRedirect($params);
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Redirect has been updated successfully');
            $flash->addMessage(array('success' => $message));
            $this->redirect(HTTP_PATH.'admin/redirect#'.$params['qString']);
        }
    }

    public function deleteredirectAction()
    {
        $id = $this->getRequest()->getParam('id');
        \KC\Repository\RouteRedirect::deleteRedirect($id);
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );
        $message = $this->view->translate ('Redirect has been deleted successfully' );
        $flash->addMessage ( array ('success' => $message ) );
        die();
    }

    public function exportredirectlistAction()
    {
        ini_set('max_execution_time', 115200);
        $data = \KC\Repository\RouteRedirect::exportRedirectList();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $column = 1;
        $row = 1;
        foreach ($data as $redirectTo) {
            $createdDate =  $redirectTo['created_at']->format('Y-m-d');
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$column, $redirectTo['orignalurl']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $redirectTo['redirectto']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$column, $createdDate);
            $column++;
            $row++;
        }
        $headerStyle = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb'=>'00B4F2'),
            ),
            'font' => array(
                'bold' => true,
            )
        );
         $borderStyle = array('borders' =>
            array('outline' =>
                array(
                    'style' => PHPExcel_Style_Border::BORDER_THICK,
                    'color' => array(
                    'argb' => '000000'
                    )
                )
            )
        );
        $borderColumn =  (intval($column) -1 );
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'C'.$column)->applyFromArray($borderStyle);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Redirect.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        die();
    }

    public function importredirectAction()
    {
        ini_set('max_execution_time', 115200);
        if ($this->getRequest()->isPost()) {
            if (isset($_FILES['excelFile']['name']) && $_FILES['excelFile']['name'] != '') {
                $RouteRedirectObj = new KC\Repository\RouteRedirect();
                $result = $RouteRedirectObj->uploadExcel($_FILES['excelFile']['name']);
                if ($result['status'] == 200) {
                    $spl = explode('/', HTTP_PATH);
                    $path = $spl[0].'//' . $spl[2];
                    $excelFilePath = $result['path'];
                    $excelFile = $excelFilePath.$result['fileName'];
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                    $objPHPExcel = $objReader->load($excelFile);
                    $worksheet = $objPHPExcel->getActiveSheet();
                    foreach ($worksheet->getRowIterator() as $row) {
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);
                        foreach ($cellIterator as $cell) {
                            $data[$cell->getRow()][$cell->getColumn()] = $cell->getValue();
                        }
                        $orignalURL =  $data[$cell->getRow()]['A'];
                        $redirectUrl =  $data[$cell->getRow()]['B'];
                        if (!empty($orignalURL)) {
                            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                            $query = $queryBuilder->select('r')
                                ->from('\Core\Domain\Entity\RouteRedirect', 'r')
                                ->where('r.orignalurl ='.$queryBuilder->expr()->literal($orignalURL));
                            $redirect = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                            if (!empty($redirect)) {
                                $redirect = \Zend_Registry::get('emLocale')->find('\Core\Domain\Entity\RouteRedirect', $redirect[0]['id']);
                            } else {
                                $redirect  = new \Core\Domain\Entity\RouteRedirect();
                            }
                            if ($orignalURL != "") {
                                $redirect->orignalurl = $orignalURL;
                            }
                            if ($redirectUrl != "") {
                                $redirect->redirectto = $redirectUrl;
                            }
                            $redirect->deleted = 0;
                            $redirect->created_at = new \DateTime('now');
                            $redirect->updated_at = new \DateTime('now');
                            \Zend_Registry::get('emLocale')->persist($redirect);
                            \Zend_Registry::get('emLocale')->flush();
                        } else {
                            $flash = $this->_helper->getHelper('FlashMessenger');
                            $message = $this->view->translate('Redirect has been updated successfully');
                            $flash->addMessage(array('success' => $message));
                            $this->_redirect(HTTP_PATH . 'admin/redirect');
                        }
                    }
                    $flash = $this->_helper->getHelper('FlashMessenger');
                    $message = $this->view->translate('Redirect has been updated successfully');
                    $flash->addMessage(array('success' => $message));
                    $this->redirect(HTTP_PATH . 'admin/redirect');
                } else {
                    die('aaaaa');
                    return false;
                }
            }
        }
    }
}

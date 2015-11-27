<?php
class Admin_SearchbarController extends Application_Admin_BaseController
{

    /**
     * check authentication before load the page
     *
     * @see Zend_Controller_Action::preDispatch()
     * @author kraj
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

    }

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );
        $message = $flash->getMessages ();
        $this->view->messageSuccess = isset ( $message [0] ['success'] ) ? $message [0] ['success'] : '';
        $this->view->messageError = isset ( $message [0] ['error'] ) ? $message [0] ['error'] : '';
    }
    /**
     * add new keyword
     * @author blal
     * @version 1.0
     */
    public function addkeywordAction()
    {
        $params = $this->_getAllParams();
        if($this->getRequest ()->isPost ()){
            $keyword = \KC\Repository\ExcludedKeyword::addKeywords($params);
            $flash = $this->_helper->getHelper ( 'FlashMessenger' );
            $message = $this->view->translate ( 'Excluded keyword has been created successfully' );
            $flash->addMessage ( array ('success' => $message ));
            $this->_redirect ( HTTP_PATH . 'admin/searchbar' );
        }

    }

    /**
     * get all keywords from database and display in a list
     * @author blal
     * @version 1.0
     */

    public function keywordlistAction()
    {
         $params = $this->_getAllParams();
        // cal to function in ExcludedKeyword model class
         $keywordList =  \KC\Repository\ExcludedKeyword::getKeywordList($params);
         echo Zend_Json::encode ( $keywordList );
         die ;
    }

    /**
     * edit excluded keyword by id
     *
     * @author blal
     * @version 1.0
     */
   public function editkeywordAction()
    {
        $id = $this->getRequest ()->getParam ('id');
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        if($id>0){
        // get keyword to edit on id basis
            $searchbar = \KC\Repository\ExcludedKeyword::getKeywordForEdit($id);
            //echo "<pre>";print_r($searchbar);die;
            $this->view->editKeyword = $searchbar;

         }
        if ($this->getRequest ()->isPost ()){
            $params = $this->getRequest ()->getParams ();
            // cal to update keyword function
            $searchbar = \KC\Repository\ExcludedKeyword::updateKeyword($params );
            $flash = $this->_helper->getHelper ( 'FlashMessenger' );
            $message = $this->view->translate ( 'Excluded Keyword has been updated successfully' );
            $flash->addMessage ( array ('success' => $message ) );
            $this->_redirect(HTTP_PATH.'admin/searchbar#'.$params['qString']);
        }
    }

    /**
     * delete excluded keyword by id
     *
     * @version 1.0
     * @author blal
     */
    public function deletekeywordsAction()
    {
        $id = $this->getRequest()->getParam('id');
        \KC\Repository\ExcludedKeyword::deleteKeyword($id);
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );
        $message = $this->view->translate ( 'Excluded Keyword has been deleted successfully' );
        $flash->addMessage ( array ('success' => $message ) );
        die();

    }

    /**
     * export excluded keyword list
     *
     * @version 1.0
     * @author blal
     */
    public function exportsearchbarlistAction()
    {
        $data = \KC\Repository\ExcludedKeyword::exportKeywordList ();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $this->view->translate('Excluded keywords'));
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $this->view->translate('Action'));
        $objPHPExcel->getActiveSheet()->setCellValue('C1', $this->view->translate('Created'));
        $column = 2;
        $row = 2;
        foreach ($data as $excludedkeyword) {
            $keyword = '';
            if ($excludedkeyword['keyword'] == '' || $excludedkeyword['keyword'] =='undefined' || $excludedkeyword['keyword'] == null || $excludedkeyword['keyword']=='0') {
                $keyword = '';
            } else {
                $keyword = $excludedkeyword['keyword'];
            }
            $action = '';
            if ($excludedkeyword['action'] =='' || $excludedkeyword['action'] == 'undefined' || $excludedkeyword['action'] == null) {
                $action = '';
            } else {
                $action = $excludedkeyword['action'];
                if ($action == '0') {
                    $action = "Redirect";
                } else {
                    $action = "Connect";
                }
            }
            $createdDate = $excludedkeyword['created_at']->format('Y-m-d');
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$column, $excludedkeyword['keyword']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $action);
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
                array('style' => PHPExcel_Style_Border::BORDER_THICK,
                            'color' => array('argb' => '000000'))
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'C1')->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:I'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'C1')->applyFromArray($borderStyle);
        $borderColumn =  (intval($column) -1 );
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'C'.$borderColumn)->applyFromArray($borderStyle);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="KeywordList.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        die();
    }

    /**
     * search top five shops for predictive search
     *
     * @author blal
     * @version 1.0
     */
    public function searchshopsAction()
    {
        $selectedShop = $this->getRequest()->getParam('selectedShop');
        $srh = $this->getRequest ()->getParam ( 'keyword' );
        $data = \KC\Repository\ExcludedKeyword::searchShops($srh,$selectedShop);
        $ar = array ();
        if (sizeof ( $data ) > 0) {
            foreach ( $data as $d ) {

                $ar[] = array("label"=>$d['name'],'value'=>$d['name'],'id'=>$d['id']);
            }
        } else {


            $msg = $this->view->translate ( 'No Record Found' );
            $ar[] = array("label"=>$msg,'value'=>$msg,'id'=>0);


        }
        echo Zend_Json::encode ( $ar );
        die ();

        // action body
    }

public function checkstoreexistAction()
{
    $id = $this->getRequest()->getParam('id');
    $retVal = \KC\Repository\ExcludedKeyword::checkStoreExistOrNot($id);
    echo Zend_Json::encode($retVal);
    die();
}
}

<?php
class Admin_RedirectController extends Zend_Controller_Action
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
        $conn2 = BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        $params = $this->_getAllParams();
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');

    }

    public function init()
    {
        /* a Initialize action controller here */
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
     * @author kraj
     * @version 1.0
     */
    public function addredirectAction()
    {
        $params = $this->_getAllParams();
        if($this->getRequest ()->isPost ()){
            $keyword = RouteRedirect::addRedirect($params);
            $flash = $this->_helper->getHelper ( 'FlashMessenger' );
            $message = $this->view->translate ( 'Redirect has been created successfully' );
            $flash->addMessage ( array ('success' => $message ));
            $this->_redirect ( HTTP_PATH . 'admin/redirect' );
        }

    }

    /**
     * get all Redirect from database and display in a list
     * @author kraj
     * @version 1.0
     */

    public function redirectlistAction()
    {
         $params = $this->_getAllParams();
        // cal to function in ExcludedKeyword model class
         $keywordList =  RouteRedirect::getRedirect($params);
         echo Zend_Json::encode ( $keywordList );
         die ;
    }

    /**
     * edit excluded keyword by id
     *
     * @author kraj
     * @version 1.0
     */
   public function editredirectAction()
   {
        $id = $this->getRequest ()->getParam ('id');
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        if($id>0){
        // get keyword to edit on id basis
            $searchbar = RouteRedirect::getRedirectForEdit($id);
            $this->view->editRedirect = $searchbar;

         }
        if ($this->getRequest ()->isPost ()){
            $params = $this->getRequest ()->getParams ();
            // cal to update keyword function
            $searchbar = RouteRedirect::updateRedirect($params );
            $flash = $this->_helper->getHelper ( 'FlashMessenger' );
            $message = $this->view->translate ( 'Redirect has been updated successfully' );
            $flash->addMessage ( array ('success' => $message ) );
            $this->_redirect(HTTP_PATH.'admin/redirect#'.$params['qString']);
        }
    }

    /**
     * delete excluded keyword by id
     *
     * @version 1.0
     * @author kraj
     */
    public function deleteredirectAction()
    {
        $id = $this->getRequest()->getParam('id');
        RouteRedirect::deleteRedirect($id);
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );
        $message = $this->view->translate ('Redirect has been deleted successfully' );
        $flash->addMessage ( array ('success' => $message ) );
        die();

    }

    /**
     * export excluded keyword list
     *
     * @version 1.0
     * @author kraj
     */
    public function exportredirectlistAction()
    {
        ini_set('max_execution_time',115200);
        //call to get all keywords function from database
        $data = RouteRedirect::exportRedirectList ();

        //create object of phpExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        //$objPHPExcel->getActiveSheet()->setCellValue('A1', $this->view->translate('Original URL'));
        //$objPHPExcel->getActiveSheet()->setCellValue('B1', $this->view->translate('Redirect To'));
        //$objPHPExcel->getActiveSheet()->setCellValue('C1', $this->view->translate('Created'));
        $column = 1;
        $row = 1;
        //loop for each keyword
        foreach ($data as $redirectTo) {

            $createdDate =  date("d-m-Y",strtotime($redirectTo['created_at']));
            //set value in column of excel
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$column, $redirectTo['orignalurl']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $redirectTo['redirectto']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$column, $createdDate);

            //counter incriment by 1
            $column++;
            $row++;
        }
        //FORMATING OF THE EXCEL
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
                                'color' => array('argb' => '000000'))));

        //HEADER COLOR

        //$objPHPExcel->getActiveSheet()->getStyle('A1:' . 'C1')->applyFromArray($headerStyle);

        //SET ALIGN OF TEXT
        //$objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$objPHPExcel->getActiveSheet()->getStyle('B2:I'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        //BORDER TO CELL
        //$objPHPExcel->getActiveSheet()->getStyle('A1:'.'C1')->applyFromArray($borderStyle);
        $borderColumn =  (intval($column) -1 );
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'C'.$column)->applyFromArray($borderStyle);


        //SET SIZE OF THE CELL
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);


        // redirect output to client browser

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Redirect.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        die();
    }

    public function importredirectAction()
    {
        ini_set('max_execution_time',115200);
        $params = $this->_getAllParams();
        if($this->getRequest ()->isPost ()){

            if (isset($_FILES['excelFile']['name']) && @$_FILES['excelFile']['name'] != '') {
                $RouteRedirectObj = new RouteRedirect();
                $result = @$RouteRedirectObj->uploadExcel($_FILES['excelFile']['name']);

                if($result['status'] == 200){

                    //Doctrine_Query::create()->delete('RouteRedirect')->execute();
                    //die();
                    //echo HTTP_PATH;
                    $spl = explode('/', HTTP_PATH);
                    //print_r($spl);
                    $path = $spl[0].'//' . $spl[2];
                    //die();
                    $excelFilePath = $result['path'];
                    $excelFile = $excelFilePath.$result['fileName'];

                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                    $objPHPExcel = $objReader->load(ROOT_PATH.$excelFile);
                    $worksheet = $objPHPExcel->getActiveSheet();
                    foreach ($worksheet->getRowIterator() as $row) {
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);
                        foreach ($cellIterator as $cell) {
                            $data[$cell->getRow()][$cell->getColumn()] = $cell->getValue();

                        }
                        $orignalURL =  $data[$cell->getRow()]['A'];
                        $redirectUrl =  $data[$cell->getRow()]['B'];
                        //find by name if exist in database
                        if(!empty($orignalURL)){
                            $redirect = Doctrine_Core::getTable('RouteRedirect')->findOneBy('orignalurl', $orignalURL);
                            if(!empty($redirect)){
                            }else{

                                $redirect  =new RouteRedirect();

                            }
                            if($orignalURL != " "){

                                $redirect->orignalurl= $orignalURL;
                            }
                            if($redirectUrl != " "){

                                $redirect->redirectto= $redirectUrl;
                            }
                            $redirect->save();

                        } else {
                            $flash = $this->_helper->getHelper ( 'FlashMessenger' );
                            $message = $this->view->translate ( 'Redirect has been updated successfully' );
                            $flash->addMessage ( array ('success' => $message ) );
                            $this->_redirect ( HTTP_PATH . 'admin/redirect' );
                        }

                    }
                    $flash = $this->_helper->getHelper ( 'FlashMessenger' );
                    $message = $this->view->translate ( 'Redirect has been updated successfully' );
                    $flash->addMessage ( array ('success' => $message ) );
                    $this->_redirect ( HTTP_PATH . 'admin/redirect' );
                } else{
                    die('aaaaa');
                    return false;
                }
            }
        }
    }
}

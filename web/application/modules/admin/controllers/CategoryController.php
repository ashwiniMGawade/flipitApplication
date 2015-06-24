<?php

class Admin_CategoryController extends Zend_Controller_Action
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
        $conn2 = \BackEnd_Helper_viewHelper::addConnection (); // connection
                                                             // generate with second
                                                             // database
        $params = $this->_getAllParams ();
        if (! \Auth_StaffAdapter::hasIdentity ()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect ( '/admin/auth/index' );
        }
        \BackEnd_Helper_viewHelper::closeConnection ( $conn2 );
        $this->view->controllerName = $this->getRequest ()->getParam ( 'controller' );
        $this->view->action = $this->getRequest ()->getParam ( 'action' );

    }
    public function init()
    {
        /*
         * Initialize action controller here
         */
    }
    public function indexAction()
    {
        // get flashes
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );
        $message = $flash->getMessages ();
        $this->view->messageSuccess = isset ( $message [0] ['success'] ) ? $message [0] ['success'] : '';
        $this->view->messageError = isset ( $message [0] ['error'] ) ? $message [0] ['error'] : '';
    }
    /**
     * add new category in database
     *
     * @version 1.0
     * @author blal
     */
    public function addcategoryAction()
    {
        if ($this->getRequest ()->isPost ()) {
            $params = $this->getRequest ()->getParams ();
            $category = \KC\Repository\Category::saveCategories ($params);
            self::updateVarnish($category[0]);
            $flash = $this->_helper->getHelper ( 'FlashMessenger' );
            if (gettype($category) == 'array') {
                $message = $this->view->translate ( 'Category has been created successfully' );
                $flash->addMessage ( array ('success' => $message ) );
            }else{
                $message = $this->view->translate ( 'Error: Your file size exceeded 2MB' );
                $flash->addMessage ( array ('error' => $message ) );
            }
                $this->_redirect ( HTTP_PATH . 'admin/category' );

        }
    }
    /**
     * upload image in edit mode
     *
     * @version 1.0
     * @author blal
     */
    public function uploadimageAction()
    {
        \KC\Repository\Category::uploadCategoriesImage ( $this->getRequest ()->getParams () );
    }
    /**
     * search top file category for predictive search
     *
     * @author blal
     * @version 1.0
     */
    public function searchtopfivecategoryAction()
    {
        $srh = $this->getRequest ()->getParam ( 'keyword' );
        $data = \KC\Repository\Category::searchToFiveCategory ( $srh);
        $ar = array ();
        if (sizeof ( $data ) > 0) {
            foreach ( $data as $d ) {

                $ar [] = $d ['name'];
            }
        } else {
            $msg = $this->view->translate ( 'No Record Found' );
            $ar [] = $msg;
        }
        echo Zend_Json::encode ( $ar );
        die ();

        // action body
    }
    /**
     * get all category from database
     * @author blal updated by kraj
     * @version 1.0
     */
    public function categorylistAction()
    {
        $params = $this->_getAllParams ();
        // cal to function in category model class
        $categoryList = \KC\Repository\Category::getCategoryList ( $params );
        echo Zend_Json::encode ( $categoryList );
        die ();

    }

    /**
     * edit category by id
     *
     * @author blal
     * @version 1.0
     */
    public function editcategoryAction()
    {
        $u = \Auth_StaffAdapter::getIdentity();
        $this->view->role = $u->users->id;
        $id = $this->getRequest()->getParam ( 'id' );
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        if ($id > 0) {
            // get edit category
            $category = \KC\Repository\Category::getCategoryInformation($id);
            $this->view->categoryDetail = $category;
        }

        if ($this->getRequest ()->isPost ()) {
            $params = $this->getRequest ()->getParams ();
            // cal to update category function
            $category = \KC\Repository\Category::updateCategory ( $params );

            self::updateVarnish($id);

            $flash = $this->_helper->getHelper ( 'FlashMessenger' );

            if ($category) {
                $message = $this->view->translate ( 'Category details have been updated successfully.' );
                $flash->addMessage ( array ('success' => $message ) );
            }else{
                $message = $this->view->translate ( 'Error: Your file size exceeded 2MB' );
                $flash->addMessage ( array ('error' => $message ) );
            }
            $this->_redirect ( HTTP_PATH . 'admin/category#'.$this->getRequest()->getParam('qString') );
        }

    }
    /**
     * change category status(online/ofline)
     *
     * @version 1.0
     * @author blal
     */
    public function categorystatusAction()
    {
        $params = $this->_getAllParams ();

        self::updateVarnish($params['id']);

        \KC\Repository\Category::changeStatus ( $params );
        die ();
    }
    /**
     * deleted category by id
     *
     * @version 1.0
     * @author blal
     */
    public function deletecategoryAction()
    {
        $params = $this->_getAllParams ();

        self::updateVarnish($params['id']);

        \KC\Repository\Category::deleteCategory ( $params );
        $flash = $this->_helper->getHelper ( 'FlashMessenger' );
        $message = $this->view->translate ( 'Category has been deleted successfully' );
        $flash->addMessage ( array ('success' => $message ) );
        die ();
    }

    /**
     * validate permalink
     *
     * @version 1.0
     * @author blal
     */
    public function validatepermalinkAction()
    {
        $url = $this->getRequest ()->getParam ( "permaLink" );
        $isEdit = $this->getRequest()->getParam("isEdit") ;
        $id = $this->getRequest()->getParam("id");


        $pattern = array ('/\s/','/[\,+@#$%^&*!]+/');

        $replace = array ("-","-");
        $url = preg_replace ( $pattern, $replace, $url );
        $url = strtolower($url);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $rp = $queryBuilder
            ->select('rp.permalink')
            ->from("\Core\Domain\Entity\RoutePermalink", "rp")
            ->where("rp.permalink = '".urlencode($url)."'")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        if($isEdit) {
            $exactLink = 'category/show/id/'.$id;

            if(@$rp[0]['permalink'] == $url ) {
                if( @$rp[0]['exactlink'] == $exactLink){
                    $res = array( 	'status' => '200' ,
                            'url' => $url ,
                            'shopNavUrl' => $url ) ;

                    echo Zend_Json::encode($res ) ;
                    die ;
                }else	{

                    $res = false ;
                    echo Zend_Json::encode( $res ) ;
                    die ;
                }
            }
        }


        if( strlen($url )  > 0) {

            if(@$rp[0]['permalink'] != $url ) {
                $res = array ( 'status' => '200',
                        'url' => $url,
                        'permaLink' =>
                        $this->getRequest ()->getParam ( "articlepermalink" )
                );
            }else {

            $res = false;
            }
        } else	{

            $res = false ;
        }
        echo Zend_Json::encode ( $res );

        die ();
    }

    /**
     * export category list
     *
     * @version 1.0
     * @author blal updated by kraj
     */
    public function exportcategorylistAction()
    {
        // get all category from database
        $data = \KC\Repository\Category::getAllCategories();

        // create object of phpExcel
        $objPHPExcel = new PHPExcel ();
        $objPHPExcel->setActiveSheetIndex ( 0 );
        $objPHPExcel->getActiveSheet ()->setCellValue ( 'A1', $this->view->translate ( 'Category' ) );
        $objPHPExcel->getActiveSheet ()->setCellValue ( 'B1', $this->view->translate ( 'Online' ) );
        $column = 2;
        $row = 2;
        // loop for each category
        foreach ( $data as $category ) {

            // condition apply on offer
            $name = '';
            if ($category[0]['name'] == '' || $category[0]['name'] == 'undefined' || $category[0]['name'] == null || $category[0]['name'] == '0') {

                $name = '';

            } else {

                $name = $category[0]['name'];
            }

            $status = '';
            if ($category[0]['status'] == true) {

                $status = $this->view->translate ( 'Yes' );

            } else {

                $status = $this->view->translate ( 'No' );
            }
            // set value in column of excel
            $objPHPExcel->getActiveSheet ()->setCellValue ( 'A' . $column, $name );
            $objPHPExcel->getActiveSheet ()->setCellValue ( 'B' . $column, $status );
            // counter incriment by 1
            $column ++;
            $row ++;
        }
        // FORMATING OF THE EXCELL
        $headerStyle = array ('fill' => array ('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array ('rgb' => '00B4F2' ) ), 'font' => array ('bold' => true ) );
        $borderStyle = array ('borders' => array ('outline' => array ('style' => PHPExcel_Style_Border::BORDER_THICK, 'color' => array ('argb' => '000000' ) ) ) );
        // HEADER COLOR

        $objPHPExcel->getActiveSheet ()->getStyle ( 'A1:' . 'B1' )->applyFromArray ( $headerStyle );

        // SET ALIGN OF TEXT
        $objPHPExcel->getActiveSheet ()->getStyle ( 'A1:B1' )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
        $objPHPExcel->getActiveSheet ()->getStyle ( 'B2:I' . $row )->getAlignment ()->setVertical ( PHPExcel_Style_Alignment::VERTICAL_TOP );

        // BORDER TO CELL
        $objPHPExcel->getActiveSheet ()->getStyle ( 'A1:' . 'B1' )->applyFromArray ( $borderStyle );
        $borderColumn = (intval ( $column ) - 1);
        $objPHPExcel->getActiveSheet ()->getStyle ( 'A1:' . 'B' . $borderColumn )->applyFromArray ( $borderStyle );

        // SET SIZE OF THE CELL
        $objPHPExcel->getActiveSheet ()->getColumnDimension ( 'A' )->setAutoSize ( true );
        $objPHPExcel->getActiveSheet ()->getColumnDimension ( 'B' )->setAutoSize ( true );

        // redirect output to client browser
        header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
        header ( 'Content-Disposition: attachment;filename="categoryList.xlsx"' );
        header ( 'Cache-Control: max-age=0' );
        $objWriter = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
        $objWriter->save ( 'php://output' );
        die ();

    }


    /**
     *  updateVarnish
     *
     *  update varnish table when a category is edited, updated and deleted
     *  @param integer $id widget id
     */
    public function updateVarnish($id)
    {
        # Add urls to refresh in Varnish
        $varnishObj = new \KC\Repository\Varnish();

        $varnishObj->addUrl(HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_categorieen'));
        # get all the urls related to this category
        $varnishUrls = \KC\Repository\Category::getAllUrls($id); 

        # check $varnishUrls has atleast one
        if(isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach($varnishUrls as $value) {
                $varnishObj->addUrl( HTTP_PATH_FRONTEND . $value);
            }
        }
    }
}

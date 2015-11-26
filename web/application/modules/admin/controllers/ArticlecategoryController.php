<?php
class Admin_ArticlecategoryController extends Application_Admin_BaseController
{
/**
 * initialize flash messages on view.
 * (non-PHPdoc)
 * @see Zend_Controller_Action::init()
 */
    public function init()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';
    }

    /**
     * preDispatch function redirect the user to login page if session is not set.
     * @see Zend_Controller_Action::preDispatch()
     * @author mkaur
     */
    public function preDispatch()
    {

        $conn2 = \BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
        $params = $this->_getAllParams();
        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');

    }

    public function indexAction()
    {
    }

    public function addcategoryAction()
    {
        $category = new \KC\Repository\Category();
        $categoryList =  $category->getCategoryList() ;
        $this->view->categoryList = $categoryList['aaData'] ;

        if ($this->getRequest()->isPost()) {
            $save = new KC\Repository\ArticleCategory();
            $result = $save->addcategory($this->getRequest()->getParams());
            $flash = $this->_helper->getHelper('FlashMessenger');

            if ($result) {

                self::updateVarnish($result);

                $message = $this->view->translate('Article category has been created successfully');
                $flash->addMessage(array('success' => $message));
                $this->_helper->redirector(null, 'articlecategory', null);

            } else {

                $message = $this->view->translate('Error: Your file size exceeded 2MB');
                $flash->addMessage(array('error' => $message));
                $this->_helper->redirector(null, 'articlecategory', null);
            }

        }

    }

    public function getcategoriesAction()
    {
        $getList = new \KC\Repository\ArticleCategory();
        $list = $getList->getCategoryList($this->getRequest()->getParams());
        echo Zend_Json::encode($list);
        die;
    }

    public function searchkeyAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = $this->getRequest()->getParam('flag');
        $data = KC\Repository\ArticleCategory::searchKeyword($srh, $flag);
        $ar = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {

                $ar[] = ucfirst($d['name']);

            }

        } else {

            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }
        echo Zend_Json::encode($ar);
        die;
    }

    public function editcategoryAction()
    {
        $this->view->role = Zend_Auth::getInstance()->getIdentity()->users->id;
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        $category = new \KC\Repository\Category();
        $categoryList =  $category->getCategoryList() ;

        $this->view->categoryList = $categoryList['aaData'] ;

        $data = new \KC\Repository\ArticleCategory();

        $id = $this->getRequest()->getParam('id');
        $varnishUrls = \KC\Repository\ArticleCategory::getAllUrls($id);
        if ($this->getRequest()->isPost()) {
            $result = $data->editCategory($this->getRequest()->getParams(), 'post');
            $flash = $this->_helper->getHelper('FlashMessenger');

            if ($result) {

                self::updateVarnish($id);

                $message = $this->view->translate('Article category has been updated successfully');
                $flash->addMessage(array('success' => $message));

            } else {
                $message = $this->view->translate('Error: Your file size exceeded 2MB');
                $flash->addMessage(array('error' => $message));

            }
            $this->_redirect(HTTP_PATH.'admin/articlecategory#'.$this->getRequest()->getParam('qString'));

        } else {
            $this->view->data = $data->editCategory($this->getRequest()->getParams(), null);

        }
    }

    public function deletecategoryAction()
    {
        $id = $this->getRequest()->getParam('id');

        self::updateVarnish($id);


        $deletePermanent = \KC\Repository\ArticleCategory::permanentDeleteArticleCategory($id);
        $flash = $this->_helper->getHelper('FlashMessenger');
        if (intval($deletePermanent) > 0) {


            $message = $this->view
            ->translate('Record has been deleted successfully.');
            $flash->addMessage(array('success' => $message));

        } else {

            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
        }
        echo Zend_Json::encode($deletePermanent);
        die;
    }

    /**
     * Export Article list in Excel file
     * @author Chetan
     * @version 1.0
     */
    public function exportarticlecategorylistAction()
    {
        // get all shop from database
        $data = \KC\Repository\ArticleCategory::exportarticlecategorylist();

        // create object of phpExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $this->view->translate('Category Name'));
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $this->view->translate('Permalink'));
        $objPHPExcel->getActiveSheet()->setCellValue('C1', $this->view->translate('Meta title'));
        $column = 2;
        $row = 2;
        // loop for each page
        foreach ($data as $page) {

            // condition apply on pagetitle
            $title = '';
            if ($page['name'] == '' || $page['name'] == 'undefined'
                    || $page['name'] == null || $page['name'] == '0') {

                $title = '';

            } else {

                $title = $page['name'];
            }

            if ($page['permalink'] == '' || $page['permalink'] == 'undefined'
                    || $page['permalink'] == null || $page['permalink'] == '0') {

                $permalink = '';

            } else {

                $permalink = $page['permalink'];
            }

            if ($page['metatitle'] == '' || $page['metatitle'] == 'undefined'
                    || $page['metatitle'] == null || $page['metatitle'] == '0') {

                $metatitle = '';

            } else {

                $metatitle = $page['metatitle'];
            }


            // set value in column of excel
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $column, $title);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $column, $permalink);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $column, $metatitle);



            // counter incriment by 1
            $column++;
            $row++;
        }
        // FORMATING OF THE EXCELL
        $headerStyle = array(
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '00B4F2')),
                'font' => array('bold' => true));
        $borderStyle = array(
                'borders' => array(
                        'outline' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THICK,
                                'color' => array('argb' => '000000'))));
        // HEADER COLOR

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'C1')
        ->applyFromArray($headerStyle);

        // SET ALIGN OF TEXT
        $objPHPExcel->getActiveSheet()->getStyle('A1:C1')->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:F' . $row)->getAlignment()
        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        // BORDER TO CELL
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'C1')
        ->applyFromArray($borderStyle);
        $borderColumn = (intval($column) - 1);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'C' . $borderColumn)
        ->applyFromArray($borderStyle);

        // SET SIZE OF THE CELL
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);

        // redirect output to client browser
        $fileName =  $this->view->translate('ArticleCategoryList.xlsx');
        header(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        header('Content-Disposition: attachment;filename='.$fileName);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        die();

    }

    /**
     * validate permalink
     *
     * @version 1.0
     * @author chetan
     */
    public function validatepermalinkAction()
    {
        $url = $this->getRequest()->getParam("permaLink");
        $id =  $this->getRequest()->getParam("id");
        $pattern = array('/\s/','/[\,+@#$%^&*!]+/');

        $replace = array("-","-");
        $url = preg_replace($pattern, $replace, $url);

        $url = strtolower($url);

        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $query = $queryBuilder->select('p')
            ->from('\Core\Domain\Entity\RoutePermalink', 'p')
            ->where('p.permalink ='.  $queryBuilder->expr()->literal($url));
        $rp = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if ($id != '') {
            $exactLink = 'moneysavingguide/category/id/'.$id;

            if (@$rp[0]['permalink'] == $url) {
                if (@$rp[0]['exactlink'] == $exactLink) {

                    $res = array(
                        'status' => '200' ,
                        'url' => substr($url, 3),
                        'permaLink' => $url
                    );

                    echo Zend_Json::encode($res);
                    die ;
                } else {

                    $res = false;
                    echo Zend_Json::encode($res);
                    die;
                }
            }
        }


        if (strlen($url) > 0) {

            if (@$rp[0]['permalink'] != $url) {

                $res = array('status' => '200',
                        'url' => substr($url, 3),
                        'permaLink' =>
                        $this->getRequest()->getParam("permaLink")
                );
            } else {
                $res = false;
            }
        } else {
            $res = false;
        }
        echo Zend_Json::encode($res);
        die ();
    }




    /**
     *  updateVarnish
     *
     *  update varnish table when an article category  is cretaed, updated and deleted
     *  @param integer $id acticle category id
     */
    public function updateVarnish($id)
    {
        // Add urls to refresh in Varnish
        $varnishObj = new \KC\Repository\Varnish();
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . strtolower('plus'));
        # get all the urls related to this Articlecategory
        $varnishUrls = \KC\Repository\ArticleCategory::getAllUrls($id);

        # check $varnishUrls has atleast one
        if (isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach ($varnishUrls as $value) {
                $varnishObj->addUrl(HTTP_PATH_FRONTEND  . $value);
            }
        }
    }
}

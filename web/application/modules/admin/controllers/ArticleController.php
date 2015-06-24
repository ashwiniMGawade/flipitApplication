<?php
class Admin_ArticleController extends Zend_Controller_Action
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
 * preDispatch function redirect the user entertainmento login page if session is not set.
 * @see Zend_Controller_Action::preDispatch()
 * @author mkaur
 */
    public function preDispatch()
    {
        //echo "<pre>"; print_r($_SERVER); die;
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
    public function indexAction()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';

    }

    public function getarticlesAction()
    {
        $getList = new KC\Repository\Articles();
        $list = $getList->getArticleList($this->getRequest()->getParams());
        echo Zend_Json::encode($list);
        die;


    }

    public function gettrashlistAction()
    {
        $getList = new \KC\Repository\Articles();
        $list = $getList->getTrashedList($this->getRequest()->getParams());
        echo Zend_Json::encode($list);
        die;
    }

    public function createarticleAction()
    {
        
        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('cat')
            ->from('\Core\Domain\Entity\Articlecategory', 'cat')
            ->where('cat.deleted= 0')
            ->orderBy('cat.name', 'ASC');

        $this->view->articleCategory = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
      
        if (isset($_COOKIE['site_name'])) {
            $site_name = "http://www.".$_COOKIE['site_name'];
        }
        $this->view->authorList = KC\Repository\Articles::getAuthorList($site_name);

        if ($this->getRequest()->isPost()) {

            //echo "<pre>"; print_r($this->getRequest()->getParams()); die;

            $result = KC\Repository\Articles::saveArticle($this->getRequest()->getParams());


            $flash = $this->_helper->getHelper('FlashMessenger');

            //echo "<pre>"; print_r($result);die;


            if ($result) {

                KC\Repository\PopularArticles::deletePopularArticles();
                $allArticles = KC\Repository\Articles::getArticlesList();
                $position = 1;
                foreach ($allArticles as $article) {
                    \KC\Repository\PopularArticles::savePopularArticle($article['id'], $position);
                    $position++;
                }

                # update only when article is being published immedately or some time later
                if (! $result['isDraft']) {
                    self::updateVarnish($result['articleId']);
                }

                $val = $this->getRequest()->getParams();
                if (isset($val['savePagebtn']) && @$val['savePagebtn'] == 'draft') {

                    $createdartid = KC\Repository\Articles::getArticleId();
                    $actualval = $createdartid[0]['id'];
                    $redirecturl = "/admin/article/editarticle/id/".$actualval;
                    $this->_redirect($redirecturl);
                    exit();
                }
                $message = $this->view->translate('The Article has been saved successfully');
                $flash->addMessage(array('success' => $message));
                $this->_helper->redirector(null, 'article', null);
            } else {
                $message = $this->view->translate('Error: Your file size exceeded 2MB');
                $flash->addMessage(array('error' => $message));
                $this->_helper->redirector(null, 'article', null) ;
            }
        }
    }
    /**
     * get all trashed pages from database
     * @return array $data
     * @author jsingh5
     * @version 1.0
    */
    public function trashlistAction()
    {
        $params = $this->_getAllParams();
        $articlesObj = new KC\Repository\Articles();
        // cal to function in network model class
        $articlesList =  $articlesObj->gettrashedArticles($params);
        echo Zend_Json::encode($articlesList);
        die ;
    }

    public function editarticleAction()
    {
        $this->view->role = Zend_Auth::getInstance()->getIdentity()->users->id;

        $entityManagerLocale = \Zend_Registry::get('emLocale');
        $queryBuilder = $entityManagerLocale->createQueryBuilder();
        $query = $queryBuilder->select('cat')
            ->from('\Core\Domain\Entity\Articlecategory', 'cat')
            ->where('cat.deleted= 0')
            ->orderBy('cat.name', 'ASC');

        $this->view->articleCategory = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
       
        $articleId = $this->getRequest()->getParam('id');
        if (isset($_COOKIE['site_name'])) {
            $site_name = "http://www.".$_COOKIE['site_name'];
        }
        $this->view->authorList = KC\Repository\Articles::getAuthorList($site_name);


        $articlcData = KC\Repository\Articles::getArticleData($this->_getAllParams());


        #redirect to article list
        if (!$articlcData) {
            $this->_helper->redirector(null, 'article', null);
        }

        $this->view->articleData = $articlcData;
        $this->view->qstring = $_SERVER['QUERY_STRING'];

        if ($this->getRequest()->isPost()) {

            $result = KC\Repository\Articles::editArticle($this->getRequest()->getParams());
            $flash = $this->_helper->getHelper('FlashMessenger');
            if ($result) {

                self::updateVarnish($articleId);

                $message = $this->view->translate('The Article has been updated successfully');
                $flash->addMessage(array('success' => $message));

            } else {
                $message = $this->view->translate('Error: Your file size exceeded 2MB');
                $flash->addMessage(array('error' => $message));

            }
            $this->_redirect(HTTP_PATH.'admin/article#'.$this->getRequest()->getParam('qString'));
        }
    }

    public function searchkeyAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = $this->getRequest()->getParam('flag');

        if ($this->getRequest()->getParam('keyword') != '' && $this->getRequest()->getParam('keyword') != 'undefined') {
            $alreadySelected = explode(',', $this->getRequest()->getParam('selectedshops'));
        }

        $data = KC\Repository\Articles::getAllStores($srh, $flag);
        $ar = array();
        $i = 0;
        if (sizeof($data) > 0) {
            foreach ($data as $d) {

                if (!in_array($d['id'], $alreadySelected)) {
                    $ar[$i]['label'] = ucfirst($d['name']);
                    $ar[$i]['value'] = ucfirst($d['name']);
                    $ar[$i]['id'] = $d['id'];
                    $i++;
                }
            }

        }
        if (sizeof($ar) == 0) {
            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }
        //print_r($ar); die;
        echo Zend_Json::encode($ar);
        die;
    }

    /**
     * search to five shop from database by flag
     * flag (1 deleted  or 0 not deleted )
     * @author kraj
     * @version 1.0
     */
    public function searchtopfivearticleAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = $this->getRequest()->getParam('flag');
        //cal to searchToFiveShop function from offer model class

        $data = \KC\Repository\Articles::searchKeyword($srh, $flag);
        $ar = array();
        $removeDup = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {

                $id =  $d['id'];
                //array fro remove duplicate search text
                if(isset($removeDup[$id])) {
                    $removeDup[$id] = $id;

                } else {

                    $removeDup[$id] = $id;
                    $ar[] = ucfirst($d['title']);

                }

            }

        } else {

            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }
        echo Zend_Json::encode($ar);
        die;
    }





    public function searchkeyarticlesAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = $this->getRequest()->getParam('flag');
        $data = KC\Repository\Articles::searchKeyword($srh, $flag);

        $ar = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                $ar[] = ucfirst($d['title']);
            }

        } else {
            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }
        echo Zend_Json::encode($ar);
        die;
    }

    /**
     * validate permalink
     *
     * @version 1.0
     * @author kkumar
     */
    public function validatepermalinkAction()
    {
        $url = $this->getRequest()->getParam("articlepermalink");

        $pattern = array('/\s/',"/[\,+@#$%'^&*!]+/");
        $replace = array("-","-");

        $url = preg_replace($pattern, $replace, $url);
        $url = trim(strtolower($url), "-");
        $id = $this->getRequest()->getParam('id');
        $isEdit = $this->getRequest()->getParam('isEdit');
        if ($isEdit==1) {

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('a')
                ->from('\Core\Domain\Entity\Articles', 'a')
                ->where('a.permalink ='.  $queryBuilder->expr()->literal(urlencode($url)))
                ->andWhere('a.id ='. $id);
            $rp = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            if (empty($rp)) {

                $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                $query = $queryBuilder->select('a')
                    ->from('\Core\Domain\Entity\Articles', 'a')
                    ->where('a.permalink ='.  $queryBuilder->expr()->literal(urlencode($url)));
                $rp = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                if (empty($rp)) {
                    $res = array( 	'status' => '200' ,
                            'url' => $url ,
                            'permaLink' => $url ) ;

                    echo Zend_Json::encode($res);
                    die ;
                } else {
                    echo Zend_Json::encode(false);
                    die ;
                }
            } else {
                    $res = array( 	'status' => '200' ,
                            'url' => $url ,
                            'permaLink' => $url ) ;

                    echo Zend_Json::encode($res);
                    die ;

            }
        } else {

            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $query = $queryBuilder->select('a')
                ->from('\Core\Domain\Entity\Articles', 'a')
                ->where('a.permalink ='.  $queryBuilder->expr()->literal(urlencode($url)));
            $rp = $query->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
           
            if (empty($rp)) {
                $res = array( 	'status' => '200' ,
                        'url' => $url ,
                        'permaLink' => $url ) ;

                echo Zend_Json::encode($res);
                die ;
            } else {
                echo Zend_Json::encode(false);
                die ;
            }
        }
        die;
    }

    /**
     * Export Article list in Excel file
     * @author Chetan
     * @version 1.0
     */
    public function exportarticlelistAction()
    {
        // get all shop from database
        $data = KC\Repository\Articles::exportarticlelist();
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $this->view->translate('Article Name'));
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $this->view->translate('Created'));
        $objPHPExcel->getActiveSheet()->setCellValue('C1', $this->view->translate('Published'));
        $objPHPExcel->getActiveSheet()->setCellValue('D1', $this->view->translate('Author'));
        $column = 2;
        $row = 2;
        // loop for each page
        foreach ($data as $page) {

            // condition apply on pagetitle
            $title = '';
            if ($page['title'] == '' || $page['title'] == 'undefined'
                    || $page['title'] == null || $page['title'] == '0') {

                $title = '';

            } else {

                $title = $page['title'];
            }


            // get created from array
            $Created = $page['created_at']->format('d-m-Y');

            // get Published from Array

            $Published = '';
            if ($page['publish'] == true) {

                $Published= $this->view->translate('Yes');
            } else {
                $Published = $this->view->translate('No');
            }

            //get author name
            $Author = $page['authorname'];


            // set value in column of excel
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $column, $title);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $column, $Created);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $column, $Published);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $column, $Author);



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

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'D1')
        ->applyFromArray($headerStyle);

        // SET ALIGN OF TEXT
        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:F' . $row)->getAlignment()
        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        // BORDER TO CELL
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'D1')
        ->applyFromArray($borderStyle);
        $borderColumn = (intval($column) - 1);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'D' . $borderColumn)
        ->applyFromArray($borderStyle);

        // SET SIZE OF THE CELL
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

        // redirect output to client browser
        $fileName =  $this->view->translate('ArticleList.xlsx');
        header(
                'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$fileName);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        die();

    }

    /**
     * delete page from database by id
     * @author jsingh5
     * @version 1.0
     */
    public function deletearticlesAction()
    {
        $id = $this->getRequest()->getParam('id');
        //cal to deleteOffer function from offer model class
        $deletePermanent = \KC\Repository\Articles::deleteArticles($id);
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
     * restore page by id
     * @author jsingh5
     * @version 1.0
     */
    public function restorearticleAction()
    {
        $id = $this->getRequest()->getParam('id');
        //cal to restoreOffer function from offer model class
        $restore = \KC\Repository\Articles::restoreArticles($id);

        if (intval($restore) > 0) {

            self::updateVarnish($id);

            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view
            ->translate('Record has been restored successfully.');
            $flash->addMessage(array('success' => $message));

        } else {

            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
        }

        echo Zend_Json::encode($restore);
        die;
    }

    /**
     * get trashed offer from database
     * @author jsingh5
     * @version 1.0
     */
    public function trasharticleAction()
    {
        $role = Zend_Auth::getInstance()->getIdentity()->users->id;
        if ($role=='1' || $role=='2') {
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $flash->getMessages();
            $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
            $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';

        } else {

            $this->_redirect(HTTP_PATH.'admin/article');
        }
    }

    /**
     * move to trash
     * @author jsingh5
     * @version 1.0
     */
    public function movetotrashAction()
    {
        $id = $this->getRequest()->getParam('id');

        $trash = KC\Repository\Articles::moveToTrash($id);

        if (intval($trash) > 0) {

            self::updateVarnish($id);

            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Article has been moved to trash successfully');
            $flash->addMessage(array('success' => $message));

        } else {

            $message = $this->view->translate('Problem in your data');
            $flash->addMessage(array('error' => $message));
        }
        echo Zend_Json::encode($trash);
        die;




    }

    public function chaptersAction()
    {
        $this->_helper->layout()->disableLayout();

        if ($this->getRequest()->getParam('partialCounter') > 0) {

            $count = $this->getRequest()->getParam('partialCounter');

            $this->view->partialCounter = $count;
        }
    }

    public function deletechaptersAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id > 0) {
            $articles = \KC\Repository\Articles::deletechapters($id);
        }
        echo Zend_Json::encode($articles);
        die;
    }


    /**
     *  updateVarnish
     *
     *  update varnish table when an article  is cretaed, edited, updated and deleted
     *  @param integer $id article id
     */
    public function updateVarnish($id)
    {
        # Add urls to refresh in Varnish
        $varnishObj = new KC\Repository\Varnish();
        $varnishObj->addUrl(HTTP_PATH_FRONTEND);
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . strtolower('plus'));
        # make markplaatfeed url's get refreashed only in case of kortingscode
        if (LOCALE == '') {
            $varnishObj->addUrl(HTTP_PATH_FRONTEND  . 'marktplaatsfeed');
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'marktplaatsmobilefeed');
        }
        # get all the urls related to this shop
        $varnishUrls = \KC\Repository\Articles::getAllUrls($id);
        # check $varnishUrls has atleast one
        if (isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach ($varnishUrls as $value) {
                $varnishObj->addUrl(HTTP_PATH_FRONTEND  . $value);
            }
        }
    }
}

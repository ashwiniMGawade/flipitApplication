<?php

class Admin_ShopController extends Zend_Controller_Action
{

    public $mandrillKey = '';
    public $exportPassword = '';
    public $mandrillSenderEmailAddress = '';
    public $mandrillSenderName = '';
  
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
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // set logged in role
        $u = Auth_StaffAdapter::getIdentity();
        $this->view->role = $u->users->id;
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';

    }

    public function getshopAction()
    {
        $params = $this->_getAllParams();
        //cal to getshoplist function from Shop model
        $shopList = \KC\Repository\Shop::getshopList($params);
        echo Zend_Json::encode($shopList);
        die;
    }

    public function gettrashshopAction()
    {
        $params = $this->_getAllParams();
        $trashShopList = \KC\Repository\Shop::gettrashshopList($params);
        echo Zend_Json::encode($trashShopList);
        die;
    }


    public function movetotrashAction()
    {
        $id = $this->getRequest()->getParam('id');
        //cal to function moveToTrash from Shop model
        $trash = \KC\Repository\Shop::moveToTrash($id);
        if (intval($trash) > 0) {
            self::updateVarnish($id);
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Record has been moved to trash');
            $flash->addMessage(array('success' => $message));
        } else {
            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
        }
        echo Zend_Json::encode($trash);
        die;
    }

    public function deleteshopAction()
    {
        $id = $this->getRequest()->getParam('id');
        //cal permanentDeleteShop function from Shop model class
        $deletePermanent = \KC\Repository\Shop::permanentDeleteShop($id);
        $flash = $this->_helper->getHelper('FlashMessenger');
        if ($deletePermanent) {
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

    public function restoreshopAction()
    {
        $id = $this->getRequest()->getParam('id');
        //cal to restoreShop function from offer model class
        $restore = \KC\Repository\Shop::restoreShop($id);

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

    public function searchkeyAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = $this->getRequest()->getParam('flag');
        $data = \KC\Repository\Shop::searchKeyword($srh, $flag);

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

    public function searchsimilarAction()
    {
        $srh = $this->getRequest()->getParam('keyword')=='undefined' ? '' : $this->getRequest()->getParam('keyword');
        $flag = $this->getRequest()->getParam('flag');
        $selctedshop = $this->getRequest()->getParam('selctedshop')=='undefined' ? '' : $this->getRequest()->getParam('selctedshop');
        if ($selctedshop=='') {
            $selctedshop = 0;
        }
        $selctedshop = $this->getRequest()->getParam('currentshopId').','.$selctedshop;
        $data =\KC\Repository\Shop::searchsimilarStore($srh, $flag, $selctedshop);
        $ar = $br =  array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                $ar[] = ucfirst($d['name']);
                $ar[] = ucfirst($d['id']);
            }
        } else {
            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }
        echo Zend_Json::encode($ar);
        die;
        // action body
    }

    public function uploadimageAction()
    {

        $uploadPath = "images/upload/shop/";
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $user_path = ROOT_PATH . $uploadPath;
        $img = $this->getRequest()->getParam('imageName');
        $fileEl = $this->getRequest()->getParam('browsedEl');
        //unlink image file from folder if exist
        if ($img) {
            @unlink($user_path . $img);
            @unlink($user_path . "thum_" . $img);
            @unlink($user_path . "thum_large" . $img);
        }
        if (!file_exists($user_path)) {
            mkdir($user_path);
        }
        $adapter->setDestination(ROOT_PATH . $uploadPath);
        $adapter->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $files = $adapter->getFileInfo();
        foreach ($files as $file => $info) {
            $name = $adapter->getFileName($file, false);
            $name = $adapter->getFileName($file);
            $orgName = time() . "_" . $info['name'];
            $fname = $user_path . $orgName;
            //call function resize image

            switch($fileEl) {
                case 'logo_file':
                    /**
                     *	 generating thumnails for upload logo
                    */
                    $path = ROOT_PATH . $uploadPath . "thum_" . $orgName;

                    BackEnd_Helper_viewHelper::resizeImage(
                        $_FILES[$fileEl],
                        $orgName,
                        200,
                        150,
                        $path
                    );
                    $path = ROOT_PATH . $uploadPath . "thum_large" . $orgName;
                    BackEnd_Helper_viewHelper::resizeImage(
                        $_FILES[$fileEl],
                        $orgName,
                        132,
                        95,
                        $path
                    );
                    break ;
                case 'small_logo_file':
                    break ;
                case 'big_logo_file':
                    break ;
            }

            $adapter->addFilter(
                new Zend_Filter_File_Rename(
                    array(
                        'target' => $fname,
                        'overwrite' => true
                    )
                ),
                null,
                $file
            );

            $adapter->receive($file);
            $status = "";
            $data = "";
            $msg = "";
            if ($adapter->isValid($file) == 1) {
                $data = $orgName;
                $status = "200";
                $statusMessage = "File uploaded successfully.";

            } else {
                $status = "-1";
                $msg = "Please upload the valid file";
            }
            echo Zend_Json::encode(
                array(
                    "fileName" => $data,
                    "sttaus" => $status,
                    "msg" => $msg,
                    "displayFileName" => $info['name'],
                    "path" => "$uploadPath",
                    "bEl" => $fileEl
                )
            );
            die();
        }
    }

    public function viewcontentAction()
    {
        //     	print_r($this->_getAllParams());
        //     	die ;
    }

    public function validatenavurlAction()
    {
        $url = $this->getRequest()->getParam("shopNavUrl");
        $isEdit = $this->getRequest()->getParam("isEdit");

        $pattern = array('/\s/',"/[\,+@#$%'^&*!]+/");

        $replace = array("-","-");
        $url = preg_replace($pattern, $replace, $url);
        $url = strtolower($url);
        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
        $rp = $queryBuilder->select("r")
            ->from("KC\Entity\RoutePermalink", "r")
            ->where("r.permalink = '".urlencode($url)."'")
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        if ($isEdit) {
            $exactLink = "store/storedetail/id/".$this->getRequest()->getParam("id") ;
            if (@$rp[0]['permalink'] == $url) {
                if (@$rp[0]['exactlink'] == $exactLink) {
                    $res = array( 	'status' => '200' ,
                    'url' => $url ,
                    'shopNavUrl' => $url ) ;
                    echo Zend_Json::encode($res);
                    die;
                } else {
                    $res = false ;
                    echo Zend_Json::encode($res);
                    die;
                }
            }
        }
        if (strlen($url) > 0) {
            if (@$rp[0]['permalink'] != $url) {
                $res = array(
                    'status' => '200',
                    'url' => $url,
                    'permaLink' => $this->getRequest()->getParam("articlepermalink")
                );
            } else {
                $res = false;
            }
        } else {
            $res = false;
        }
        echo Zend_Json::encode($res);
        die();
    }

    public function createshopAction()
    {
        $arr = array();
        $arr['status'] = '1';
        $category = new \KC\Repository\Category();
        $this->view->categoryList = $category->getCategoriesInformation();

        $site_name = "";
        if (isset($_COOKIE['site_name'])) {
            $site_name =  $_COOKIE['site_name'];
        }

        $users = new \KC\Repository\User();
        $this->view->MangersList = $users->getManagersLists($site_name);
        $pages = new \KC\Repository\Page();
        $this->view->DefaultPagesList = $pages->defaultPagesList();
        $affiliate = new \KC\Repository\AffliateNetwork();
        $arr['sortBy'] = 'name';
        $arr['off'] = '1';
        $affiliateNetworkList =  $affiliate->getNetworkList($arr);
        $this->view->affiliateNetworkList = $affiliateNetworkList['aaData'];

        if ($this->_request->isPost()) {
            $parmas = $this->_getAllParams();
            $shop = new \KC\Repository\Shop();
            $flash = $this->_helper->getHelper('FlashMessenger');
            if ($parmas['shopName'] != null && $parmas['shopName'] != '') {
                $shopId = $shop->CreateNewShop($parmas);
                if ($shopId) {
                    self::updateVarnish($shopId);
                    $message = $this->view->translate('The shop has been saved successfully');
                    $flash->addMessage(array('success' => $message));
                    $this->_helper->redirector(null, 'shop', null);
                } else {
                    $message = $this->view->translate('Error: Your file size exceeded 2MB');
                    $flash->addMessage(array('error' => $message));
                    $this->_helper->redirector(null, 'shop', null);
                }
            } else {
                $message = $this->view->translate('Error: Invalid shop name');
                $flash->addMessage(array('error' => $message));
                $this->_helper->redirector(null, 'shop', null);
            }
        }
    }

    public function editshopAction()
    {

        // set logged in role
        $u = Auth_StaffAdapter::getIdentity();
        $this->view->role = $u->users->id;
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        /* get Category List*/
        $arr = array();
        /* get Category List*/
        $arr['status'] = '1';
        $category = new \KC\Repository\Category();
        $this->view->categoryList = $category->getCategoriesInformation();
        $id = $this->getRequest()->getParam('id');
        $site_name = "kortingscode.nl";
        if (isset($_COOKIE['site_name'])) {
            $site_name =  $_COOKIE['site_name'];
        }


        // display managers and account managers list
        $users = new \KC\Repository\User();

        //display shop reasons
        $shopReasons = new \KC\Repository\ShopReasons();
        $this->view->shopReasons = $shopReasons->getShopReasons($id);
        $this->view->ballonData = \KC\Repository\EditorBallonText::getEditorText($id);
        // display managers and account managers list
        $users = new \KC\Repository\User();
        $this->view->MangersList = $users->getManagersLists($site_name);

        // display  page's list
        $pages = new \KC\Repository\Page();
        $this->view->DefaultPagesList = $pages->defaultPagesList();


        // display affliate network's list
        $affiliate = new \KC\Repository\AffliateNetwork();
        $arr['sortBy'] = 'name';
        $affiliateNetworkList =  $affiliate->getNetworkList($arr);

        $this->view->affiliateNetworkList = $affiliateNetworkList['aaData'];
        $id = $this->getRequest()->getParam('id');
        if (intval($id) > 0) {
            $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
            $data = $queryBuilder->select('s, c, sl, bl, chapter, sp, pg, logo, af')
                ->from('KC\Entity\Shop', 's')
                ->leftJoin("s.categoryshops", "c")
                ->leftJoin("s.howtousesmallimage", "sl")
                ->leftJoin("s.howtousebigimage", "bl")
                ->leftJoin("s.howtochapter", "chapter")
                ->leftJoin("s.relatedshops", "sp")
                ->leftJoin("s.shopPage", "pg")
                ->leftJoin("s.affliatenetwork", "af")
                ->leftJoin("s.logo", "logo")
                ->where("s.id = ". $id)
                ->getQuery()
                ->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $this->view->relatedShopName = KC\Repository\Shop::getRelatedShops($data['relatedshops']);
            $this->view->data = $data ;
            $existingCategories  = $data['categoryshops'] ;
            $catArray  = array();
            foreach ($existingCategories as $categories) {
                $catArray[] = $categories['categoryId'];
            }
            $this->view->catArray = '';
            if (isset($catArray) && count($catArray) >0) {

                $this->view->catArray =  $catArray  ;
              
            }
        } else {
            $this->_helper->redirector('createshop', 'shop', 'admin');
        }

        // if request is post
        if ($this->_request->isPost()) {
            $parmas = $this->_getAllParams();
            $shop = new \KC\Repository\Shop();
            $flash = $this->_helper->getHelper('FlashMessenger');

            if ($shop->CreateNewShop($parmas, true)) {
                self::updateVarnish($id);

                $message = $this->view->translate('The shop has been updated successfully');
                $flash->addMessage(array('success' => $message));
                $this->_redirect(HTTP_PATH.'admin/shop#'.$this->getRequest()->getParam('qString'));

            } else {

                $message = $this->view->translate('Error: Your file size exceeded 2MB');
                $flash->addMessage(array('error' => $message));

                $this->_redirect(HTTP_PATH.'admin/shop#'.$this->getRequest()->getParam('qString'));

            }


        }
    }

    public function deleteshopreasonAction()
    {
        $firstFieldName = $this->getRequest()->getParam('firstFieldName');
        $secondFieldName = $this->getRequest()->getParam('secondFieldName');
        $thirdFieldName = $this->getRequest()->getParam('thirdFieldName');
        $forthFieldName = $this->getRequest()->getParam('forthFieldName');
        $shopId = $this->getRequest()->getParam('shopId');
        \KC\Repository\ShopReasons::deleteReasons($firstFieldName, $secondFieldName, $thirdFieldName, $forthFieldName, $shopId);
        $shopList = $shopId.'_list';
        $key = 'shop_sixReasons_'.$shopList;
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        exit();
    }

    public function exportshoplistAction()
    {
        //get all shop from database
        set_time_limit(10000);
        ini_set('max_execution_time', 115200);
        ini_set("memory_limit", "1024M");
        $data =  \KC\Repository\Shop::exportShopsList();
        //echo "<pre>";
        //print_r($data); die;
        //create object of phpExcel

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $this->view->translate('Shopname'));
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $this->view->translate('Navigation URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('C1', $this->view->translate('Money shop'));
        $objPHPExcel->getActiveSheet()->setCellValue('D1', $this->view->translate('Account manager'));
        $objPHPExcel->getActiveSheet()->setCellValue('E1', $this->view->translate('Start'));
        $objPHPExcel->getActiveSheet()->setCellValue('F1', $this->view->translate('Network'));
        $objPHPExcel->getActiveSheet()->setCellValue('G1', $this->view->translate('Online'));
        $objPHPExcel->getActiveSheet()->setCellValue('H1', $this->view->translate('Offline since'));
        $objPHPExcel->getActiveSheet()->setCellValue('I1', $this->view->translate('Overwrite Title'));
        $objPHPExcel->getActiveSheet()->setCellValue('J1', $this->view->translate('Meta Description'));
        $objPHPExcel->getActiveSheet()->setCellValue('K1', $this->view->translate('Allow user generated content'));
        $objPHPExcel->getActiveSheet()->setCellValue('L1', $this->view->translate('Allow Discussions'));
        $objPHPExcel->getActiveSheet()->setCellValue('M1', $this->view->translate('Title'));
        $objPHPExcel->getActiveSheet()->setCellValue('N1', $this->view->translate('Sub-title'));
        $objPHPExcel->getActiveSheet()->setCellValue('O1', $this->view->translate('Notes'));
        $objPHPExcel->getActiveSheet()->setCellValue('P1', $this->view->translate('Editor'));
        $objPHPExcel->getActiveSheet()->setCellValue('Q1', $this->view->translate('Category'));
        $objPHPExcel->getActiveSheet()->setCellValue('R1', $this->view->translate('Similar Shops'));
        $objPHPExcel->getActiveSheet()->setCellValue('S1', $this->view->translate('Deeplinking code'));
        $objPHPExcel->getActiveSheet()->setCellValue('T1', $this->view->translate('Ref URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('U1', $this->view->translate('Actual URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('V1', $this->view->translate('Shop Text'));
        $objPHPExcel->getActiveSheet()->setCellValue('W1', $this->view->translate('Days Without Online Coupons'));
        $objPHPExcel->getActiveSheet()->setCellValue('X1', $this->view->translate('No. of Times Shop became Favourite'));
        $objPHPExcel->getActiveSheet()->setCellValue('Y1', $this->view->translate('Last week Clickouts'));
        $objPHPExcel->getActiveSheet()->setCellValue('Z1', $this->view->translate('Total Clickouts'));
        $objPHPExcel->getActiveSheet()->setCellValue('AA1', $this->view->translate('Amount of Coupons'));
        $objPHPExcel->getActiveSheet()->setCellValue('AB1', $this->view->translate('Amount of Offers'));

        $column = 2;
        $row = 2;

        //loop for each shop
        foreach ($data as $shop) {

            //condition apply on affliatedprograme
            $prog = '';
            if ($shop['affliateProgram']==true) {

                $prog= $this->view->translate('Yes');
            } else {
                $prog = $this->view->translate('No');
            }

            //get account manage name from array
            $accountManagername = '';
            if (
                $shop['accountManagerName']==''
                ||$shop['accountManagerName']=='undefined'
                ||$shop['accountManagerName']==null
                ||$shop['accountManagerName']=='0') {

                $accountManagername ='';
            } else {

                $accountManagername = $shop['accountManagerName'];
            }

            //create start date format
            $startDate =  date("d-m-Y", strtotime($shop['created_at']));

            //get affilate network from array
            $affilateNetwork = '';

            if (
                $shop['affname']==null
                ||$shop['affname']==''
                ||$shop['affname']=='undefined') {

                $affilateNetwork = '';

            } else {

                $affilateNetwork = $shop['affname'];
            }


            //get offline (status of shop from array
            $offLine='';
            if ($shop['status']==true) {

                $offLine=$this->view->translate('Yes');

            } else {

                $offLine=$this->view->translate('No');
            }

            //get offline since or not from array
            $offLineSince = '';
            if (
                $shop['offlineSicne']=='undefined'
                || $shop['offlineSicne']==null
                || $shop['offlineSicne']=='') {

                $offLineSince='';

            } else {

                $offLineSince = date("d-m-Y", strtotime($shop['offlineSicne']));
            }

            $overriteTitle = '';
            if (
                $shop['overriteTitle']=='undefined'
                || $shop['overriteTitle']==null
                || $shop['overriteTitle']=='') {

                $overriteTitle='';

            } else {

                $overriteTitle = $shop['overriteTitle'];
            }

            $metaDesc = '';
            if (
                $shop['metaDescription']=='undefined'
                || $shop['metaDescription']==null
                || $shop['metaDescription']=='') {

                $metaDesc='';

            } else {

                $metaDesc = $shop['metaDescription'];
            }

            $userGenerated = '';
            if ($shop['usergenratedcontent']==true) {

                $userGenerated= $this->view->translate('Yes');
            } else {
                $userGenerated = $this->view->translate('No');
            }

            $discussion = '';
            if (
                $shop['discussions']=='undefined'
                || $shop['discussions']==null
                || $shop['discussions']=='') {

                $discussion= '';
            } else {
                $discussion = $shop['discussions'];
            }

            $title = '';
            if (
                $shop['title']=='undefined'
                || $shop['title']==null
                || $shop['title']=='') {

                $title='';

            } else {

                $title = FrontEnd_Helper_viewHelper::replaceStringVariable($shop['title']);
            }

            $subTitle = '';
            if (
                $shop['subTitle']=='undefined'
                || $shop['subTitle']==null
                || $shop['subTitle']=='') {

                $subTitle ='';

            } else {

                $subTitle = FrontEnd_Helper_viewHelper::replaceStringVariable($shop['subTitle']);
            }

            $notes = '';
            if (
                $shop['notes']=='undefined'
                || $shop['notes']==null
                || $shop['notes']=='') {

                $notes ='';

            } else {

                $notes = $shop['notes'];
            }

            $contentManagerName = '';
            if (
                $shop['contentManagerName']=='undefined'
                || $shop['contentManagerName']==null
                || $shop['contentManagerName']=='') {

                $contentManagerName ='';

            } else {

                $contentManagerName = $shop['contentManagerName'];
            }

            $categories = '';
            if (!empty($shop['category'])) {
                $prefix = '';
                foreach ($shop['category'] as $cat) {
                    $categories .= $prefix  . $cat['name'];
                    $prefix = ', ';
                }
            }

            $relatedshops = '';
            if (!empty($shop['relatedshops'])) {
                $prefix = '';
                foreach ($shop['relatedshops'] as $rShops) {
                    $relatedshops .= $prefix  . $rShops['name'];
                    $prefix = ', ';
                }
            }

            $deeplink = '';
            if (
                $shop['deepLink']=='undefined'
                || $shop['deepLink']==null
                || $shop['deepLink']=='') {

                $deeplink ='';

            } else {

                $deeplink = $shop['deepLink'];
            }

            $refUrl = '';
            if (
                $shop['refUrl']=='undefined'
                || $shop['refUrl']==null
                || $shop['refUrl']=='') {

                $refUrl ='';

            } else {

                $refUrl = $shop['refUrl'];
            }

            $actualUrl = '';
            if (
                $shop['actualUrl']=='undefined'
                || $shop['actualUrl']==null
                || $shop['actualUrl']=='') {

                $actualUrl ='';

            } else {

                $actualUrl = $shop['actualUrl'];
            }

            $shopText = '';
            if (
                $shop['shopText']=='undefined'
                || $shop['shopText']==null
                || $shop['shopText']=='') {

                $shopText ='';

            } else {

                $shopText = $shop['shopText'];
            }

            //Extra columns added to excel export
            $daysWithoutCoupon = \KC\Repository\Shop::getDaysSinceShopWithoutOnlneOffers($shop['id']);
            $timesShopFavourite = \KC\Repository\Shop::getFavouriteCountOfShop($shop['id']);
            $lastWeekClicks = \KC\Repository\ShopViewCount::getAmountClickoutOfShop($shop['id']);
            $totalClicks = \KC\Repository\ShopViewCount::getTotalAmountClicksOfShop($shop['id']);
            $totalAmountCoupons = \KC\Repository\Offer::getTotalAmountOfShopCoupons($shop['id'], 'CD');
            $totalAmountOffers = \KC\Repository\Offer::getTotalAmountOfShopCoupons($shop['id']);

            //set value in column of excel
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$column, $shop['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $shop['permaLink']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$column, $prog);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$column, $accountManagername);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$column, $startDate);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$column, $affilateNetwork);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$column, $offLine);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$column, $offLineSince);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$column, $overriteTitle);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$column, $metaDesc);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$column, $userGenerated);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$column, $discussion);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.$column, $title);
            $objPHPExcel->getActiveSheet()->setCellValue('N'.$column, $subTitle);
            $objPHPExcel->getActiveSheet()->setCellValue('O'.$column, $notes);
            $objPHPExcel->getActiveSheet()->setCellValue('P'.$column, $contentManagerName);
            $objPHPExcel->getActiveSheet()->setCellValue('Q'.$column, $categories);
            $objPHPExcel->getActiveSheet()->setCellValue('R'.$column, $relatedshops);
            $objPHPExcel->getActiveSheet()->setCellValue('S'.$column, $deeplink);
            $objPHPExcel->getActiveSheet()->setCellValue('T'.$column, $refUrl);
            $objPHPExcel->getActiveSheet()->setCellValue('U'.$column, $actualUrl);
            $objPHPExcel->getActiveSheet()->setCellValue('V'.$column, $shopText);
            $objPHPExcel->getActiveSheet()->setCellValue('W'.$column, $daysWithoutCoupon);
            $objPHPExcel->getActiveSheet()->setCellValue('X'.$column, $timesShopFavourite);
            $objPHPExcel->getActiveSheet()->setCellValue('Y'.$column, $lastWeekClicks);
            $objPHPExcel->getActiveSheet()->setCellValue('Z'.$column, $totalClicks);
            $objPHPExcel->getActiveSheet()->setCellValue('AA'.$column, $totalAmountCoupons);
            $objPHPExcel->getActiveSheet()->setCellValue('AB'.$column, $totalAmountOffers);

            //counter incriment by 1
            $column++;
            $row++;
        }

        //FORMATING OF THE EXCELL
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
                                'color' => array('argb' => '000000'),	),),);
        //HEADER COLOR

        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'V1')->applyFromArray($headerStyle);

        //SET ALIGN OF TEXT
        $objPHPExcel->getActiveSheet()->getStyle('A1:V1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:V'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        //BORDER TO CELL
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'V1')->applyFromArray($borderStyle);
        $borderColumn =  (intval($column) -1 );
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'V'.$borderColumn)->applyFromArray($borderStyle);


        //SET SIZE OF THE CELL
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(true);

        // redirect output to client browser

        $pathToFile = UPLOAD_EXCEL_PATH ;
        echo $shopFile = $pathToFile . "shopList.xlsx";
        $fileName =  $this->view->translate('shopList.xlsx');

        die ;
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($shopFile);


        die();


    }

    public function trashAction()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success']
        : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error']
        : '';

    }

    public function importshopimageAction()
    {
        $shops = \KC\Repository\Shop::getAllShopDetails();
        foreach($shops as $shopList):
        endforeach;
        //echo ROOT_PATH."excel/";
        //die;
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(ROOT_PATH."excel/shopsdata.xlsx");
        //$objWorksheet = $objPHPExcel->getActiveSheet();
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $worksheetTitle     = $worksheet->getTitle();
            $highestRow         = $worksheet->getHighestRow();
            $highestColumn      = $worksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $nrColumns = ord($highestColumn) - 64;
            //echo "<br>The worksheet ".$worksheetTitle." has ";
            //echo $nrColumns . ' columns (A-' . $highestColumn . ') ';
            //echo ' and ' . $highestRow . ' row.';
            /*for ($row = 1; $row <= $highestRow; ++ $row) {
                   //$cell = $worksheet->getCellByColumnAndRow(1,$row);
                    $val = $worksheet->getCell('A'. $row)->getValue();
                    //$val = $cell->getValue();
                    //$dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
                    echo $val; echo "<br>";


            }*/


        }
        $handle = opendir(ROOT_PATH . '/Logo/Logo');
        $path = PUBLIC_PATH . 'Logo/Logo/';
        while ($file = readdir($handle)) {
            $rootpath = ROOT_PATH . '/Logo/Logo/';

            if ($file !== '.' && $file !== '..') {
                $originalpath = $rootpath.$file;
                $thumbpath = $rootpath . "thum_large_" . $file;
                $ext = BackEnd_Helper_viewHelper :: getImageExtension($file);

                if ($ext=='jpg' || $ext == 'png' || $ext =='JPEG'|| $ext =='PNG' || $ext =='gif') {
                    BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 70, 50, $thumbpath, $ext);
                }
            }
        }
        die("Raman");


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
        $articles = \KC\Repository\Shop::deletechapters($id);
        echo Zend_Json::encode($articles);
        die;
    }

    public function updateVarnish($id)
    {
        // Add urls to refresh in Varnish
        $varnishObj = new \KC\Repository\Varnish();
        $varnishObj->addUrl(HTTP_PATH_FRONTEND);
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_nieuw'));
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_top-20'));
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_alle-winkels-09-e'));
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_alle-winkels-f-j'));
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_alle-winkels-k-o'));
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_alle-winkels-p-t'));
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_alle-winkels-u-z'));
        $varnishObj->addUrl(HTTP_PATH_FRONTEND . FrontEnd_Helper_viewHelper::__link('link_categorieen'));
        # make markplaatfeed url's get refreashed only in case of kortingscode
        if (LOCALE == '') {
            $varnishObj->addUrl(HTTP_PATH_FRONTEND  . 'marktplaatsfeed');
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'marktplaatsmobilefeed');

        }

        # get all the urls related to this shop
        $varnishUrls = \KC\Repository\Shop::getAllUrls($id);

        # check $varnishUrls has atleast one
        if (isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach ($varnishUrls as $value) {
                $varnishObj->addUrl(HTTP_PATH_FRONTEND . $value);
            }
        }
    }

    public function updateImagesAction()
    {
        \KC\Repository\Shop::updateImages();
    }

    public function importshopsAction()
    {
        ini_set('max_execution_time', 115200);
        $params = $this->_getAllParams();
        if ($this->getRequest()->isPost()) {
            if (isset($_FILES['excelFile']['name']) && @$_FILES['excelFile']['name'] != '') {
                $RouteRedirectObj = new \KC\Repository\RouteRedirect();
                $result = @$RouteRedirectObj->uploadExcel($_FILES['excelFile']['name']);
                if ($result['status'] == 200) {
                    $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                    // $queryBuilder->delete('\KC\Entity\RouteRedirect', 'rr')
                    // ->where("w.offers=" . $id)
                    // ->getQuery();
                    //Doctrine_Query::create()->delete('\KC\Entity\RouteRedirect')->execute();
                    $spl = explode('/', HTTP_PATH);
                    $path = $spl[0].'//' . $spl[2];
                    $excelFilePath = $result['path'];
                    $excelFile = $excelFilePath.$result['fileName'];
                    $handle = opendir(ROOT_PATH . 'Logo/Logo');
                    $rootpath = ROOT_PATH . 'Logo/Logo/';
                    $pathToUpload = ROOT_PATH . 'images/upload/shop/';
                    $pathUpload = 'images/upload/shop/';
                    if (!file_exists($pathToUpload)) {
                        mkdir($pathToUpload);
                    }
                    # Screen Shots
                    $siteHandle = opendir(ROOT_PATH . 'Logo/Screenshot');
                    $rootSitePath = ROOT_PATH . 'Logo/Screenshot/';
                    $pathToUploadSiteImg = ROOT_PATH . 'images/upload/screenshot/';
                    $sitePathUpload = 'images/upload/screenshot/';
                    if (!file_exists($pathToUploadSiteImg)) {
                        mkdir($pathToUploadSiteImg);
                    }
                    $image_array =  array(); // Array for all image names
                    $siteimage_array =  array(); // Array for all site image names
                    //die();
                    # Get all the images from the folder and store in an array-$image_array
                    while ($file = readdir($handle)) {
                        if ($file !== '.' && $file !== '..') {
                            $image_array[] = $file;
                        }
                    }

                    while ($fileSite = readdir($siteHandle)) {
                        if ($fileSite !== '.' && $fileSite !== '..') {
                            $siteimage_array[] = $fileSite;
                        }
                    }

                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                    $objPHPExcel = $objReader->load($excelFile);
                    $worksheet = $objPHPExcel->getActiveSheet();

                    $data =  array();
                    foreach ($worksheet->getRowIterator() as $row) {
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);

                        foreach ($cellIterator as $cell) {
                            $data[$cell->getRow()][$cell->getColumn()] = $cell->getCalculatedValue();
                        }

                        $name =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['A']);
                        $permalink =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['B']);
                        $overwriteTitle =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['C']);
                        $metaDescription =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['D']);
                        $allowUserGeneratedContent =  strtolower($data[$cell->getRow()]['E']);
                        $allowDiscussions =  strtolower($data[$cell->getRow()]['F']);
                        $title =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['G']);
                        $subTitle  =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['H']);
                        $notes  =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['I']);
                        $accountManager  =  $data[$cell->getRow()]['J'];
                        $editor =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['K']);
                        $category =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['L']);
                        $similarShops =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['M']);
                        $affiliateNetwork =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['N']);
                        $deeplinkingCode  =  strtolower($data[$cell->getRow()]['O']);
                        $refURL = BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['P']);
                        $actualURL =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['Q']);
                        $moneyShop =  $data[$cell->getRow()]['R'];
                        $logo =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['S']);
                        $websiteScreen =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['T']);
                        $shop_text = BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['U']);
                        ///$extraOptions =  strtolower($data[$cell->getRow()]['V']);
                        //$views =  $data[$cell->getRow()]['W'];
                        //$howToUseTheCode =  strtolower($data[$cell->getRow()]['X']);

                        //$deepLink =  strtolower($data[$cell->getRow()]['Y']);

                        # find by name if exist in database
                        if (!empty($name)) {
                            # FIND SHOP BY NAME FROM DATABSE
                            $query = $queryBuilder
                                ->select('s.id as shopId, o.extendedUrl')
                                ->from('KC\Entity\Shop', 's')
                                ->where('s.name = '.$name);
                            $shopList = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
                            
                            # IF SHOP EXIST IN DATABASE THEN CONTINUE TO EDIT THE SHOPS
                            if (empty($shopList)) {
                                # ADD SHOPS DATA IN DATABASE IF SHOP NOT EXIST IN DATABASE
                                if (strtolower($name)!='shop name(must be filled)') {
                                    $shopList = new \KC\Entity\Shop();
                                    $shopList->name = BackEnd_Helper_viewHelper::stripSlashesFromString($name);
                                    $shopList->status = false;
                                }
                            }
                            if (strtolower($name)!='shop name(must be filled)' && $permalink!='' && $permalink!='None') {
                                # common data for shop edit or new
                                if ($permalink!='None' && $permalink!='') {
                                    $shopList->permaLink = BackEnd_Helper_viewHelper::stripSlashesFromString($permalink);
                                }

                                if ($overwriteTitle!='None' && $overwriteTitle!='') {
                                    $shopList->overriteTitle = FrontEnd_Helper_viewHelper::replaceStringVariable(BackEnd_Helper_viewHelper::stripSlashesFromString($overwriteTitle));
                                }

                                if ($metaDescription!='None' && $metaDescription!='') {
                                    $shopList->metaDescription = BackEnd_Helper_viewHelper::stripSlashesFromString($metaDescription);
                                }

                                if ($allowUserGeneratedContent!='None' && $allowUserGeneratedContent!='') {
                                    if ($allowUserGeneratedContent=='no') {
                                        $shopList->usergenratedcontent = '0';
                                    } else {
                                        $shopList->usergenratedcontent = '1';
                                    }
                                }

                                if ($allowDiscussions!='None' && $allowDiscussions!='') {
                                    if ($allowDiscussions=='no') {
                                        $shopList->discussions = '0';
                                    } else {
                                        $shopList->discussions = '1';
                                    }
                                }

                                if ($title!='None' && $title!='') {
                                    $shopList->title = BackEnd_Helper_viewHelper::stripSlashesFromString($title);
                                }

                                if ($subTitle!='None' && $subTitle!='') {
                                    $shopList->subTitle = BackEnd_Helper_viewHelper::stripSlashesFromString($subTitle);
                                }

                                if ($notes!='None' && $notes!='') {
                                    $shopList->notes = BackEnd_Helper_viewHelper::stripSlashesFromString($notes);
                                }

                                if ($accountManager != 'None' && $accountManager != '') {
                                    $shopList->accountManagerName = $accountManager;
                                    $repo = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\User');
                                    $acName = $repo->findOneBy(array('firstName' => $accountManager));
                                    if ($acName) {
                                        $shopList->accoutManagerId = $acName->id;
                                    }
                                    # NEW USER ADD IN USER TABLE PENDING
                                }

                                if ($editor != 'None' && $editor != '') {
                                        $shopList->contentManagerName = $editor;
                                        $repo2 = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\User');
                                        $cmName = $repo2->findOneBy(array('firstName' => $editor));
                                    if ($cmName) {
                                        $shopList->contentManagerId = $cmName->id;
                                    }
                                    # NEW USER ADD IN USER TABLE PENDING
                                }

                                if ($affiliateNetwork!='None' && $affiliateNetwork!='') {
                                    $repo3 = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\AffliateNetwork');
                                    $afNetwork = $repo3->findOneBy(array('name' => $affiliateNetwork));
                                    if ($afNetwork) {
                                        $shopList->affliateNetworkId = $afNetwork->id;
                                    }
                                    # NEW AFFILATE NEWWORK SAVE IN DATABASE
                                }

                                if ($deeplinkingCode != 'None' && $deeplinkingCode != '') {
                                    if ($deeplinkingCode == 'no') {
                                        $shopList->deepLinkStatus = false;
                                    } else {
                                        $shopList->deepLinkStatus = true;
                                    }
                                    #deeplinkURL missing in excel
                                }

                                if ($refURL != 'None' && $refURL != '') {
                                    $shopList->refUrl = BackEnd_Helper_viewHelper::stripSlashesFromString($refURL);
                                }

                                if ($actualURL != 'None' && $actualURL != '') {
                                    $shopList->actualUrl = BackEnd_Helper_viewHelper::stripSlashesFromString($actualURL);
                                }

                                if ($moneyShop != 'None' && $moneyShop != '') {
                                    if ($moneyShop == 'no') {
                                        $shopList->affliateProgram = false;
                                    } else {
                                        $shopList->affliateProgram = true;
                                    }
                                }

                                $shopList->displayExtraProperties = false;
                                $shopList->views = 0;
                                $shopList->howToUse = false;
                                $shopList->deepLink = null;
                                $format = 'Y-m-j H:i:s';
                                $date = date($format);
                                $shopList->offlineSicne = $date;
                                $shopList->status = false;

                                if ($shop_text != 'None' && $shop_text != "") {
                                    $shopList->shopText = BackEnd_Helper_viewHelper::stripSlashesFromString($shop_text);
                                }

                                $key = array_search(strtolower($logo), array_map('strtolower', $image_array));

                                if ($key >= 0) {
                                    $file = BackEnd_Helper_viewHelper::stripSlashesFromString($image_array[$key]);
                                    $newName = time() . "_" . $file;
                                    $ext = BackEnd_Helper_viewHelper :: getImageExtension($file);
                                    $originalpath = $rootpath.$file;

                                    if ($ext == 'jpg' || $ext == 'JPG' || $ext == 'png' || $ext == 'PNG' || $ext == 'gif' || $ext == 'GIF' || $ext == 'JPEG'  || $ext == 'jpeg') {
                                        $thumbpath = $pathToUpload . "thum_large_" . $newName;
                                        BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 200, 150, $thumbpath, $ext);
                                        $thumbpath = $pathToUpload . "thum_small_" . $newName;
                                        BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 84, 42, $thumbpath, $ext);
                                        $thumbpath = $pathToUpload . "thum_medium_store_" . $newName;
                                        BackEnd_Helper_viewHelper::resizeImageFromFolder($originalpath, 200, 100, $thumbpath, $ext);
                                        $thumbpath = $pathToUpload . "thum_medium_" . $newName;
                                        BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);
                                        $thumbpath = $pathToUpload . "thum_big_" . $newName;
                                        BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 234, 117, $thumbpath, $ext);
                                        $thumbpath = $pathToUpload . "thum_expired_" . $newName;
                                        BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);
                                        $logo =  new \KC\Entity\Logo();
                                        $logo->ext = $ext;
                                        $logo->path = \BackEnd_Helper_viewHelper::stripSlashesFromString($pathUpload);
                                        $logo->name = \BackEnd_Helper_viewHelper::stripSlashesFromString($newName);
                                        $logo->deleted = 0;
                                        $logo->created_at = new \DateTime('now');
                                        $logo->updated_at = new \DateTime('now');
                                        \Zend_Registry::get('emLocale')->persist($logo);
                                        \Zend_Registry::get('emLocale')->flush();
                                        $shopList->logo = \Zend_Registry::get('emLocale')
                                            ->getRepository('KC\Entity\Logo')
                                            ->find($logo->id);
                                    } else {
                                        echo $logo." This is an Invalid image";
                                    }
                                }

                                $shopList->screenshotId = 1;
                                $shopList->created_at = new \DateTime('now');
                                $shopList->updated_at = new \DateTime('now');
                                \Zend_Registry::get('emLocale')->persist($shopList);
                                \Zend_Registry::get('emLocale')->flush();
                                if ($category != 'None' && $category != '') {
                                    $splitCat  = explode(',', $category);
                                    if (!empty($shopList->id)) {
                                        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                                        $query = $queryBuilder->delete('KC\Entity\RefShopCategory', 'rf')
                                            ->where("rf.shopId=" . $shopDetail['id'])
                                            ->getQuery()->execute();
                                    }
                                    foreach ($splitCat as $key => $categories) {
                                        $refShopCategory = new \KC\Entity\RefShopCategory();
                                        $refShopCategory->created_at = new \DateTime('now');
                                        $refShopCategory->updated_at = new \DateTime('now');
                                        $refShopCategory->category = \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $shopList->id);
                                        $refShopCategory->shop = \Zend_Registry::get('emLocale')->find('KC\Entity\Category', $categories);
                                        \Zend_Registry::get('emLocale')->persist($refShopCategory);
                                        \Zend_Registry::get('emLocale')->flush();
                                    }
                                    # NEW CATEGORY PENDIGN
                                }
                                $repo = \Zend_Registry::get('emLocale')->getRepository('KC\Entity\RoutePermalink');
                                $pr = $repo->findOneBy(array('permalink' => $permalink));
                                if ($pr) {
                                    $pr->permalink = BackEnd_Helper_viewHelper::stripSlashesFromString($permalink);
                                    $pr->type = 'SHP';
                                    $pr->exactlink = 'store/storedetail/id/'.$shopList->id;
                                    $pr->created_at = $pr->created_at;
                                    $pr->updated_at = new \DateTime('now');
                                } else {
                                    $pr = new \KC\Repository\RoutePermalink();
                                    $pr->permalink = BackEnd_Helper_viewHelper::stripSlashesFromString($permalink);
                                    $pr->type = 'SHP';
                                    $pr->exactlink = 'store/storedetail/id/'.$shopList->id;
                                    $pr->created_at = new \DateTime('now');
                                    $pr->updated_at = new \DateTime('now');
                                }
                                \Zend_Registry::get('emLocale')->persist($pr);
                                \Zend_Registry::get('emLocale')->flush();

                                if ($similarShops != 'None' && $similarShops != '') {

                                    $similarstoreordArray = explode(',', $similarShops);
                                    $i = 1;
                                    if (!empty($shopDetail['id'])) {
                                        $queryBuilder = \Zend_Registry::get('emLocale')->createQueryBuilder();
                                        $query = $queryBuilder->delete('KC\Entity\RefShopRelatedshop', 'rsrs')
                                            ->where("rsrs.shop=" . $shopDetail['id'])
                                            ->getQuery()->execute();
                                    }
                                    foreach ($similarstoreordArray as $shop) {
                                        if ($shop!='') {
                                            $relateshopObj = new \KC\Entity\RefShopRelatedshop();
                                            $relateshopObj->shop = \Zend_Registry::get('emLocale')
                                                ->getRepository('KC\Entity\Shop')
                                                ->find($shopList->id);
                                            $relateshopObj->relatedshopId = $shop;
                                            $relateshopObj->position = $i;
                                            $relateshopObj->created_at = new \DateTime('now');
                                            $relateshopObj->updated_at = new \DateTime('now');
                                            \Zend_Registry::get('emLocale')->persist($relateshopObj);
                                            \Zend_Registry::get('emLocale')->flush();
                                            ++$i;
                                        }
                                    }
                                    #NEW SHOPS ADD IN SHOP TABLE PENDING
                                }
                            }
                        } else {
                            $flash = $this->_helper->getHelper('FlashMessenger');
                            $message = $this->view->translate('The Shop Images Data has been imported Successfully!!');
                            $flash->addMessage(array('success' => $message));
                            $this->_redirect(HTTP_PATH . 'admin/shop');
                        }
                    }
                }
            } else {
                $flash = $this->_helper->getHelper('FlashMessenger');
                $message = $this->view->translate('Problem in your file!!');
                $flash->addMessage(array('error' => $message));
                $this->_redirect(HTTP_PATH . 'admin/shop');
            }
        }
    }

    public function emptyXlxAction()
    {
        # set fiel and its trnslattions
        $file =  APPLICATION_PATH . '/migration/empty.xlsx' ;
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

    public function localExportXlxAction()
    {
        # set fiel and its translattions
        $locale = LOCALE != "" ? "-".strtoupper(LOCALE) : "-NL";
        $file =  UPLOAD_EXCEL_PATH . 'shopList'.$locale.'.xlsx' ;
        $fileName =  $this->view->translate($file);
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        # set reponse headers and body
        $this->getResponse()
        ->setHeader('Content-Disposition', 'attachment;filename=' . basename($fileName))
        ->setHeader('Content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->setHeader('Cache-Control', 'max-age=0')
                ->setHeader('robots', 'noindex, follow')
                ->setBody(file_get_contents($fileName));
    }

    public function globalExportXlxAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $globalExportParameters = $this->_getAllParams();
        $checkPassword = \KC\Repository\GlobalExportPassword::getPasswordForExportDownloads('shopExport');
        if (isset($globalExportParameters['password']) && $globalExportParameters['password'] == $checkPassword) {
            # set fiel and its trnslattions
            $file =  APPLICATION_PATH. '/../data/excels/globalShopList.xlsx' ;
            $fileName =  $this->view->translate($file);
            # set reponse headers and body
            $this->getResponse()
                ->setHeader('Content-Disposition', 'attachment;filename=' . basename($fileName))
                ->setHeader('Content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->setHeader('Cache-Control', 'max-age=0')
                ->setBody(file_get_contents($fileName));
        }
    }

    public function globalExportXlxPasswordAction()
    {
        $this->saveGlobalExportPassword();
        $this->sendMailToSuperAdmin();
        exit();
    }

    protected function saveGlobalExportPassword()
    {
        \KC\Repository\GlobalExportPassword::savePasswordForExportDownloads('shopExport');
        $this->exportPassword = \KC\Repository\GlobalExportPassword::getPasswordForExportDownloads('shopExport');
    }

    protected function sendMailToSuperAdmin()
    {
        $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
        );
        $frontControlerObject = $application->getOption('resources');
        $this->mandrillKey = $frontControlerObject['frontController']['params']['mandrillKey'];
        $mailer  = new FrontEnd_Helper_Mailer(array('mandrillKey' => $this->mandrillKey));
        $basePath = new Zend_View();
        $basePath->setBasePath(APPLICATION_PATH . '/views/');
        $content = array(
            'name'    => 'content',
            'content' => $basePath->partial(
                'emails/exportScriptPassword.phtml',
                array(
                    'password' => $this->exportPassword
                )
            )
        );
        
        $settings = \KC\Repository\Signupmaxaccount::getAllMaxAccounts();
        $this->mandrillSenderEmailAddress = $settings[0]['emailperlocale'];
        $this->mandrillSenderName = $settings[0]['sendername'];
        $mailer->send(
            $this->mandrillSenderName,
            $this->mandrillSenderEmailAddress,
            'Arthur',
            'export@imbull.com',
            'Global Export password',
            $content,
            '',
            '',
            '',
            '',
            array(
                'exportScript' => 'yes'
            )
        );
    }
    
    public function shopstatusAction ()
    {
        $parameters = $this->_getAllParams();
        self::updateVarnish($parameters['id']);
        $ret = \KC\Repository\Shop::changeStatus($parameters);
        $offlineDate = $ret['offlineSince']->format('Y-m-d h:i:s');
        if ($ret['offlineSince'] && $ret['howToUse'] == 1) {
            $this->_helper->json(array('date' => $offlineDate, 'message'=> 1));
        } else if ($ret['offlineSince'] && $ret['howToUse'] == '') {
            $this->_helper->json(array('date'=>$offlineDate, 'message'=>0));
        } else {
            $this->_helper->json($offlineDate);
        }
    }

    public function addballontextAction()
    {
        $this->_helper->layout()->disableLayout();
        if ($this->getRequest()->getParam('partialCounter') > 0) {
            $count = $this->getRequest()->getParam('partialCounter');
            $this->view->partialCounter = $count;
        }
    }

    public function deleteballontextAction()
    {
        $textId = $this->getRequest()->getParam('id');
        if (!empty($textId)) {
            $ballonText = \KC\Repository\EditorBallonText::deletetext($textId);
            echo Zend_Json::encode($ballonText);
        }
        die;
    }
}

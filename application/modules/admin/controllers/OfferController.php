<?php
class Admin_OfferController extends Zend_Controller_Action
{

    ############################################################
    ############### REFACTORED CODE ############################
    ############################################################
    public function getofferAction()
    {
        $parameters = $this->_getAllParams();
        //echo "<pre>";print_r($parameters);die;
        $offerList = \KC\Repository\Offer::getOfferList($parameters);
        echo \Zend_Json::encode($offerList);
        die();
    }

    public function gettrashedofferAction()
    {
        $parameters = $this->_getAllParams();
        //echo "<pre>";print_r($parameters);die;
        $offerList = \KC\Repository\Offer::getTrashedOfferList($parameters);
        echo \Zend_Json::encode($offerList);
        die();
    }
    ############################################################
    ################ END REFACTORED CODE #######################
    ############################################################
    /**
     * check authentication before load the page
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
        $this->view->controllerName = $this->getRequest()
                ->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');
    }
    public function init()
    {
        /* Initialize action controller here */
    }
    /**
     * display list of offer in this view
     * @author kraj
     * @version 1.0
     */
    public function indexAction()
    {
        // action body
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
    }

    public function permanentdeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $deletePermanent = \KC\Repository\Offer::permanentDeleteOffer($id);
        die;
    }
    public function addofferAction()
    {

        $this->view->shopList =\KC\Repository\Shop::getOfferShopList();
        $this->view->catList = \KC\Repository\Category::getCategoriesInformation();
        $pageObj = new KC\Repository\Page();
        $this->view->pages = $pageObj->getPagesOffer();
        $allTiles = $this->getAllTilesForOfferAction();
        $this->view->tiles = $allTiles;
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';


    }

    public function editofferAction()
    {
        $parameters = $this->_getAllParams();
        $this->view->offerId = $parameters['id'];
        $userGeneratedOfferStatus = KC\Repository\Offer::checkUserGeneratedOffer($parameters['id']);
        if ($userGeneratedOfferStatus) {
            $this->view->offerController = 1;
        }
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        $offerId = $parameters['id'];
        $shopImageOfOffer = new KC\Repository\Offer();
        $shop = $shopImageOfOffer::getOfferShopDetail($offerId);
        $this->view->offerShopLogo = $shop;
        $shopObject = new KC\Repository\Shop();
        $this->view->shopList = $shopObject->getOfferShopList();
        $categoryObject = new KC\Repository\Category();
        $this->view->categoryList = $categoryObject->getCategoriesInformation();
        $pageObject = new KC\Repository\Page();
        $this->view->pages = $pageObject->getPagesOffer();
        $allTiles = $this->getAllTilesForOfferAction();
        $this->view->tiles = $allTiles;
    }


    public function updateofferAction()
    {

        $parameters = $this->_getAllParams();

        $offer =\Zend_Registry::get('emLocale')->find('KC\Entity\Offer', $parameters['offerId']);
        $offerRepository = new KC\Repository\Offer();
        $offerUpdated = $offerRepository->updateOffer($parameters);
        if ($parameters['approveSocialCode'] == 1) {
            KC\Repository\UserGeneratedOffer::saveApprovedStatus($parameters['offerId'], $parameters['approveSocialCode']);
        }
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        if ($offerUpdated['result']) {
            self::updateVarnish($parameters['offerId']);
            $message = $this->view->translate('Offer has been updated successfully.');
            $flashMessage->addMessage(array('success' => $message ));
        } else {
            $message = $this->view->translate('Error: Your file size exceeded 2MB');
            $flashMessage->addMessage(array('error' => $message ));
        }
        if ($parameters['approveSocialCode'] == 1) {
            $this->_redirect(HTTP_PATH.'admin/usergeneratedoffer#'.$parameters['qString']);
        } else {
            $this->_redirect(HTTP_PATH.'admin/offer#'.$parameters['qString']);
        }
        die;
    }


    public function shopdetailAction()
    {
        $params = $this->_getAllParams();
        $shopObj = new \KC\Repository\Shop();
        $Getshopdetails = $shopObj->getShopDetail($params['shopId']);
        //echo "<pre>";print_r($Getshopdetails);die;
        $details = Zend_Json::encode($Getshopdetails);

        echo $details;
        die;
    }

    public function favouriteshopdetailAction()
    {
        $params = $this->_getAllParams();
        $favoriteShop = new \KC\Repository\FavoriteShop();
        $getVisitorsCount = $favoriteShop->getVisitorsCountByFavoriteShopId($params['shopId']);
        echo $getVisitorsCount;
        die;
    }

    public function saveofferAction()
    {
        $params = $this->_getAllParams();
        $offerObj = new \KC\Repository\Offer();
        $offer = $offerObj->saveOffer($params);

        $flash = $this->_helper->getHelper('FlashMessenger');
        if ($offer['result']) {

            $type = isset($offer['errType']) ?  $offer['errType'] : "" ;

            # return appropriate message to user
            switch ($type) {
                case 'shop':

                    $message = $this->view->translate('Please select a shop');
                    $flash->addMessage(array('error' => $message ));

                break;
                default:

                    self::updateVarnish($offer['ofer_id']);


                    $message = $this->view->translate('Offer has been added successfully.');

                    if (filter_var($params['saveAndAddnew'], FILTER_VALIDATE_BOOLEAN)) {
                        $message = $this->view->translate('Offer has been added successfully and add new offer again');
                    }

                    $flash->addMessage(array('success' => $message ));

            }

        } else {
            $message = $this->view->translate('Error: Your file size exceeded 2MB');
            $flash->addMessage(array('error' => $message ));
        }

        if (filter_var($params['saveAndAddnew'], FILTER_VALIDATE_BOOLEAN)) {
            $this->_redirect(HTTP_PATH.'admin/offer/addoffer');

        } else {
            $this->_redirect(HTTP_PATH.'admin/offer');
        }

    }

    /**
     * record move in trash
     * @author kraj
     * @version 1.0
     */
    public function movetotrashAction()
    {
            $id = $this->getRequest()->getParam('id');

            self::updateVarnish($id);

            //cal to moveToTrash function from offer model class
            $trash = \KC\Repository\Offer::moveToTrash($id);

            if (intval($trash) > 0) {

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

/**
     * search to five shop from database by flag
     * flag (1 deleted  or 0 not deleted )
     * @author Er.kundal
     * @version 1.0
     */
    public function searchtopfiveshopAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = $this->getRequest()->getParam('flag');
        //cal to searchToFiveShop function from offer model class
        $data = \KC\Repository\Offer::searchToFiveShop($srh, $flag);


        $ar = array();
        //$removeDup = array();
        if (sizeof($data) > 0) {
            //$ar[] = $srh;
            foreach ($data as $d) {

                $id =  $d['id'];
                //array fro remove duplicate search text
                $ar[] = ucfirst($d['name']);

            }

        } else {

            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }



        echo Zend_Json::encode($ar);
        die;

        // action body
    }


    /**
     * search to five Coupon from database by flag
     * flag (1 deleted  or 0 not deleted )
     * @author Amit Sharma
     * @version 1.0
     */
    public function searchtopfivecouponAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = $this->getRequest()->getParam('flag');
        //cal to searchToFiveShop function from offer model class
        $data = \KC\Repository\Offer::searchToFiveCoupon($srh, $flag);
        //echo "<pre>";print_r($data);die;

        $ar = array();
        //$removeDup = array();
        if (sizeof($data) > 0) {
            //$ar[] = $srh;
            foreach ($data as $d) {

                $id =  $d['id'];
                //array fro remove duplicate search text
                $ar[] = $d['couponCode'];

            }

        } else {

            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }

        echo Zend_Json::encode($ar);
        die;

        // action body
    }


    /**
     * search to five offer from database by flag
     * flag (1 deleted  or 0 not deleted )
     * @author Er.kundal
     * @version 1.0
     */
    public function searchtopfiveofferAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = $this->getRequest()->getParam('flag');

        //cal to searchToFiveShop function from offer model class
        $data = \KC\Repository\Offer::searchTopFiveOffer($srh, $flag);

        $ar = array();
        $removeDup = array();
        if (sizeof($data) > 0) {
        //	$ar[] = $srh;
            foreach ($data as $d) {

                $id =  $d['id'];
                //array fro remove duplicate search text
                if (isset($removeDup[$id])) {
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

        // action body
    }


    /**
     * Export offer list in excell file
     * @author kraj
     * @version 1.0
     */
    public function exportofferlistAction()
    {
        set_time_limit(10000);
        ini_set('max_execution_time', 115200);
        ini_set("memory_limit", "1024M");

        // get all shop from database
        $data = Offer::exportofferList();

        //echo "<pre>"; print_r($data); die;
        // create object of phpExcel
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $this->view->translate('Title'));
        $objPHPExcel->getActiveSheet()->setCellValue('B1', $this->view->translate('Shop'));
        $objPHPExcel->getActiveSheet()->setCellValue('C1', $this->view->translate('Type'));
        $objPHPExcel->getActiveSheet()->setCellValue('D1', $this->view->translate('Visibility'));
        $objPHPExcel->getActiveSheet()->setCellValue('E1', $this->view->translate('Extended'));
        $objPHPExcel->getActiveSheet()->setCellValue('F1', $this->view->translate('Start'));
        $objPHPExcel->getActiveSheet()->setCellValue('G1', $this->view->translate('End'));
        $objPHPExcel->getActiveSheet()->setCellValue('H1', $this->view->translate('Clickouts'));
        $objPHPExcel->getActiveSheet()->setCellValue('I1', $this->view->translate('Author'));
        $objPHPExcel->getActiveSheet()->setCellValue('J1', $this->view->translate('Coupon Code'));
        $objPHPExcel->getActiveSheet()->setCellValue('K1', $this->view->translate('Ref URL'));
        $objPHPExcel->getActiveSheet()->setCellValue('L1', $this->view->translate('Exclusive'));
        $objPHPExcel->getActiveSheet()->setCellValue('M1', $this->view->translate('Editor Picks'));
        $objPHPExcel->getActiveSheet()->setCellValue('N1', $this->view->translate('User Generated'));
        $objPHPExcel->getActiveSheet()->setCellValue('O1', $this->view->translate('Approved'));
        $objPHPExcel->getActiveSheet()->setCellValue('P1', $this->view->translate('Offline'));
        $objPHPExcel->getActiveSheet()->setCellValue('Q1', $this->view->translate('Created At'));
        $objPHPExcel->getActiveSheet()->setCellValue('R1', $this->view->translate('Deeplink'));
        $objPHPExcel->getActiveSheet()->setCellValue('S1', $this->view->translate('Terms & Conditions'));

        $column = 2;
        $row = 2;

        // loop for each offer
        foreach ($data as $offer) {

            // condition apply on offer
            $title = '';
            if ($offer['title'] == '' || $offer['title'] == 'undefined'
                    || $offer['title'] == null || $offer['title'] == '0') {

                $title = '';

            } else {

                $title = $offer['title'];
            }
            $shopname = '';
            if (isset($offer['shop'])) {

                if ($offer['shop']['name'] == ''
                        || $offer['shop']['name'] == 'undefined'
                        || $offer['shop']['name'] == null
                        || $offer['shop']['name'] == '0') {

                    $shopname = '';

                } else {

                    $shopname = $offer['shop']['name'];
                }
            }
            $type = '';
            if ($offer['discountType'] == 'CD') {

                $type = $this->view->translate('Coupon');

            } elseif ($offer['discountType'] == 'SL') {

                $type = $this->view->translate('Sale');

            } else {

                $type = $this->view->translate('Printable');
            }
            // get visability name from array
            $Visability = '';
            if ($offer['Visability'] == 'DE') {

                $Visability = $this->view->translate('Default');

            } else {

                $Visability = $this->view->translate('Members');
            }

            // get extended from array
            $Extended = '';
            if ($offer['extendedOffer'] == true) {

                $Extended = $this->view->translate('Yes');

            } else {

                $Extended = $this->view->translate('No');
            }

            // create start date format
            $startDate = date("d-m-Y", strtotime($offer['startDate']));
            // end date format
            $endDate = date("d-m-Y", strtotime($offer['endDate']));
            // get Clickouts from array
            $Clickouts = $offer['Count'];
            // get Author from array
            $Author = '';
            if (isset($offer['authorName'])) {

                $Author = $offer['authorName'];

            } else {

                $Author = '';
            }

            $code = '';
            if ($offer['couponCode'] == '' || $offer['couponCode'] == 'undefined'
                    || $offer['couponCode'] == null) {

                $code = '';

            } else {

                $code = $offer['couponCode'];
            }

            $refUrl = '';
            if ($offer['refURL'] == '' || $offer['refURL'] == 'undefined'
                    || $offer['refURL'] == null) {

                $refUrl = '';

            } else {

                $refUrl = $offer['refURL'];
            }

            $exclusive = '';
            if ($offer['exclusiveCode'] == true) {

                $exclusive = $this->view->translate('Yes');

            } else {

                $exclusive = $this->view->translate('No');
            }

            $editor = '';
            if ($offer['editorPicks'] == true) {

                $editor = $this->view->translate('Yes');

            } else {

                $editor = $this->view->translate('No');
            }

            $usergenerated = '';
            if ($offer['userGenerated'] == true) {

                $usergenerated = $this->view->translate('Yes');

            } else {

                $usergenerated = $this->view->translate('No');
            }

            $approved = '';
            if ($offer['approved'] == true) {

                $approved = $this->view->translate('Yes');

            } else {

                $approved = $this->view->translate('No');
            }

            $offline = '';
            if ($offer['offline'] == true) {

                $offline = $this->view->translate('Yes');

            } else {

                $offline = $this->view->translate('No');
            }

            $created_at = '';
            if ($offer['created_at'] == '' || $offer['created_at'] == 'undefined'
                    || $offer['created_at'] == null) {

                $created_at = '';

            } else {

                $created_at = date("d-m-Y", strtotime($offer['created_at']));
            }

            $deeplink = '';
            if ($offer['shop']['deepLink'] == '' || $offer['shop']['deepLink'] == 'undefined'
                    || $offer['shop']['deepLink'] == null) {

                $deeplink = '';

            } else {

                $deeplink = $offer['shop']['deepLink'];
            }

            $terms = '';
            if (@$offer['termandcondition'][0]['content'] == '' || @$offer['termandcondition'][0]['content'] == 'undefined'
                    || @$offer['termandcondition'][0]['content'] == null) {

                $terms = '';

            } else {

                $terms = @$offer['termandcondition'][0]['content'];
            }

            // set value in column of excel
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $column, $title);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $column, $shopname);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $column, $type);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $column, $Visability);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $column, $Extended);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $column, $startDate);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $column, $endDate);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $column, $Clickouts);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $column, $Author);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $column, $code);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $column, $refUrl);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $column, $exclusive);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $column, $editor);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $column, $usergenerated);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $column, $approved);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $column, $offline);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $column, $created_at);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $column, $deeplink);
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $column, $terms);

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

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'S1')->applyFromArray($headerStyle);

        // SET ALIGN OF TEXT
        $objPHPExcel->getActiveSheet()->getStyle('A1:S1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:S' . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        // BORDER TO CELL
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'S1')->applyFromArray($borderStyle);
        $borderColumn = (intval($column) - 1);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'S' . $borderColumn)->applyFromArray($borderStyle);

        // SET SIZE OF THE CELL
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

        // redirect output to client browser
        $fileName =  $this->view->translate('offerList.xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$fileName);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        die();

    }
    /**
     * get trashed offer from database
     * @author kraj
     * @version 1.0
     */
    public function trashAction()
    {
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';

    }
    /**
     * delete offer from database by id
     * @author kraj
     * @version 1.0
     */
    public function deleteofferAction()
    {
        $id = $this->getRequest()->getParam('id');

        //cal to deleteOffer function from offer model class
        $deletePermanent = \KC\Repository\Offer::deleteOffer($id);

        $flash = $this->_helper->getHelper('FlashMessenger');
        if (intval($deletePermanent) > 0) {
            $message = $this->view
                    ->translate('Record has been deleted successfully.');
            $flash->addMessage(array('success' => $message));

        } else {

            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
        }
    //	echo Zend_Json::encode($deletePermanent);
        die;
    }
    /**
     * restore offer by id
     * @author kraj
     * @version 1.0
     */
    public function restoreofferAction()
    {
        $id = $this->getRequest()->getParam('id');

        self::updateVarnish($id);

        //cal to restoreOffer function from offer model class
        $restore = \KC\Repository\Offer::restoreOffer($id);

        if (intval($restore) > 0) {

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

    public function offerdetailAction()
    {
            $id = $this->getRequest()->getParam('offerId');
            $offerObj = new KC\Repository\Offer();
            $offerDetail = $offerObj->getOfferDetail($id);
            echo Zend_Json::encode($offerDetail);
            die;
    }

    public function addmorenewsAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_getAllParams();

        if (isset($params['secret']) && @base64_decode($params['secret']) == "kortingscodeoffernews" && isset($params['secret']) && @$params['jcode'] == "passwordmd5") {

            $morenews = '<div class="clear line"></div><div class="mainpage-content-right-inner-left"><label><strong>Title</strong></label></div>
                              <div class="mainpage-content-right-inner-right-other"></div>
                                <div class="mainpage-content-right-inner-right-full">
                                <input type="text" name="newsTitle[]" placeholder="News title" class="span3">
                          </div>

                          <div class="mainpage-content-right-inner-left"><label><strong>Ref URL</strong></label></div>
                              <div class="mainpage-content-right-inner-right-other"></div>
                                <div class="mainpage-content-right-inner-right-full">
                                <input type="text" name="newsrefUrl[]" id="newsrefUrl" disabled="disabled" placeholder="Ref.Url" class="span3 ignore" style="width:330px !important;">&nbsp;&nbsp;&nbsp;
                                Deeplinking is
                        <button class="btn" id="newsdeepLinkOnbtn" name="newsdeepLinkOnbtn" type="button" onclick="newschangelinkStatus(this)">On</button>
                        <button style="border-radius: 0 4px 4px 0;" name="newsdeepLinkoofbtn" onclick="newschangelinkStatus(this)" class="btn mr10 btn-primary" id="newsdeepLinkoofbtn" type="button">Off</button>
                        <input type="checkbox" style="display:none;" id="newsdeepLinkStatus" value="1" name="newsdeepLinkStatus[]">
                          </div>

                         <div class="mainpage-content-right-inner-left"><label><strong>Description</strong></label></div>
                              <div class="mainpage-content-right-inner-right-other"></div>
                                <div class="mainpage-content-right-inner-right-full">
                                <textarea rows="4" cols="3" name="newsDescription[]" id="newsDescription"></textarea>
                          </div>';
            echo $morenews;
            exit();
        }

    }


    /**
     * validate permalink
     *
     * @version 1.0
     * @author kkumar
     */
    public function validatepermalinkAction()
    {
        $url = $this->getRequest ()->getParam ( "extendedOfferRefurl" );
        $id = $this->getRequest()->getParam("id") ;

        $pattern = array ('/\s/',"/[\,+@#$%'^&*!]+/");
        $replace = array ("-","-");
        $url = preg_replace ( $pattern, $replace, $url );

        $url = strtolower($url);

        $rp = KC\Repository\Offer::getExtendedUrl($url);

        if($id != '') {


            if(@$rp[0]['extendedUrl'] == $url ) {
                if( @$rp[0]['id'] == $id ){
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

            if(@$rp[0]['extendedUrl'] != $url ) {
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
     * Save offer tiles
     * @author blal
     */
    public function addoffertileAction()
    {
        $params = $this->_getAllParams();
        $imgext = \BackEnd_Helper_viewHelper::getImageExtension(@$params['hidimage']);
        //if(isset($params['position']) && $params['position']!=""){
            $offerTile = \KC\Repository\OfferTiles::addOfferTile($params,$imgext);
            $newArr= array();
            $newArr['imagename'] = $params['hidimage'];
            $newArr['imagepath'] = PUBLIC_PATH_LOCALE."images/upload/offertiles/thum_small_".$params['hidimage'];
            $newArr['imgId'] = $offerTile;
            $newArr['label'] = $params['label'];
            $newArr['type'] = $params['hidtype'];
            echo Zend_Json::encode($newArr);
            die();
         //}

    }
    public function onfileselectAction()
    {
        if($_FILES) {
            //Zend_Debug::dump($_FILES);
            $temp = array();
            if(isset($_FILES['tileupload']['name']) && $_FILES['tileupload']['name']!=''){
                    //$data = new Offer();
                    $temp['type'] = $_FILES['tileupload']['type'][0];
                    $temp['path'] = $_FILES['tileupload']['tmp_name'][0];

                    $fileName = \KC\Repository\Offer::uploadTiles($_FILES['tileupload']['name'][0]);

                    $temp['name'] = $fileName;

                }
                echo Zend_Json::encode($temp);
        }
        die;
    }

    public function deletemenuAction()
    {
        //echo "hello"; die;
        $params = $this->_getAllParams();
        //print_r($params); die;
        $menu = \KC\Repository\OfferTiles::deleteMenuRecord($params);
        echo Zend_Json::encode($menu);
        die();
    }
    public function getilebyidAction()
    {
        $id  = $this->getRequest()->getParam('id');
        $offerTiles = \KC\Repository\OfferTiles::getOfferTilesList($id);
        echo Zend_Json::encode($offerTiles);
        die();

    }
    public function getalltilesAction()
    {
        $Tiles = \KC\Repository\OfferTiles::getAllTiles();
        echo Zend_Json::encode($Tiles);
        die;
        //return $Tiles;
    }

    public function getAllTilesForOfferAction()
    {
        $Tiles = \KC\Repository\OfferTiles::getAllTiles();
        //echo Zend_Json::encode($Tiles);
        //die;
        return $Tiles;
    }

    public static function importimagesAction()
    {
        $handle = opendir(ROOT_PATH . '/Logo/Logo');
        $rootpath = ROOT_PATH . '/Logo/Logo/';
        $pathToUpload = ROOT_PATH . '/images/upload/shop/';
        $pathUpload = '/images/upload/shop/';

        //Screen Shots
        $siteHandle = opendir(ROOT_PATH . '/Logo/Screenshot');
        $rootSitePath = ROOT_PATH . '/Logo/Screenshot/';
        $pathToUploadSiteImg = ROOT_PATH . '/images/upload/screenshot/';
        $sitePathUpload = '/images/upload/screenshot/';



        $image_array =  array(); // Array for all image names
        $siteimage_array =  array(); // Array for all site image names

        // Get all the images from the folder and store in an array-$image_array
        while ($file = readdir($handle)){
            if ($file !== '.' && $file !== '..'){

                $image_array[] = $file;

            }
        }

        while($fileSite = readdir($siteHandle)){
            if($fileSite !== '.' && $fileSite !== '..'){

                $siteimage_array[] = $fileSite;

            }
        }


        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load(ROOT_PATH."/shopsdata.xlsx");

        $data =  array();
        $worksheet = $objPHPExcel->getActiveSheet();

        foreach ($worksheet->getRowIterator() as $row) {

            $i=  0;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            foreach ($cellIterator as $cell) {
                //$data[2]['A'] = $cell->getValue();
                $data[$cell->getRow()][$cell->getColumn()] = $cell->getValue();

            }

            $name =  $data[$cell->getRow()]['A'];
            $logo =  $data[$cell->getRow()]['B'];
            $websiteScreen =  $data[$cell->getRow()]['C'];
            $shop_text = $data[$cell->getRow()]['D'];
            $freeDel = $data[$cell->getRow()]['E'];
            $delCost = $data[$cell->getRow()]['F'];
            $returnPol = $data[$cell->getRow()]['G'];
            $delTime = $data[$cell->getRow()]['H'];

            //find by name if exist in database
            if (!empty($name)) {

                $shopList = Doctrine_Core::getTable('Shop')->findOneBy('name', $name);

                if(!empty($shopList)){


                    if($shop_text != ""){
                        $shopList->shopText = $shop_text;
                    }else{
                        //echo "lege desc voor ".$shopList['id']."\r\n";
                        //echo $shop_text."\n\r";
                    }
                    if($freeDel == 0 || $freeDel=='0'||$freeDel == 1||$freeDel == '1'){

                        $shopList->freeDelivery = intval($freeDel);
                        $shopList->deliveryCost = $delCost;

                    }else {

                        $shopList->freeDelivery = intval($freeDel);
                        $shopList->deliveryCost = " ";

                    }

                    if($returnPol != " "){
                        $shopList->returnPolicy=$returnPol;
                    }

                    if($returnPol != " "){
                        $shopList->Deliverytime= $delTime;
                    }



                    $key = array_search(strtolower($logo), array_map('strtolower', $image_array));


                    if(!empty($key)){

                        $file = $image_array[$key];
                        $newName = time() . "_" . $file;

                        $ext = BackEnd_Helper_viewHelper :: getImageExtension($file);
                        $originalpath = $rootpath.$file;

                        if($ext=='jpg' || $ext == 'png' || $ext =='JPEG'|| $ext =='PNG' || $ext =='gif'){


                            $thumbpath = $pathToUpload . "thum_large_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 200, 150, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_small_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 84, 42, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_medium_store_" . $newName;
                            \BackEnd_Helper_viewHelper::resizeImageFromFolder($originalpath, 200, 100, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_medium_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_big_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 234, 117, $thumbpath, $ext);

                            $thumbpath = $pathToUpload . "thum_expired_" . $newName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 100, 50, $thumbpath, $ext);

                            $shopList->logo->ext = $ext;
                            $shopList->logo->path = $pathUpload;
                            $shopList->logo->name = $newName;

                        } else{
                            echo $logo." This is an Invalid image";
                        }
                    }

                    //Website Screen shots

                    $keySite = array_search(strtolower($websiteScreen), array_map('strtolower', $siteimage_array));
                    if(!empty($keySite)){

                        $sitefile = $siteimage_array[$keySite];
                        $sitenewName = time() . "_" . $sitefile;

                        $siteExt = \BackEnd_Helper_viewHelper::getImageExtension($sitefile);
                        $originalpath = $rootSitePath.$sitefile;

                        if($siteExt=='jpg' || $siteExt == 'png' || $siteExt =='JPEG'|| $siteExt =='PNG' || $siteExt =='gif'){

                            $thumbpath = $pathToUploadSiteImg . "thum_large_" . $sitenewName;
                            BackEnd_Helper_viewHelper :: resizeImageFromFolder($originalpath, 450,0, $thumbpath, $siteExt);
                            $shopList->screenshot->ext = $siteExt;
                            $shopList->screenshot->path = $sitePathUpload;
                            $shopList->screenshot->name = $sitenewName;

                        } else{
                            echo $websiteScreen." This is an Invalid image";
                        }
                    }

                    $shopList->save();

                }
            } else {
                echo "The Shop Images Data has been imported Successfully!!";
                exit;
            }

        }
    }


    /**
     *  updateVarnish
     *
     *  update varnish table when an offer is created , updated and deleted
     *  @param integer $id offer id
     */
    public function updateVarnish($id)
    {
        // Add urls to refresh in Varnish
        $varnishObj = new \KC\Repository\Varnish();
        # get all the urls related to an offer
        $varnishUrls = \KC\Repository\Offer::getAllUrls($id);
        $varnishRefreshTime = (array) $varnishUrls['refreshTime'];
        $refreshTime = FrontEnd_Helper_viewHelper::convertOfferTimeToServerTime($varnishRefreshTime['date']);
        # check $varnishUrls has atleast one url
        if (isset($varnishUrls) && count($varnishUrls) > 0) {
            foreach ($varnishUrls as $varnishIndex => $varnishUrl) {
                if ($varnishIndex != 'refreshTime') {
                    $varnishObj->addUrl(HTTP_PATH_FRONTEND . $varnishUrl, $refreshTime);
                }
            }
        }
        $varnishObj->addUrl(HTTP_PATH_FRONTEND, $refreshTime);
        $varnishObj->addUrl(
            HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_nieuw'),
            $refreshTime
        );
        $varnishObj->addUrl(
            HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_top-20'),
            $refreshTime
        );
        $varnishObj->addUrl(
            HTTP_PATH_FRONTEND . \FrontEnd_Helper_viewHelper::__link('link_categorieen'),
            $refreshTime
        );
        $varnishObj->addUrl("http://www.flipit.com", $refreshTime);
        # make markplaatfeed url's get refreashed only in case of kortingscode
        if (LOCALE == '') {
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'marktplaatsfeed', $refreshTime);
            $varnishObj->addUrl(HTTP_PATH_FRONTEND. 'marktplaatsmobilefeed', $refreshTime);
        }
    }

    /**
     * validate url
     * @author Raman
     * @version 1.0
     */
    public function validateextendedurlAction()
    {
        $url = $this->getRequest()->getParam("shopNavUrl") ;
        $isEdit = $this->getRequest()->getParam("isEdit") ;


        $pattern = array ('/\s/',"/[\,+@#$%'^&*!]+/");

        $replace = array ("-","-");
        $url = preg_replace ( $pattern, $replace, $url );
        $url = strtolower($url);
        $rp = Doctrine_Query::create()->select()->from("RoutePermalink")->where("permalink = '".urlencode($url)."'")->fetchArray();

        if ($isEdit) {
            $exactLink = "store/storedetail/id/".$this->getRequest()->getParam("id") ;

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
            /* */
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
        echo Zend_Json::encode( $res ) ;

        die();
    }


    /**
     * emptyXlx
     *
     * used to download empty xlsx file for shop imports
     * @author Surinderpal Singh
     */
    public function emptyXlxAction()
    {
        # set fiel and its trnslattions
        $file =  APPLICATION_PATH . '/migration/emptycouponcode.xlsx' ;
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

    public function exportcodelistAction()
    {

        $id = $this->getRequest()->getParam('id' , false);


        if($id) {

            //get all shop from database
            set_time_limit ( 10000 );
            ini_set('max_execution_time',115200);
            ini_set("memory_limit","1024M");
            $data =  \KC\Repository\CouponCode::exportCodeList($id);


            //create object of phpExcel

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Code');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Status');

            $column = 2;
            $row = 2;



            //loop for each shop
            foreach ($data as $code) {


                $status = $code['status'] == 1 ? 'Available' : 'Used';

                //set value in column of excel
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$column, $code['code']);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $status);

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

            $objPHPExcel->getActiveSheet()->getStyle('A1:'.'B1')->applyFromArray($headerStyle);

            //SET ALIGN OF TEXT
            $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('B2:B'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

            //BORDER TO CELL
            $objPHPExcel->getActiveSheet()->getStyle('A1:'.'B1')->applyFromArray($borderStyle);
            $borderColumn =  (intval($column) -1 );
            $objPHPExcel->getActiveSheet()->getStyle('A1:'.'B'.$borderColumn)->applyFromArray($borderStyle);


            //SET SIZE OF THE CELL
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);


            // redirect output to client browser

            $pathToFile = ROOT_PATH;

            $fileName =  $this->view->translate('CouponCodeList.xlsx');

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename='.$fileName);
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            die();


        } else {

            $this->_helper->redirector('index', 'offer', 'admin');
        }


    }

    /**
     * import excel
     *
     * used to import code for an  offer
     * @author Surinderpal Singh
     */
    public function importcodesAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        # set memory limit to avoid memory allocation error
        ini_set('max_execution_time', 115200);


        $errorFlag = true ;
        # check request is post
        if ($this->getRequest ()->isPost ()) {

            # validate filename
            if (isset($_FILES['importCodes']['name']) && @$_FILES['importCodes']['name'] != '') {

                try {


                        # upload file to excel upload folder
                        $RouteRedirectObj = new \KC\Repository\RouteRedirect();
                        $result = @$RouteRedirectObj->uploadExcel($_FILES['importCodes']['name']);


                        # check file is uploaded or not
                        if($result['status'] == 200){


                            # cretae path for uploaded file
                            $spl = explode('/', HTTP_PATH);
                            $path = $spl[0].'//' . $spl[2];
                            $excelFilePath = $result['path'];
                            $excelFile = $excelFilePath.$result['fileName'];

                            # read excel file
                            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                            $objPHPExcel = $objReader->load($excelFile);
                            $worksheet = $objPHPExcel->getActiveSheet();


                            unlink($excelFile);
                            $data =  array();

                            $i = 0 ;


                            $codesArray = array();


                            # traverse the excel file to update codes
                            foreach ($worksheet->getRowIterator() as $row) {

                                $cellIterator = $row->getCellIterator();
                                $cellIterator->setIterateOnlyExistingCells(false);

                                # irrerate through a row
                                foreach ($cellIterator as $cell) {

                                    $data[$cell->getRow()][$cell->getColumn()] = $cell->getCalculatedValue();
                                }


                                # set code and its status to appropriate variables
                                $code =  $data[$cell->getRow()]['A'];
                                $status =  $data[$cell->getRow()]['B'];

                                # skip first row of headers
                                if($i == 0) {
                                    # if validate excel sheet is correct according couponcodesx
                                    if(strtolower( $code) == 'code' && strtolower( $status) == 'status') {
                                        $errorFlag = false ;

                                    }

                                    $i++ ;
                                    continue ;

                                }






                                if(! $errorFlag ){


                                    # check code esists or not
                                    if(!empty($code) ) {
                                        $codesArray[] = $code;
                                            $offerId = $this->getRequest()->getParam('offer' , null);

                                            # FIND Code BY Offer id and code
                                            $codeRecord = Doctrine::getTable('CouponCode')
                                                    ->createQuery()
                                                    ->where("code = '" . $code ."'")
                                                    ->andWhere('offerid ='.  $offerId )
                                                    ->limit(1)
                                                    ->fetchArray();

                                            # if record found then simply update its ttaus
                                            if($codeRecord) {

                                                $newStaus = 0 ;


                                                if(strtolower($status) == 'available' ) {
                                                    $newStaus = 1 ;
                                                }

                                                Doctrine_Query::create()->update('CouponCode')
                                                    ->set('status',  $newStaus )
                                                    ->where("code = '" . $code ."'")
                                                    ->andWhere('offerid ='.  $offerId)
                                                    ->execute();


                                            } else {

                                                # add new code with its status
                                                $newStaus = 0 ;


                                                if(strtolower($status) == 'available' ) {
                                                    $newStaus = 1 ;
                                                }


                                                $couponCode = new CouponCode();
                                                $couponCode->code = $code;
                                                $couponCode->status = $newStaus;
                                                $couponCode->offerid = $offerId;
                                                $couponCode->save();
                                                $couponCode->free(true);


                                            }



                                    }

                                }else {


                                    # display errors
                                    $message = $this->view->translate ('Problem in your file!!');
                                    echo Zend_Json::encode( array('status' => '100' , 'message' => $message));
                                    die;
                                }
                            }



                            $d3 = Doctrine_Query::create()->delete()
                            ->from('CouponCode')
                            ->where("offerid =" . $offerId)
                            ->andWhereNotIn("code",$codesArray)
                            ->execute();

                            \KC\Repository\Offer::updateCache($offerId);

                            self::updateVarnish($offerId);

                            $codesDetail = \KC\Repository\CouponCode::returnCodesDetail($offerId);
                            $codesDetail['status'] = '200' ;
                            $codesDetail['message'] = $this->view->translate ('Codes have been imported successfully');

                            echo Zend_Json::encode($codesDetail);

                            die;


                        } else {


                            # display errors
                            $message = $this->view->translate ('Problem in your file!!');
                            echo Zend_Json::encode( array('status' => '100' , 'message' => $message));


                        }


                } catch (Exception $e) {


                    # display errors

                    $message = $this->view->translate ('Problem in your file!!!');
                    echo Zend_Json::encode( array('status' => '100' , 'message' => $message));

                    die;
                }
            }
        }


        # display errors
        $message = $this->view->translate ('Problem in your file!!');
        echo Zend_Json::encode( array('status' => '100' , 'message' => $message));


    }


    /**
     * exportXlx
     *
     * used to download offer export xlsx file of current locale
     * @author Surinderpal Singh
     */
    public function exportXlxAction()
    {
        # set fiel and its translattions
        $locale = LOCALE != "" ? "-".strtoupper(LOCALE) : "-NL";
        $file =  UPLOAD_EXCEL_PATH . 'offerList'.$locale.'.xlsx' ;
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

    public function importoffersAction()
    {
        $flashMessage = $this->_helper->getHelper('FlashMessenger');
        ini_set('max_execution_time', 115200);
        $params = $this->_getAllParams();
        if ($this->getRequest()->isPost()) {
            if (isset($_FILES['excelFile']['name']) && $_FILES['excelFile']['name'] != '') {
                $uploadResult = BackEnd_Helper_viewHelper::uploadExcel($_FILES['excelFile']['name'], false, 'offer');
                if ($uploadResult['status'] == 200) {
                    $excelFilePath = $uploadResult['path'];
                    $excelFile = $excelFilePath.$uploadResult['fileName'];
                    $dataSaved = BackEnd_Helper_importOffersExcel::importExcelOffers($excelFile);
                    if ($dataSaved) {
                        $message = $dataSaved.' '.$this->view->translate('backend_Valid Offers have been imported Successfully!!');
                        $flashMessage->addMessage(array('success' => $message));
                        $this->_redirect(HTTP_PATH . 'admin/offer');
                    } else {
                        $message = $this->view->translate('backend_Problem in your Data!!');
                        $flashMessage->addMessage(array('error' => $message));
                        $this->_redirect(HTTP_PATH . 'admin/offer/importoffers');
                    }
                } else {
                    $message = $this->view->translate('backend_Problem in your file size!!');
                    $flashMessage->addMessage(array('error' => $message));
                    $this->_redirect(HTTP_PATH . 'admin/offer/importoffers');
                }
            }
        }
        $message = $flashMessage->getMessages();
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
    }

    public function emptyOfferXlxAction()
    {
        $file = APPLICATION_PATH . '/migration/emptyOffer.xlsx' ;
        $fileName = $this->view->translate($file);
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()
            ->setHeader('Content-Disposition', 'attachment;filename=' . basename($fileName))
            ->setHeader('Content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody(file_get_contents($fileName));
    }

}

<?php
class Admin_UsergeneratedofferController extends Zend_Controller_Action
{
    /**
     * check authentication before load the page
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
        $this->view->controllerName = $this->getRequest()
                ->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');


        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new Zend_Session_Namespace();
        if($sessionNamespace->settings['rights']['system manager']['rights'] != '1') {
            $this->_redirect('/admin/auth/index');
        }

    }
    public function init()
    {
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
        $deletePermanent = Offer::permanentDeleteOffer($id);
        die;
    }
    public function addofferAction()
    {

        $shopObj = new Shop();
        $this->view->shopList=$shopObj->getOfferShopList();
        $catObj = new Category();
        $this->view->catList=$catObj->getCategoryList();
        $pageObj = new Page();
        $this->view->pages = $pageObj->getPagesOffer();

     }
    public function editofferAction()
    {
        $params = $this->_getAllParams();
        $this->view->qstring = $_SERVER['QUERY_STRING'];
        $this->view->offerId = $params['id'];
        $shopObj = new Shop();
        $this->view->shopList=$shopObj->getOfferShopList();
        $catObj = new Category();
        $this->view->catList=$catObj->getCategoryList();
        $pageObj = new Page();
        $this->view->pages = $pageObj->getPagesOffer();
           $this->view->offerVoteList = Vote::getofferVoteList($params['id']);

    }


    public function updateofferAction()
    {
        $params = $this->_getAllParams();
        $offer = Doctrine_Core::getTable("UserGeneratedOffer")->find($params['offerId']);
        $offer->updateOffer($params);
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate('Offer has been updated successfully.');
        $flash->addMessage(array('success' => $message ));
        $this->_redirect(HTTP_PATH.'admin/usergeneratedoffer#'.$params['qString']);
        die;
        //echo "Edit shop is under progress";

    }



    public function shopdetailAction()
    {
        $params = $this->_getAllParams();
        $shopObj = new Shop();
        echo Zend_Json::encode($shopObj->getShopDetail($params['shopId']));
        die;
    }

    public function saveofferAction()
    {
        $params = $this->_getAllParams();

        $offerObj = new Offer();
        $offerObj->saveOffer($params);
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate('Offer has been added successfully.');
        $flash->addMessage(array('success' => $message ));
        $this->_redirect(HTTP_PATH.'admin/offer');
        die;
    }


    /**
     * Get offer list from database by flag
     * flag (1 deleted  or 0 not deleted )
     * @author kraj
     * @version 1.0
     */
    public function getofferAction()
    {
        $params = $this->_getAllParams();
        //cal to getofferlist function from offer model class
        $offerList = UserGeneratedOffer::getofferList($params);
        echo Zend_Json::encode($offerList);
        die();
    }

    /**
     * Get offer Votes from database
     * flag (1 deleted  or 0 not deleted )
     * @author Raman
     * @version 1.0
     */
    public function getoffervoteAction()
    {
        $params = $this->_getAllParams();
        //cal to getofferlist function from offer model class
        $offerVoteList = Vote::getofferVoteList($params);
        echo Zend_Json::encode($offerVoteList);
        die();
    }
    /**
     * record move in trash
     * @author kraj
     * @version 1.0
     */
    public function movetotrashAction()
    {
        $id = $this->getRequest()->getParam('id');
        //cal to moveToTrash function from offer model class
        $trash = Offer::moveToTrash($id);

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
     * Make a user generated offer offline
     * @author Raman
     * @version 1.0
     */
    public function makeofflineAction()
    {
        $id = $this->getRequest()->getParam('id');
        $offlineval = $this->getRequest()->getParam('ob');

        //call to maketooffline function from offer model class
        $res = UserGeneratedOffer::makeToOffline($id, $offlineval);

        if (intval($res) > 0) {

            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Offer offline status has been updated');
            $flash->addMessage(array('success' => $message));

        } else {

            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
        }
        echo Zend_Json::encode($res);
        die;
    }
    /**
     * Delte a user generated offer vote
     * @author Raman
     * @version 1.0
     */
    public function deletevoteAction()
    {
        $id = $this->getRequest()->getParam('id');

        //call to maketooffline function from offer model class
        $res = Vote::deleteVote($id);

        if (intval($res) > 0) {

            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('The Vote has been deleted');
            $flash->addMessage(array('success' => $message));

        } else {

            $message = $this->view->translate('Problem in your data.');
            $flash->addMessage(array('error' => $message));
        }
        echo Zend_Json::encode($res);
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
        $data = UserGeneratedOffer::searchToFiveShop($srh, $flag);
        //echo $data;
        //die;
        $ar = array();
        $removeDup = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                    $id =  $d['shop']['id'];
                    //array fro remove duplicate search text
                    if(isset($removeDup[$id])) {
                        $removeDup[$id] = $id;

                    } else {

                        $removeDup[$id] = $id;
                        $ar[] = ucfirst($d['name']);

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
        $data = UserGeneratedOffer::searchToFiveOffer($srh, $flag);

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

        // action body
    }


    /**
     * Export offer list in excell file
     * @author kraj
     * @version 1.0
     */
    public function exportofferlistAction()
    {
        // get all shop from database
        $data = Offer::exportofferList();
        //echo "<pre>";
        //print_r($data);
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

                if ($offer['shop']['shopname'] == ''
                        || $offer['shop']['shopname'] == 'undefined'
                        || $offer['shop']['shopname'] == null
                        || $offer['shop']['shopname'] == '0') {

                    $shopname = '';

                } else {

                    $shopname = $offer['shop']['shopname'];
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
            $Clickouts = '11';
            // get Author from array
            $Author = '';
            if (isset($offer['acName'])) {

                $Author = $offer['acName'];

            } else {

                $Author = '';
            }

            // set value in column of excel
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $column, $title);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('B' . $column, $shopname);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $column, $type);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('D' . $column, $Visability);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('E' . $column, $Extended);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('F' . $column, $startDate);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('G' . $column, $endDate);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('H' . $column, $Clickouts);
            $objPHPExcel->getActiveSheet()
                    ->setCellValue('I' . $column, $Author);

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

        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'I1')
                ->applyFromArray($headerStyle);

        // SET ALIGN OF TEXT
        $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:I' . $row)->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        // BORDER TO CELL
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'I1')
                ->applyFromArray($borderStyle);
        $borderColumn = (intval($column) - 1);
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'I' . $borderColumn)
                ->applyFromArray($borderStyle);

        // SET SIZE OF THE CELL
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')
                ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')
                ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')
                ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')
                ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')
                ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')
                ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')
                ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')
                ->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')
                ->setAutoSize(true);
        // redirect output to client browser
        $fileName =  $this->view->translate('offerList.xlsx');
        header(
                'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
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
        $deletePermanent = Offer::deleteOffer($id);

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
     * restore offer by id
     * @author kraj
     * @version 1.0
     */
    public function restoreofferAction()
    {
        $id = $this->getRequest()->getParam('id');
        //cal to restoreOffer function from offer model class
        $restore = Offer::restoreOffer($id);

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
            $offerObj = new UserGeneratedOffer();
            $offerDetail = $offerObj->getOfferDetail($id);
            echo Zend_Json::encode($offerDetail);
            die;
    }




}

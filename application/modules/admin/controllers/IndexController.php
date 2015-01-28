<?php

class Admin_IndexController extends Zend_Controller_Action
{

    public function preDispatch()
    {

        if (!Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');

        }

    }
    public function init()
    {
        BackEnd_Helper_viewHelper::addConnection();//connection generate with second database

        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';
    }

    public function indexAction()
    {
        $data = Dashboard::getDashboardToDisplay();
        $this->view->data = $data;

        $lastweek = $data['total_no_of_shops_online_code_lastweek'];
        $thisweek = $data['total_no_of_shops_online_code_thisweek'];
        if ($lastweek > 0) :
            $percent = (($thisweek/$lastweek) - 1) * 100;
            $prcnt = explode('.', round($percent, 2));
        else:
            $percent = '0.00';
            $prcnt = explode('.', $percent);
        endif;
        $green_img = 'arrow-green-dashboard';
        $green_cls = 'green-text-arrow';
        if ($percent < 0) {
            $green_img = 'arrow-red-dashboard';
            $green_cls = 'red-text-arrow';
        }
        $this->view->green_img = $green_img;
        $this->view->green_cls = $green_cls;
        $this->view->prcnt = $prcnt;

    }

    public function savewidgetAction()
    {
        // action body
    }

    public function saveadmintextAction()
    {
        $text = BackEnd_Helper_viewHelper::stripSlashesFromString($this->getRequest()->getParam('content'));
        $textCond = trim($text);
        $checkDataExist = Doctrine_Query::create()->from('Dashboard')->fetchArray();
        if (count($checkDataExist) == 0) {
            $saveMessage = new Dashboard();
            $saveMessage->id = 1;
            $saveMessage->message = trim($text);
            $saveMessage->save();


        } else {
            $updateMessage = Doctrine_Query::create()->update('Dashboard')->set("message", "'".trim($text)."'")->execute();

        }
        if ($textCond=='' || $textCond == null) {
            echo $this->view->translate('Click here and write a message for employees.');
        } else {

            echo '1';
        }
        die;
    }

    /**
     * Testing of cron job functions for updating dashboard purpose
     * @author Raman
     * @version 1.0
     */
    public static function getdataAction()
    {
        $name = ShopViewCount::getTotalAmountClicksOfShop(465);
        echo "<pre>"; print_r($name);
        die();
    }

    /**
     * relese note fro admin
     */
    public function releaseNotesAction()
    {

    }
}

<?php

class Admin_MenuController extends Zend_Controller_Action
{

    /**
     * check authentication before load the page
     * @see Zend_Controller_Action::preDispatch()
     * @author mkaur
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


        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new Zend_Session_Namespace();
        if (
            $sessionNamespace->settings['rights']['content']['rights'] != '1'
            && $sessionNamespace->settings['rights']['content']['rights'] != '2'
        ) {
            $this->_redirect('/admin/auth/index');
        }
    }
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {

    }
    /**
     * save menu data in database to create menu.
     * @author mkaur
     */
    public function addmenuAction()
    {
        $params = $this->_getAllParams();
        if ($params['hid']=='') {
            $menu = \KC\Repository\Menu::insertOne($params);
            echo Zend_Json::encode($menu);
        } else {
            $menu = \KC\Repository\Menu::insertNode($params);
            echo Zend_Json::encode($menu);
        }
        die();

    }
    
  /**
   * fetches list of records for left pannel
   * @author mkaur
   */
    public function listmenuAction()
    {
        $menu = \KC\Repository\Menu::getmenuList();
        echo Zend_Json::encode($menu);
        die();
    }

    /**
     * Fetches one row record for model popup
     * @author mkaur
     */
    public function getrecordAction()
    {
        $params = $this->getRequest()->getParam('id');
        $menu = \KC\Repository\Menu::getmenuRecord($params);
        echo Zend_Json::encode($menu);
        die();
    }

    /**
     * update menu records.
     * @author mkaur
     */
    public function editmenuAction()
    {
        $params = $this->_getAllParams();
        $menu = \KC\Repository\Menu::editmenuRecord($params);
        echo Zend_Json::encode($menu);
        die();
    }

    /**
    * get right menu records
    * @author mkaur
    */
    public function getrightmenuAction()
    {
        $params = $this->getRequest()->getParam('id');
        $menu = \KC\Repository\Menu::getrtmenuRecord($params);
        echo Zend_Json::encode($menu);
        die();
    }

    /**
    * get position min position in the database
    * @author mkaur
    */
    public function getidAction()
    {
        $menu = \KC\Repository\Menu::gethighposition();
        echo Zend_Json::encode($menu);
        die();
    }

    /**
    * uploads image by creating thumb.
    * @author mkaur
    */
    public function uploadimageAction()
    {

        $params = $this->_getAllParams();
        $uploadPath = "images/upload/menu/";

        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $user_path = ROOT_PATH . $uploadPath;
        if (!file_exists($user_path)) {
            mkdir($user_path, 776, true);
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
            $path = ROOT_PATH . $uploadPath . "thum_" . $orgName;
            \BackEnd_Helper_viewHelper::resizeImage(
                $_FILES["files"],
                $orgName,
                35,
                35,
                $path
            );

            $adapter->addFilter(
                new \Zend_Filter_File_Rename(
                    array('target' => $fname,
                    'overwrite' => true)
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
                    "path" => "$uploadPath"
                )
            );
        }
        die();
    }

    public function mainmenuAction()
    {
        // $mainmenu = mainmenu::insertOne();
    }
    
    /**
    * Save mainmenu data in database to create menu.
    * @author mkaur
    */
    public function addmainmenuAction()
    {
        $params = $this->_getAllParams();
        //print_r($params);die;
        if ($params['hid']=='') {
            $mainmenu = \KC\Repository\Mainmenu::insertOne($params);
            echo Zend_Json::encode($mainmenu);
        } else {
            $mainmenu = \KC\Repository\Mainmenu::insertNode($params);
            echo Zend_Json::encode($mainmenu);
        }
        die();
    }

    /**
    * fetches list of records for left pannel
    * @author mkaur
    */
    public function listmainmenuAction()
    {
        $mainmenu = \KC\Repository\Mainmenu::getmenuList();
        //print_r($menu);die;
        echo Zend_Json::encode($mainmenu);
        die();
    }
    /**
    * Fetches one row record for model popup
    * @author mkaur
    */
    public function getmainrecordAction()
    {
        $params = $this->getRequest()->getParam('id');
        $mainmenu = \KC\Repository\Mainmenu::getmenuRecord($params);
        //echo "<pre>";
        //  print_r($mainmenu);die;
        echo Zend_Json::encode($mainmenu);
        die();
    }
    /**
    * edit Mainmenu records in the database
    * @author mkaur
    */
    public function editmainmenuAction()
    {
        $params = $this->_getAllParams();

        // print_r($params);die;
        $mainmenu = \KC\Repository\Mainmenu::editmenuRecord($params);
        echo Zend_Json::encode($mainmenu);
        die();
    }

    public function getrtmainmenuAction()
    {
        $params = $this->getRequest()->getParam('id');
        $menu = \KC\Repository\Mainmenu::getrtmainmenuRecord($params);
        //$menu = mainmenu::getSecondLevel($params);
        //print_r($menu);die;
        echo Zend_Json::encode($menu);
        die();
    }
    public function getmainidAction()
    {
        $menu = \KC\Repository\Mainmenu::getmainhighposition();
        echo Zend_Json::encode($menu);
        die();

    }
    public function getthirdlevelAction()
    {
        $params = $this->getRequest()->getParam('id');
        //$menu = mainmenu::getrtmainmenuRecord($params);
        $menu = \KC\Repository\Mainmenu::getThirdLevel($params);
        //print_r($menu);die;
          echo Zend_Json::encode($menu);
        die();
    }

    /**
    * Delete menu from database
    * @author mkaur
    */
    public function deletemenuAction()
    {
        $params = $this->_getAllParams();
        $menu = \KC\Repository\Menu::deleteMenuRecord($params);
        echo Zend_Json::encode($menu);
        die();
    }

    /**
    * Delete Mainmenu from database
    * @author mkaur
    */
    public function deletemainmenuAction()
    {
        $params = $this->_getAllParams();
        $menu = \KC\Repository\Mainmenu::deleteMenuRecord($params);
        echo Zend_Json::encode($menu);
        die();
    }
 }

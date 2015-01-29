<?php

class Admin_LanguageController extends Zend_Controller_Action
{

    /**
     * Check the login state of user
     * @author cbhopal
     * @version 1.0
     */
    public function preDispatch()
    {
        $params = $this->_getAllParams();
        if (!\Auth_StaffAdapter::hasIdentity()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect('/admin/auth/index');
        }

        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');

    }
/**
 * Flash success and error messages.
 * (non-PHPdoc)
 * @see Zend_Controller_Action::init()
 * @author cbhopal
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

   public function indexAction()
    {

    }

    public function getlanguagelistAction()
    {
        $params = $this->getRequest()->getParams();
        $scanned_directory = array();
        $handle = opendir(ROOT_PATH . 'language');

        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'po') {

                    $scanned_directory[]['fileName'] = $file ;
                }
            }
            closedir($handle);
        }

        $retArr = array("sEcho" => intval($params['sEcho']),
                "iTotalRecords" => intval(count($scanned_directory)),
                "iTotalDisplayRecords" => intval(count($scanned_directory)),
                "aaData" => $scanned_directory);


        echo Zend_Json::encode($retArr);
        die;
    }

    public function downloadfileAction()
    {

        $fileName = ROOT_PATH . 'language/' . $this->getRequest()->getParam('fname');

        //echo $fileName; die;
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($fileName));
        readfile($fileName);
        die;
    }

    public function savefileAction()
    {
        if (isset($_FILES['files']['name']) && $_FILES['files']['name'] != '') {

            $ext = \BackEnd_Helper_viewHelper::getImageExtension($_FILES['files']['name']);


            $result = self::uploadImage($_FILES['files']);

            die;
        }
    }

    public function uploadimage($files)
    {
        $uploadPath = "language/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $user_path = ROOT_PATH . $uploadPath;
        $img = $files['name'];
        //echo $user_path; die;
        //unlink image file from folder if exist
        if (file_exists($user_path.$img)) {
            @unlink($user_path . $img);
        }
        if (!file_exists($user_path))
            mkdir($user_path ,776, true);
        $adapter->setDestination(ROOT_PATH . $uploadPath);
        $adapter->addValidator('Extension', false, array('po,mo', true));
        $files = $adapter->getFileInfo();
        foreach ($files as $file => $info) {

            $name = $adapter->getFileName($file, false);
            $name = $adapter->getFileName($file);
            $orgName = $info['name'];
            $fname = $user_path . $orgName;

            $adapter->addFilter(
                    new \Zend_Filter_File_Rename(
                            array('target' => $fname,
                                    'overwrite' => true)), null, $file);

            $adapter->receive($file);
            $status = "";
            $data = "";
            $msg = "";
            $flash = $this->_helper->getHelper('FlashMessenger');
            if ($adapter->isValid($file) == 1) {

                $message = $this->view->translate('File is uploaded successfully');
                $flash->addMessage(array('success' => $message));

            } else {

                $message = $this->view->translate('Please upload the valid PO or MO file');
                $flash->addMessage(array('error' => $message));

            }
                echo Zend_Json::encode(
                        array("fileName" => $data, "sttaus" => $status,
                                "msg" => $msg, "displayFileName" => $info['name'],
                                "path" => "$uploadPath" ));
            die();
        }

    }

    public function scanfileAction()
    {
        # add suffix according to locale
        $suffix = "" ;
        if(LOCALE) {
            $suffix = "_" . strtoupper( LOCALE ) ;
        }

        $fileName = $this->getRequest()->getParam('fname');
        $existback_php = strstr($fileName, 'backend_php' . $suffix );// check if back_php keyword is present
        $existback_js = strstr($fileName, 'backend_js' . $suffix);// check if back_js keyword is present
        $existfront_js = strstr($fileName, 'frontend_js' . $suffix);// check if front_js keyword is present
        $existlinks = strstr($fileName, 'links');// check if links keyword is present
        $existForm = strstr($fileName, 'form'  . $suffix);// check if form keyword is present
        $existEmail = strstr($fileName, 'email'  . $suffix);// check if emails keyword is present
        $obj = new \FrontEnd_Helper_POTCreator();

        if($existback_php){
            $scanPath[0] = APPLICATION_PATH . '/modules';
            $obj->set_exts('php|phtml');
            $obj->set_regular('/(translate)\([^)]*\)/');
        }elseif($existback_js){
            $scanPath[0] = APPLICATION_PATH.'/../public/js/back_end';
            $scanPath[1] = APPLICATION_PATH.'/../public/js/front_end';
            $obj->set_exts('js');
            $obj->set_regular('/(__)\([^)]*\)/');
        }elseif($existfront_js){
            $scanPath[0] = APPLICATION_PATH.'/../public/js/front_end';
            $obj->set_exts('js');
            $obj->set_regular('/(__)\([^)]*\)/');
        }elseif($existlinks){
            $scanPath[0] = APPLICATION_PATH . '/controllers';
            $scanPath[1] = APPLICATION_PATH . '/views';
            $scanPath[2] = APPLICATION_PATH . '/modules';
            $scanPath[3] = APPLICATION_PATH . '/models';
            $scanPath[4] = APPLICATION_PATH . '/migration';
            $scanPath[5] = LIBRARY_PATH . '/FrontEnd';
            $scanPath[6] = APPLICATION_PATH.'/../public/js/front_end';
            $obj->set_exts('php|phtml|js');
            $obj->set_regular('/(__link)\([^)]*\)/');
        }elseif ($existForm) {
            $scanPath[0] = APPLICATION_PATH . '/controllers';
            $scanPath[1] = APPLICATION_PATH . '/views';
            $scanPath[2] = APPLICATION_PATH . '/modules';
            $scanPath[3] = APPLICATION_PATH . '/migration';
            $scanPath[4] = LIBRARY_PATH . '/FrontEnd';
            $scanPath[5] = APPLICATION_PATH . '/forms';
            $obj->set_exts('php|phtml');
            $obj->set_regular('/(__form)\([^)]*\)/');
        }elseif ($existEmail) {
            $scanPath[0] = APPLICATION_PATH . '/controllers';
            $scanPath[1] = APPLICATION_PATH . '/views';
            $scanPath[2] = APPLICATION_PATH . '/modules';
            $scanPath[3] = APPLICATION_PATH . '/migration';
            $scanPath[4] = LIBRARY_PATH . '/BackEnd';
            $obj->set_exts('php|phtml');
            $obj->set_regular('/(__email)\([^)]*\)/');
        }

        //echo "<pre>"; print_r($scanPath); die;

        $obj->set_root($scanPath);

        $obj->set_read_subdir(true);

        $potfile = ROOT_PATH . 'language/'.$this->getRequest()->getParam('fname').'t';
        $obj->write_pot($potfile);
            header('Content-Description: File Transfer');
            header('Content-Type: application/force-download');
            header('Content-Disposition: attachment; filename='.$this->getRequest()->getParam('fname').'t');
            readfile($potfile);
            unlink($potfile);
            die;

    }

    public function deleteAction()
    {



        try {

                # file name to delete
                $file = $this->getRequest()->getParam("file" , false);


                $uploadPath = "language/";
                $filePath =  ROOT_PATH . $uploadPath . $file ;

                # cretae mo file path
                $fileName =  basename($file , '.po');
                $moFile = 	ROOT_PATH . $uploadPath . $fileName . ".mo" ;


                //unlink files from folder if exist
                if (file_exists($filePath)) {
                    @unlink($filePath);
                    @unlink($moFile);
                }

                echo $this->_helper->json->sendJson('true');
            } catch (Exception $e) {

                //echo $this->_helper->json->sendJson($e->getMessage());
            }

    }
    public function renamefilesAction()
    {
        self::traverseDir(ROOT_PATH);
        die;
    }


     static function traverseDir($dir)
     {
      echo "traversing  $dir  <br>";
        if(!($dp = opendir($dir))) die("Cannot open $dir.");

        while((false !== $file = readdir($dp))){


            # do nothing for root or parent directory and traverse untill langauge dir not found
            if($file != '.' && $file !='..'){

                # read language dir
                if( is_dir($dir . $file) && basename($file) == 'language') {
                         self::traverseDir($dir . $file);
                   }

                   # rename po and mo fiel if current dir id language dir
                   if(basename($dir) == 'language') {



                       # add suffix according to locale
                       $suffix = "" ;
                       if(LOCALE) {
                           $suffix = "_" . strtoupper( LOCALE ) ;
                       }


                       if(!  strstr ( $file, $suffix . ".po" ) &&  ! strstr ( $file, $suffix . ".mo" )) {
                        echo "this is file $file <br>";
                    }
                }

            }


        }
        closedir($dp);

    }

}

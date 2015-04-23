<?php
/**
 * get and set values from database
 * @author kraj
 * @version 1.0
 *
 */

class Admin_UserController extends Zend_Controller_Action
{
    /**
     * check authentication before load the page
     * @see Zend_Controller_Action::preDispatch()
     * @author kraj
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


        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ?
        $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ?
        $message[0]['error'] : '';

        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new Zend_Session_Namespace();


        # add action as new case which needs to be viewed by other users
        switch(strtolower($this->view->action)) {
            case 'profile':

            case 'searchtoptenshop':
            break;
            default:
                 if( $sessionNamespace->settings['rights']['administration']['rights'] != '1' ) {
                    $this->_redirect('/admin/auth/index');
                 }

        }
    }
    /**
     * User Helper file for switch the connection
     * @see Zend_Controller_Action::init()
     * @author kraj
     * @version 1.0
     */
    public function init()
    {
        \BackEnd_Helper_viewHelper::addConnection();//connection generate with second database
    }
    /**
     * set the basic hidden field in user list and add new user
     * @author kraj
     */
    public function indexAction()
    {
       // echo "<pre>";
       //  print_r(KC\Repository\RouteRedirect::getRedirect(array('role'=>1, "searchtext"=>'k')));
       // die('END');

        $u = \Auth_StaffAdapter::getIdentity();
        $this->view->id = $u->id;
        $this->view->role = $u->users->id;
        $this->view->roles = KC\Repository\Role::createUserPermission($u->users->id);
        //get flashes
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
    }
    /**
     * add user form
     * @author kkumar
     */
    public function adduserAction()
    {
        $u = \Auth_StaffAdapter::getIdentity();
        $this->view->id = $u->id;
        $this->view->role = $u->users->id;
        $this->view->roles = KC\Repository\Role::createUserPermission($u->users->id);
        $categoryList =  KC\Repository\Category::getCategoryList();
        $this->view->categoryList = $categoryList['aaData'] ;
        $this->view->countriesLocales = \FrontEnd_Helper_viewHelper::getAllCountriesByLocaleNames();
    }
    /**
     * function use for getwebbsite acccording to useId and role
     * @return array $data
     * @author kraj
     * @version 1.0
     */
    public function getwebsiteAction()
    {
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender();

        $data =  KC\Repository\User::getWebsite(
            $this->getRequest()->getParam('id'),
            $this->getRequest()->getParam('rolid')
        );
        echo Zend_Json::encode($data);
        die();
    }
    /**
     * Get five record from database by search text in autocomplete
     * @param string $text
     * @author kraj
     * @version 1.0
     */
    public function gettopfiveAction()
    {
        $ar = KC\Repository\User::getTopFiveForAutoComp($this->getRequest()->getParam('for'),$this->getRequest()->getParam('text'));
        echo Zend_Json::encode($ar);
        die();
    }
    /**
     * function use for getalluser from database
     * @return array $data
     * @author kraj
     * @version 1.0
     */
    public function getuserlistAction()
    {
        $params = $this->_getAllParams();
        $userList =  KC\Repository\User::getUserList($params);
        echo  $userList;
        die();
    }
    /**
     * function use for save the new user in database
     * @return integr $id
     * @author kraj
     * @param posted data and image
     */
    public function saveuserAction()
    {
        $params = $this->getRequest()->getParams();

        $uesrPicName = '';
        if (isset($_FILES['imageName']['name']) && $_FILES['imageName']['name']!='') {
            $uesrPicName = self::uploadFile($_FILES['imageName']['name']);
        }

        $flash = $this->_helper->getHelper('FlashMessenger');
        $result  = null;
        if ($params) {
            $u = new KC\Repository\User();
            $result  = $u->addUser($params, $uesrPicName);

            # check if there is any error in user data
            if (is_array($result) && isset($result['error'])) {
                    $message = $this->view->translate($result['message']);
                    $flash->addMessage(array('error' => $message));

                    $this->_redirect(HTTP_PATH.'admin/user/adduser');

                    exit;
            }
            //$userPermlink = $u->slug ;
            //self::updateVarnish($userPermlink);
        }
        echo Zend_Json::encode($result);
        $message = $this->view->translate('User has been created successfully.');
        $flash->addMessage(array('success' => $message ));
        $this->_redirect(HTTP_PATH.'admin/user');
        die();
    }
    /**
     * save image for user in database
     * @param objec $file
     * @version 1.0
     * @author kkumar
     */
    public function uploadFile($imgName)
    {
        $uploadPath = "images/upload/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $user_path = BASE_ROOT . $uploadPath;
        $img = $imgName;

        //unlink image file from folder if exist
        if ($img) {
            @unlink($user_path . $img);
            @unlink($user_path . "thum_" . $img);
            @unlink($user_path . "thum_large" . $img);
        }

        if (!file_exists($user_path))
            mkdir($user_path, 776, true);

        $adapter->setDestination(BASE_ROOT . $uploadPath);
        $adapter->addValidator('Extension', false, 'jpg,jpeg,png,gif');
        $files = $adapter->getFileInfo();
        foreach ($files as $file => $info) {

            $name = $adapter->getFileName($file, false);
            $name = $adapter->getFileName($file);
            $orgName = time() . "_" . $info['name'];
            $fname = $user_path . $orgName;
            //call function resize image
            $path = BASE_ROOT . $uploadPath . "thum_" . $orgName;
            \BackEnd_Helper_viewHelper::resizeImage($_FILES["imageName"], $orgName, 126, 90, $path);

            //call function resize image
            $path = BASE_ROOT . $uploadPath . "thum_medium_" . $orgName;
            \BackEnd_Helper_viewHelper::resizeImage($_FILES["imageName"], $orgName, 100, 85, $path);

            $path = BASE_ROOT . $uploadPath . "thum_large_" . $orgName;
            \BackEnd_Helper_viewHelper::resizeImage($_FILES["imageName"], $orgName, 132, 112, $path);

            $path = BASE_ROOT . $uploadPath . "thum_large_widget_" . $orgName;
            \BackEnd_Helper_viewHelper::resizeImage($_FILES["imageName"], $orgName, 142, 90, $path);


            $adapter->addFilter(
                    new \Zend_Filter_File_Rename(
                            array('target' => $fname,
                                    'overwrite' => true)), null, $file);

            $adapter->receive($file);
            $status = "";
            $data = "";
            $msg = "";
            if ($adapter->isValid($file) == 1) {
                $data = $orgName;
                $status = "200";
                $statusMessage = $this->view->translate("File uploaded successfully.");

            } else {

                $status = "-1";
                $msg = $this->view->translate("Please upload the valid file");

            }
            return $data;
        }
    }
    /**
     * function use for delete user from database
     * @return boolean true/false
     * @version 1.0
     */
    public function deleteuserAction()
    {
        $entityManagerUser  = \Zend_Registry::get('emUser');
        $id = $this->getRequest()->getParam('id');
        if ($id && $id != \Auth_StaffAdapter::getIdentity()->id) {
            $uDel = $entityManagerUser->find('KC\Entity\User', $id);
            $uDel->deleted = true;
            $entityManagerUser->persist($uDel);
            $entityManagerUser->flush();
            $User = new KC\Entity\User();
            //$User->updateInDatabase($id, null, 0);
            $userPermlink = $uDel->__get('slug');
            //self::updateVarnish($userPermlink);

        } else {
            $id = null;
        }
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate('User has been deleted successfully.');
        $flash->addMessage(array('success' => $message ));
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_user_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_users_list');

        echo Zend_Json::encode($id);
        die();
    }
    /**
     * this function use only for view of the trashed
     * users
     * @author kraj
     * @version 1.0
     */
    public function trashAction()
    {
        // action body
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
    }
    /**
     * Export user list in excel with users images
     * @author kraj
     * @version 1.0
     *
     */
    public function exportuserlistAction()
    {
        $role =   \Zend_Auth::getInstance()->getIdentity()->roleId;
        //get data from database (user table)
                $data = Doctrine_Query::create()
                ->select('u.*,r.name as role,p.path as path,p.name as ppname')
                ->from("User u")->leftJoin('u.profileimage p')
                ->addSelect('(SELECT COUNT(us.createdby) FROM User us WHERE us.createdby = u.id)  as entries')
                ->where('u.deleted=0')
                ->addWhere('u.roleId >='.\Auth_StaffAdapter::getIdentity()->roleId)
                ->addWhere("u.id <>".\Auth_StaffAdapter::getIdentity()->id)
                ->leftJoin('u.role r')->orderBy("u.id DESC")->fetchArray();
        //CREATE A OBJECT OF PHPECEL CLASS
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', $this->view->translate('Name'));
        $objPHPExcel->getActiveSheet()->setCellValue('C1', $this->view->translate('Email'));
        $objPHPExcel->getActiveSheet()->setCellValue('D1', $this->view->translate('Role'));
        $objPHPExcel->getActiveSheet()->setCellValue('E1', $this->view->translate('Entries'));
        $column = 2;
        $row = 2;
        foreach ($data as $user) {

            $name  =  $user['firstName'] . " " . $user['lastName'];
            //GET PATH OF THE IMAGE
            if($user['ppname']!=null || $user['ppname']!='') {

                $tPath = ROOT_PATH  . $user['path'].'thum_'.$user['ppname'];

            } else {

                $tPath = ROOT_PATH  . "images/NoImage/" .'user-avtar.jpg';
            }

            if(!file_exists ( $tPath )){

                $tPath = ROOT_PATH  . "images/NoImage/" .'user-avtar.jpg';
            }
            //CREATE A OBJECT OF DRAWING CLASS
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName($name);
            $objDrawing->setDescription('Profile Image');
            $objDrawing->setPath($tPath);
            $objDrawing->setCoordinates('A'.$column);
            $objDrawing->setHeight(90);
            $objDrawing->setWidth(126);
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(70);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);

            //SET VALUE IN CELL
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $name);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$column, $user['email']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$column, $user['role']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$column, $user['entries']);

            $column++;
            $row++;
        }
        //ADD COMMENT IN AXCELL
        $objPHPExcel->getActiveSheet()->getComment('E1')->setAuthor('PHPExcel');
        $objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E1')->getText()->createTextRun('Pending:');
        $objCommentRichText->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getComment('E1')->getText()->createTextRun("\r\n");
        $objPHPExcel->getActiveSheet()->getComment('E1')->getText()->createTextRun('This column value is static');

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

        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'E1')->applyFromArray($headerStyle);

        //SET ALIGN OF TEXT
        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('B2:E'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        //BORDER TO CELL
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'E1')->applyFromArray($borderStyle);
        $borderColumn =  (intval($column) -1 );
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.'E'.$borderColumn)->applyFromArray($borderStyle);

        //SET SIZE OF THE CELL
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        // redirect output to client browser
        $fileName =  $this->view->translate('UserList.xlsx');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$fileName);
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        die();

    }
    /**
     * save image for user in database
     * @param objec $file
     * @version 1.0
     * @author kraj
     */
    public function uploadimageAction()
    {
        $uploadPath = "images/upload/";
        $adapter = new \Zend_File_Transfer_Adapter_Http();
        $user_path = ROOT_PATH . $uploadPath;
        $img = $this->getRequest()->getParam('imageName');

        //unlink image file from folder if exist
        if ($img) {
            @unlink($user_path . $img);
            @unlink($user_path . "thum_" . $img);
            @unlink($user_path . "thum_large" . $img);
        }
        if (!file_exists($user_path))
            mkdir($user_path,776, true);
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
            \BackEnd_Helper_viewHelper::resizeImage($_FILES["files"], $orgName,
                    126, 90, $path);

            //call function resize image
            $path = ROOT_PATH . $uploadPath . "thum_large" . $orgName;
            \BackEnd_Helper_viewHelper::resizeImage($_FILES["files"], $orgName,
                    132, 95, $path);

            $adapter->addFilter(
                            new \Zend_Filter_File_Rename(
                                    array('target' => $fname,
                                            'overwrite' => true)), null, $file);

            $adapter->receive($file);
            $status = "";
            $data = "";
            $msg = "";
            if ($adapter->isValid($file) == 1) {
                $data = $orgName;
                $status = "200";
                $statusMessage = $this->view->translate("File uploaded successfully.");

            } else {

                $status = "-1";
                $msg = $this->view->translate("Please upload the valid file");

            }
            echo Zend_Json::encode(
                    array("fileName" => $data, "sttaus" => $status,
                            "msg" => $msg, "displayFileName" => $info['name'],
                            "path" => "$uploadPath"));
            die();
        }
    }
    /**
     * restore user only change status of deleleted
     * column value
     * @param integer $id
     * @version 1.0
     * @author mkaur
     */
    public function restoreuserAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $entityManagerUser  = \Zend_Registry::get('emUser');
            $uRes  = $entityManagerUser->createQueryBuilder()
                ->update('KC\Entity\User', 'u')
                ->set('u.deleted', "0")
                ->where('u.id ='.$id)
                ->getQuery()->execute();

            $fU = \Zend_Registry::get('emUser')->find('KC\Entity\User', $id);
            $fullName = $fU->firstName . " " . $fU->lastName;
            $User = new \KC\Repository\User();
            $User->updateInDatabase($id, $fullName, 0);
            if ($uRes) {
                $data =  Doctrine_Query::create()
                    ->select('u.slug')
                    ->from("User u")
                    ->where('u.id= ? ', $id)
                    ->fetchOne(null, Doctrine::HYDRATE_ARRAY);
                $userPermlink = $data['slug'] ;
                //self::updateVarnish($userPermlink);
            }
        } else {
            $id = null;
        }
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate('User has been restored successfully.');
        $flash->addMessage(array('success' => $message ));
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_user_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_users_list');
        echo Zend_Json::encode($id);
        die();
    }
    /**
     * Permanent delete User from database
     * @param integer $id
     * @version 1.0
     * @author mkaur
     */
    public function permanentdeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $entityManagerUser  = \Zend_Registry::get('emUser');
            $User = new \KC\Repository\User();
            $User->updateInDatabase($id, null, 1);
            $u = \Zend_Registry::get('emUser')->find('KC\Entity\User', $id);
            $del1 =\Zend_Registry::get('emUser')->createQueryBuilder()
                ->delete('KC\Entity\refUserWebsite', 'r')
                ->where('r.websiteUsers ='.$id)
                ->getQuery()->execute();

            $del = \Zend_Registry::get('emUser')->createQueryBuilder()
                ->delete('KC\Entity\User', 'u')
                ->where('u.id ='.$id)
                ->getQuery()->execute();

            if ((intval($u->profileimage->id)) > 0) {
                $del = \Zend_Registry::get('emUser')->createQueryBuilder()
                    ->delete('KC\Entity\ProfileImage', 'i')
                    ->where('i.id ='. $u->profileimage->id)
                    ->getQuery()->execute();
            }

        } else {

            $id = null;
        }
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate('User has been deleted permanentally.');
        $flash->addMessage(array('success' => $message ));
        //call cache function
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_user_list');
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_users_list');

        echo Zend_Json::encode($id);
        die();
    }
    /**
     * Get deleted  users record from user table
     * @version 1.0
     * @author mkaur
     */
    public function trashuserlistAction()
    {
        // action body
        $params = $this->_getAllParams();
        $trashUserList = \KC\Repository\User::getTrashUserList($params);
        echo $trashUserList;
        die();
    }

    /**
     * check duplicate usr
     * @param string $email
     * @version 1.0
     * @author kraj
     */
    public function checkuserAction()
    {
        $u = KC\Repository\User::checkDuplicateUser($this->_getParam('email'));
        $cnt  = intval($u);

        if ($cnt > 0) {
            echo Zend_Json::encode(false);
        } else {
            echo Zend_Json::encode(true);
        }

        die();
    }
    /**
     * function for edit user and  fetch data form database
     * @version 1.0
     * @author spsingh
     */
    public function edituserAction()
    {

     $id = intval($this->getRequest()->getParam('id'));
     $this->view->qstring = $_SERVER['QUERY_STRING'];
     $this->view->countriesLocales = \FrontEnd_Helper_viewHelper::getAllCountriesByLocaleNames();
	 $categoryList =  KC\Repository\Category::getCategoryList() ;
	 //get favorites store of currect user(admin)
	 $favShop  = KC\Repository\User::getUserFavouriteStores($id);
	 //get unterestng category of currecnt user(admin)
	 $intCat = KC\Repository\User::getUserInterestingCat($id);
     //echo "<pre>";print_r($intCat);die;
     $catArray  = array();//array generate on key based
     foreach ($intCat as $categories){

             $catArray[] = $categories['id'];
         }
         $this->view->catArray = '';
         if(isset($catArray) && count($catArray)>0){

            $this->view->catArray =  $catArray;
         }

         //pas value on phtml page
         $this->view->categoryList = $categoryList['aaData'] ;
         $this->view->favoritesShop = $favShop;

        $catArray  = array();//array generate on key based
        foreach ($intCat as $categories) {
            $catArray[] = $categories['category']['id'];
        }
        $this->view->catArray = '';
        if (isset($catArray) && count($catArray)>0) {
            $this->view->catArray =  $catArray  ;
        }
       
        //pas value on phtml page
        $this->view->categoryList = $categoryList['aaData'];
        $this->view->favoritesShop = $favShop;

        if ($id > 0) {
            $u = \Auth_StaffAdapter::getIdentity();
            $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
            $query = $queryBuilder->select('u, rf, w, pi, r')
                ->from('\KC\Entity\User', 'u')
                ->leftJoin("u.profileimage", "pi")
                ->leftJoin("u.users", "r")
                ->leftJoin('u.refUserWebsite', 'rf')
                ->leftJoin('rf.refUsersWebsite', 'w')
                ->where($queryBuilder->expr()->eq('u.id', $id));
            $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            //echo "<pre>";
           // print_r($data);
           // die();
            $role =  Zend_Auth::getInstance()->getIdentity()->users->id;

            if (($role=='3'
                || $role=='4')
                || $role > $data['users']['id']
                || $data['users']['id'] == '1'
                || $u->id == $id
            ) {

                $flash = $this->_helper->getHelper('FlashMessenger');
                $message = $this->view->translate("You don't have permissions to edit this user.");
                $flash->addMessage(array('error' => $message ));
                $this->_redirect(HTTP_PATH.'admin/user');
            }

            $this->view->id = $u->id;
            $this->view->roles = KC\Repository\Role::createUserPermission($u->users->id);
            $this->view->roleId = $data['users']['id'];
            $this->view->userId = $id;
            $this->view->userDetail = $data;
            $this->view->role = $u->users->id;
            $this->view->webAcess ='';

       
            foreach ($data['refUserWebsite'] as $key => $value) {
                $this->view->webAcess.= $value['refUsersWebsite']['id'].',';
            }
            $this->view->webAcess = rtrim($this->view->webAcess, ',');
        }
        // echo Zend_Json::encode($data) ;
    }




   /**
    * Update user
    * @version 1.0
    * @author spsingh
    */
    public function updateuserAction()
    {
        if ($this->getRequest()->isPost()) {

            $params = $this->getRequest()->getParams();

            $id = null ;

            if ($params) {

                $uesrPicName = '';
                if (isset($_FILES['imageName']['name']) && $_FILES['imageName']['name']!='') {
                    $uesrPicName = self::uploadFile($_FILES['imageName']['name']);
                }

                $entityManagerUser  = \Zend_Registry::get('emUser');
                $repo = $entityManagerUser->getRepository('KC\Entity\User');
                $user = $repo->find($params['id']);
                $user->firstName = $params['firstName'];
                $user->lastName = $params['lastName'];
                $entityManagerUser->persist($user);
                $entityManagerUser->flush();

                $u = new KC\Repository\User();
                $result = $u->update($params, $uesrPicName);

                $flash = $this->_helper->getHelper('FlashMessenger');

                # check if there is any error in user data
                if (is_array($result) && isset($result['error'])) {

                    $message = $this->view->translate($result['message']);
                    $flash->addMessage(array('error' => $message ));

                    $this->_redirect(HTTP_PATH.'admin/user/edituser/id/' . trim($params['id']). '?'.$params['qString']);

                    exit;
                }



                //$userPermlink = $user->slug ;
                //self::updateVarnish($userPermlink);
            }

            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('User details has been updated successfully.');
            $flash->addMessage(array('success' => $message ));

            $this->_redirect(HTTP_PATH.'admin/user#'.$params['qString']);
            //echo Zend_Json::encode($id);
            die();
        }
    }
    /**
     * function use for getwebbsite acccording to useId and role
     * @return array $data
     * @author kraj
     * @version 1.0
     */
    public function validatepasswordAction()
    {
        $params = $this->getRequest()->getParams();
        $isValid = "Old password don't matched" ;
        if (intval($params['id']) > 0) {
            $entityManagerUser  =\Zend_Registry::get('emUser');
            $user = $entityManagerUser->find('KC\Entity\User', $params['id']);
            $isValid = $user->validatePassword($params['oldPassword']);
        }
        echo Zend_Json::encode($isValid);
        die();
    }
    /**
     * render profile with data
     * @author spsingh
     * @version 1.0
     */
    public function profileAction()
    {
        $id =  \Auth_StaffAdapter::getIdentity()->id;

        $connUser = \BackEnd_Helper_viewHelper::addConnection();
        \BackEnd_Helper_viewHelper::closeConnection($connUser);
        $connSite = \BackEnd_Helper_viewHelper::addConnectionSite();
        $this->view->countriesLocales = FrontEnd_Helper_viewHelper::getAllCountriesByLocaleNames();
            //get category list from category table
            $categoryList =  \KC\Repository\Category::getCategoryList() ;
            //get favorites store of currect user(admin)
            $favShop  = \KC\Repository\User::getUserFavouriteStores($id);
            //get unterestng category of currecnt user(admin)
            $intCat = \KC\Repository\User::getUserInterestingCat($id);
            //print_r($favShop);
        \BackEnd_Helper_viewHelper::closeConnection($connSite);
        $connUser = BackEnd_Helper_viewHelper::addConnection();
        $catArray  = array();//array generate on key based
        foreach ($intCat as $categories) {

            $catArray[] = $categories['category']['id'];
        }
        $this->view->catArray = '';
        if (isset($catArray) && count($catArray)>0) {

            $this->view->catArray =  $catArray  ;
        }
        //pas value on phtml page
        $this->view->categoryList = $categoryList['aaData'] ;
        $this->view->favoritesShop = $favShop;

       
        //$user = Doctrine_Core::getTable("User")->find($id);
        //echo "<pre>";
        //print_r($data);
        //die();
        $queryBuilder  = \Zend_Registry::get('emUser')->createQueryBuilder();
        $query = $queryBuilder->select('u, rf, w, pi, r')
            ->from('\KC\Entity\User', 'u')
            ->leftJoin("u.profileimage", "pi")
            ->leftJoin("u.users", "r")
            ->leftJoin('u.refUserWebsite', 'rf')
            ->leftJoin('rf.refUsersWebsite', 'w')
            ->where($queryBuilder->expr()->eq('u.id', $id));
        $data = $query->getQuery()->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $this->view->profile = $data;
    }

    /**
     * update user profile
     * @author spsingh
     * @version 1.0
     */
    public function updateprofileAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        if ($this->getRequest()->isPost()) {

            $params = $this->getRequest()->getParams();
            $id = \Auth_StaffAdapter::getIdentity()->id  ;

            if ($params) {

                $uesrPicName = '';
                if (isset($_FILES['imageName']['name']) && $_FILES['imageName']['name']!='') {
                    $uesrPicName=self::uploadFile($_FILES['imageName']['name']);
                }

                $entityManagerUser  = \Zend_Registry::get('emUser');
                $repo = $entityManagerUser->getRepository('KC\Entity\User');
                $user = $repo->find($params['id']);
                $user->firstName = $params['firstName'];
                $user->lastName = $params['lastName'];
                $entityManagerUser->persist($user);
                $entityManagerUser->flush();
                
                $u = new KC\Repository\User();
                $result = $u->update($params, $uesrPicName, 'profileupdate');

                $flash = $this->_helper->getHelper('FlashMessenger');

                # check if there is any error in user data
                if (is_array($result) && isset($result['error'])) {

                    $message = $this->view->translate($result['message']);
                    $flash->addMessage(array('error' => $message ));
                } else {

                    $message = $this->view->translate("Profile has been updated successfully");
                    $flash->addMessage(array('success' => $message ));
                }

                //$userPermlink = $user->slug ;
                //self::updateVarnish($userPermlink);
                //die('Hello');
            }
            $this->_redirect('admin/user/profile');

        }

    }
    /**
     * Get roles according to the authenticated user
     * @author kraj
     * @version 1.0
     */
    public function getrolesAction()
    {
        $roles = KC\Repository\User::getRoles();
        echo Zend_Json::encode($roles);
        die();
    }
    /**
     * add store in favorite store
     * @author kraj
     * @version 1.0
     */
    public function addstoreAction()
    {
        $data = $this->getRequest()->getParam('name');
        echo "<pre>";
        print_r($data);
        die("raman");
        //call to add offer function from model
        $flag = KC\Repository\User::addStoreInList($data);

        echo Zend_Json::encode($flag);
        echo Zend_Json::encode($data);
        die();
    }
    /**
     * delete favorite store
     * @author kraj
     * @version 1.0
     */
    public function deletestoreAction()
    {
        $id = $this->getRequest()->getParam('id');
        //call model class function  id
        KC\Repository\User::deleteStore($id);
        //get popular code from database
        $data = User::getFavoriteStore();
        echo Zend_Json::encode($data);
        die();
    }
    /**
     * search top ten shops from database based on search text
     * @author kraj
     * @version 1.0
     */
    public function searchtoptenshopAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $selectedShop = $this->getRequest()->getParam('selectedShop');
        $data = KC\Repository\User::searchTopTenStore($srh, $selectedShop);


        $ar = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {

                $ar[] = array("label"=>$d['name'],'value'=>$d['name'],'id'=>$d['id']);
            }

        } else {

            $msg = $this->view->translate('No Record Found');
            $ar[] = array("label"=>$msg,'value'=>$msg,'id'=>0);
        }

        echo Zend_Json::encode($ar);
        die;
    }
    public function checkstoreexistAction()
    {
        $name = $this->getRequest()->getParam('name');
        $retVal = KC\Repository\User::checkStoreExistOrNot($name);
        echo Zend_Json::encode($retVal);
        die();
    }

    /**
     *  updateVarnish
     *
     *  update varnish table whenever a user is updated created
     *  @param string $userPage user profile slug
     */
    public function updateVarnish($userPage)
    {
        // Add urls to refresh in Varnish
        $varnishObj = new \KC\Repository\Varnish();

        # redatice page
        $editor = \FrontEnd_Helper_viewHelper::__link("link_redactie"). "/" ;

        #user page
        $userPage = $editor . $userPage ;

        $varnishObj->addUrl( rtrim( HTTP_PATH_FRONTEND. $editor  , '/'  ));
        $varnishObj->addUrl( rtrim( HTTP_PATH_FRONTEND. $userPage  , '/'  ));
    }

    public function checkexportpasswordAction()
    {
        $globalExportPassword =  new \KC\Repository\GlobalExportPassword();
        $globalExportPasswordCount = $globalExportPassword->checkPasswordForExport(
            $this->_getParam('password'),
            'shopExport'
        );

        if ($globalExportPasswordCount > 0) {
            echo Zend_Json::encode(true);
        } else {
            echo Zend_Json::encode(false);
        }
        die();
    }
}

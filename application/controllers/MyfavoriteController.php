<?php

class MyfavoriteController extends Zend_Controller_Action
{

/**
 * Flash success and error messages.
 * (non-PHPdoc)
 * @see Zend_Controller_Action::init()
 * @author kraj
 */

    /**
     * override views based on modules if exists
     * @see Zend_Controller_Action::init()
     * @author Bhart
     */
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());

        # check module specific view exists or not
        if (file_exists (APPLICATION_PATH . '/modules/'  . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")){

            # set module specific view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/modules/'  . $module . '/views/scripts' );
        } else{

            # set default module view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
        }

    }

    public function indexAction()
    {

        if (Auth_VisitorAdapter::hasIdentity()){

            $this->pageDetail = Page::getPageFromPageAttribute(16);
            $this->view->pageTitle = @$this->pageDetail->pageTitle;
            $this->view->headTitle(@$this->pageDetail->metaTitle);

            if(@$this->pageDetail->customHeader) {
                $this->view->layout()->customHeader = "\n" . @$this->pageDetail->customHeader;
            }


            $this->view->headMeta()->setName('description', @trim($this->pageDetail->metaDescription));



        $params = $this->_getAllParams();
        $this->view->controllerName = $params['controller'];
        $this->view->action 		= $params['action'];

        // fetch suggestions for shops which are not selected as favorite
        $topStores = FrontEnd_Helper_viewHelper::getStoreForFrontEnd("popular");
        $getFavoriteNotSelectedAlready = FavoriteShop::rejectAlreadySelected($topStores);
        $this->view->topStores = $getFavoriteNotSelectedAlready;

        // fetch favorite offers for personal deals
        $data = Visitor::getfavoriteOffer();
        $paginator = FrontEnd_Helper_viewHelper::renderPagination($data,$this->_getAllParams(),27,7);
        $this->view->paginator = $paginator;

        // fetch favorite stores for personal deals
        $favoriteShops = Visitor::getFavoriteShops(Auth_VisitorAdapter::getIdentity()->id);
        $this->view->favShops = $favoriteShops;

        $userdetail=Visitor::getUserDetail(Auth_VisitorAdapter::getIdentity()->id);
        $this->view->userdetail = $userdetail[0];

        # set reponse header X-Nocache used for varnish
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');


     } else{

         $this->getResponse()->setHeader('X-Nocache', 'no-cache');
         $this->_helper->redirector->setCode(301);
         $this->_redirect(HTTP_PATH_LOCALE. FrontEnd_Helper_viewHelper::__link('login') .'/'. FrontEnd_Helper_viewHelper::__link('dummy'));
     }
   }
   public function addfavoriteshopAction()
   {

        $userId = $this->getRequest()->getParam('userid');
        $shopId = $this->getRequest()->getParam('shopid');
        $addFav = FavoriteShop::addshop($userId, $shopId);
        echo Zend_Json::encode($addFav);
        exit;
   }

   public function getsuggestionsshopsAction()
   {
        $topStores = FrontEnd_Helper_viewHelper::getStoreForFrontEnd("popular");
        $getFavoriteNotSelectedAlready = FavoriteShop::rejectAlreadySelected($topStores);
        echo Zend_Json::encode($getFavoriteNotSelectedAlready);
        die;
   }

   public function getfavoriteselectedAction()
   {
        $favoriteShops = Visitor::getFavoriteShops(Auth_VisitorAdapter::getIdentity()->id);
        echo Zend_Json::encode($favoriteShops);
        die;
   }

   public function getfavoriteofferAction()
   {
        $this->_helper->layout()->disableLayout();
        $data = Visitor::getfavoriteOffer();

        $paginator = FrontEnd_Helper_viewHelper::renderPagination($data,$this->_getAllParams(),27,7);
        $this->view->paginator = $paginator;

   }

   public function deletefavoriteAction()
   {
        $userId = $this->getRequest()->getParam('userid');
        $shopId = $this->getRequest()->getParam('shopid');
        $favoriteShops = FavoriteShop::delFavoriteShops($shopId, $userId);

        echo Zend_Json::encode($favoriteShops);
        die;
   }
   /**
    * get and show member only code in user profile page
    * @author kraj
    * @version 1.0
    */
   public function memberonlycodesAction()
   {
        if(Auth_VisitorAdapter::hasIdentity()){

            $this->view->controllerName = $this->getRequest()->getParam('controller');
            $this->view->action 		= $this->getRequest()->getParam('action');
            $offers =  FrontEnd_Helper_viewHelper::commonfrontendGetCode("newestmemberonly",12);
            $this->view->offers = $offers;
            $userdetail=Visitor::getUserDetail(Auth_VisitorAdapter::getIdentity()->id);
            $this->view->userdetail = $userdetail[0];

        }else{

            $this->_redirect(HTTP_PATH_LOCALE.'login');
        }


        # set reponse header X-Nocache used for varnish
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');




   }
/**
 * show profile of the visitor
 * @author kraj
 * @version 1.0
 */
  public function showprofilepageAction()
  {
    $this->view->controllerName = $this->getRequest()->getParam('controller');
    $this->view->action 		= $this->getRequest()->getParam('action');

    if (!Auth_VisitorAdapter::hasIdentity()) {
        $this->getResponse()->setHeader('X-Nocache', 'no-cache');
        $this->_redirect('/');
    }
    $userdetail=Visitor::getUserDetail(Auth_VisitorAdapter::getIdentity()->id);
    $this->view->userdetail = $userdetail[0];

    $flash = $this->_helper->getHelper('FlashMessenger');
    $message = $flash->getMessages();
    $this->view->messageSuccess = isset($message[0]['success']) ?
    $message[0]['success'] : '';
    $this->view->messageError = isset($message[0]['error']) ?
    $message[0]['error'] : '';


    # set reponse header X-Nocache used for varnish
    $this->getResponse()->setHeader('X-Nocache', 'no-cache');


  }
  /**
   * Save profile image of visitor
   * @author kraj
   * @version 1.0
   */
  public function savefileAction()
  {
    if (isset($_FILES['files']['name']) && $_FILES['files']['name'] != '') {

        //get extension of uploaded file
        $ext = BackEnd_Helper_viewHelper::getImageExtension($_FILES['files']['name']);
        //upload file by uploadImage function
        $result = self::uploadImage($_FILES['files']['name']);
        if ($result['status'] == '200') {
            $imgId = Auth_VisitorAdapter::getIdentity()->imageId;
            if(intval($imgId) > 0){

                $pImage = Doctrine_Core::getTable('VisitorImage')->find($imgId);

            }else{

                $pImage  = new VisitorImage();
            }
            $pImage->ext = $ext;
            $pImage->path ='/images/upload/visitor/';
            $pImage->name = $result['fileName'];
            $pImage->save();
            //get id from zend auth for editing of current visitor
            $visitor = Doctrine_Core::getTable('Visitor')->find(Auth_VisitorAdapter::getIdentity()->id);
            $visitor->imageId = $pImage->id;
            $visitor->save();


            $imgPath =  '/images/upload/visitor/' ;
            $imgName = "thum_".$result['fileName'] ;

            echo Zend_Json::encode( array( 'imgPath' => $imgPath  , 'imgName' =>  $imgName ) );

        }else {

            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Please upload the valid file');
            $flash->addMessage(array('error' => $message));

        }

        die;
    }
  }
  /**
   * upload visitor image
   * @author kraj
   * @version 1.0
   */
  public function uploadimage($imgName)
  {

        $uploadPath = "images/upload/visitor/";
        $adapter = new Zend_File_Transfer_Adapter_Http();
        $user_path = ROOT_PATH . $uploadPath;
        if (!file_exists($user_path))
            mkdir($user_path);
        $img = $imgName;

        //unlink image file from folder if exist
        if ($img) {
            @unlink($user_path . $img);
            @unlink($user_path . "thum_" . $img);
            @unlink($user_path . "thum_large" . $img);
        }
        if (!file_exists($user_path))
            mkdir($user_path);
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
            $flag = BackEnd_Helper_viewHelper::resizeImageForFrontEnd($_FILES["files"], $orgName, 0, 115, $path);
            $adapter->addFilter(
                    new Zend_Filter_File_Rename(
                            array('target' => $fname,
                                    'overwrite' => true)), null, $file);

            $adapter->receive($file);

            if ($adapter->isValid($file)) {

                return array("fileName" => $orgName, "status" => "200",
                        "msg" => "File uploaded successfully",
                        "path" => $uploadPath);

            } else {

                return array("status" => "-1",
                        "msg" => "Please upload the valid file");

            }
        }

  }


}

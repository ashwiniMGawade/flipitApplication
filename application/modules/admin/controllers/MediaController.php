<?php

class Admin_MediaController extends Zend_Controller_Action
{

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
	
		
		# redirect of a user don't have any permission for this controller
		$sessionNamespace = new Zend_Session_Namespace();
		if($sessionNamespace->settings['rights']['content']['rights'] != '1')
		{
			$this->_redirect('/admin/auth/index');
		}
		
    }

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
        // action body
    	
    	
    }

    /**
     * Get list of media from database by using model class
     * @param object $params
     * @author mkaur
     *
     */
    public function getmediaAction()
    {
   		$params = $this->_getAllParams();
		//print_r($params);die;
   		$mediaList = Media::getmediaList($params);  
		//print_r($mediaList);die;
    	echo Zend_Json::encode(
				DataTable_Helper::generateDataTableResponse($mediaList,
						$params,
						array("__identifier" => 'm.id','m.id','m.name','m.alternateText','m.created_at','m.mediaImageId','m.authorName','m.fileUrl'),
  					array(),
  					array())); 
    	die();
    
    }

    /**
     * @author mkaur
     * Permanent delete media from database
     * @param integer $id
     *
     */
    public function permanentdeleteAction()
    {
    	$id = $this->getRequest()->getParam('id');
    
    	
    	$flash = $this->_helper->getHelper('FlashMessenger');
    	
    	if(Media::permanentDeleteMedia($id)){
    		$this->_helper->flashMessenger->addMessage(array('success'=>'Media has been deleted successfully.'));
    		$this->_helper->redirector(null , 'media' , null ) ;
    	} else{
    		$this->_helper->flashMessenger->addMessage(array('error'=>'The media is not deleted.'));
    		$this->_helper->redirector(null , 'media' , null ) ;
    	}
    	
    	//echo Zend_Json::encode($deletePermanent);
    	die();
    	
    }
    /**
     * Update media records
     * @author mkaur
     */

    public function addmediaAction()
    {
    // echo Zend_Auth::getInstance()->getIdentity()->firstName;die;
    if ($this->_request->isPost()){
        	$params = $this->_getAllParams();
	    	//print_r($params);die;

	    	if(Media::updateMediaRecord($params)){
	    		$this->_helper->flashMessenger->addMessage(array('success'=>'Media has been updated successfully!'));
	    		$this->_helper->redirector(null , 'media' , null ) ;
	    	} else{
	    		$this->_helper->flashMessenger->addMessage(array('error'=>'The media is not created!'));
	    		$this->_helper->redirector(null , 'media' , null ) ;
   	    	}
		}
      	//error_reporting(E_ALL | E_STRICT);
	}

public function getmediadataAction()
{
	$params = $this->getRequest()->getParam('id');
	//print_r($params);die;
	$mediaList = Media::getMediadata($params);
	echo Zend_Json::encode(@$mediaList[0]);
	die();

}
/*public function updatemediaAction(){
	$params = $this->_getAllParams();
	print_r($params);
	$id = Media::updateMediaRecord($params);
	echo Zend_Json::encode($id);
	die();
	
}*/
public function editmediaAction(){
	$parmas = $this->_getAllParams();
	$this->view->qstring = $_SERVER['QUERY_STRING'];
	$id = $this->getRequest()->getParam('id');
	if( intval($id) > 0 )
	{
		$data = Doctrine_Query::create()
		->from('Media m')
		->where("m.id = ?" , $id)
		->fetchOne(null , Doctrine::HYDRATE_ARRAY);

		$this->view->data = $data ;
		$this->view->id = $id;
	} 
	if(@$parmas['act']=='delete'){
		$media= new Media();
		//$media = Doctrine_Core::getTable("Media")->find($id);
		$flash = $this->_helper->getHelper('FlashMessenger');
		if($media->permanentDeleteMedia($id))
		{
			$message = $this->view->translate('Media has been deleted successfully');
			$flash->addMessage(array('success' => $message));
			$this->_helper->redirector(null , 'media' , null ) ;
		
		} else
		{
			$message = $this->view->translate('Problem in your data.');
			$flash->addMessage(array('error' => $message));
			$this->_helper->redirector(null , 'media' , null ) ;
		}
	}
	/*else
	{
		$this->_helper->redirector( 'editmedia', 'media', 'admin' ) ;
	}*/
	
	if ($this->_request->isPost())
	{
		$parmas = $this->_getAllParams();
		$media = Doctrine_Core::getTable("Media")->find($id);
		$flash = $this->_helper->getHelper('FlashMessenger');
		if($media->editMediaRecord($parmas))
		{
			$message = $this->view->translate('Media has been updated successfully');
			$flash->addMessage(array('success' => $message));
			$this->_redirect(HTTP_PATH.'admin/media#'.$parmas['qString']);
	
		} else
		{
			$message = $this->view->translate('Problem in your data.');
			$flash->addMessage(array('error' => $message));
			$this->_redirect(HTTP_PATH.'admin/media#'.$parmas['qString']);
		}
		 
		 
	}
}

/**
 * save Image in database and create thumbnail(resize) of image.
 * @author mkaur 
 */
public function saveimageAction(){
		$upload_handler = new Media();
		switch ($_SERVER['REQUEST_METHOD']) {
			case 'OPTIONS':
				break;
			case 'HEAD':
			case 'GET':
				$upload_handler->getfile();
				break;
			case 'POST':
				if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
					$upload_handler->deleteMedia();
				} else {
					$upload_handler->post();
				}
				break;
			case 'DELETE':
				$upload_handler->deleteMedia();
				break;
			default:
				header('HTTP/1.1 405 Method Not Allowed');
		}
	die();
	}
}




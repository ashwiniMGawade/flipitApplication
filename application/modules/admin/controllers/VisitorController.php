<?php

class Admin_VisitorController extends Zend_Controller_Action
{

	/**
	 * For switch the connection
	 * @author mkaur
	 * @version 1.0
	 */
	public function preDispatch() {
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
		$this->_settings  = $sessionNamespace->settings['rights'] ;


		# apply admin level access on all controllers 
		if($this->getRequest()->isXmlHttpRequest()) {
		
			# add action as new case which needs to be viewed by other users
			switch(strtolower($this->view->action)) {
				case 'searchemails':
				 	# no restriction
				break;
				default:
					if( $this->_settings['administration']['rights'] != '1' &&
						$this->_settings['administration']['rights'] != '2'  ) {

		    			$this->getResponse()->setHttpResponseCode(404);
   						$this->_helper->redirector('index' , 'index' , null ) ;
					}

			}

		} else {
			if($this->_settings['administration']['rights'] != '1' &&
						$this->_settings['administration']['rights'] != '2' ) 	{
				$this->_redirect('/admin/auth/index');
			}
		}
	}
	/**
	 * Flash success and error messages.
	 * (non-PHPdoc)
	 * @see Zend_Controller_Action::init()
	 * @author mkaur
	 */
    public function init() {

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
    /**
     * function use for getfavoriteshop acccording to visitorId
     * @return array $data
     * @author mkaur
     * @version 1.0
     */
    public function getfavoriteshopAction() {

		$this->_helper->layout()->disableLayout(true);
		$this->_helper->viewRenderer->setNoRender();
		$data =  Visitor::getFavorite($this->getRequest()->getParam('id'));
		echo Zend_Json::encode($data);
		die();
    }

    /**
     * function use for get all visitors from database
     * @return array $data
     * @author mkaur
     */
    public function getvisitorlistAction() {

    	$params = $this->_getAllParams();
    	$visitorList = Visitor::VisitorList($params);
    	echo Zend_Json::encode($visitorList);
    	die();
   }

   /**
     * function use for delete Visitor from database
     * @return boolean true/false
     * @version 1.0
     * @author mkaur
     */
    public function deletevisitorAction() {

        $id = $this->getRequest()->getParam('id');
		if ($id) {

			$uDel = Doctrine_Core::getTable('Visitor')->find($id);
			$uDel->delete();

		} else {

			$id = null;
		}
		$flash = $this->_helper->getHelper('FlashMessenger');
		$message = $this->view->translate('Visitor has been deleted successfully.');
		$flash->addMessage(array('success' => $message ));
		echo Zend_Json::encode($id);
		die();
    }
    /**
     * Trash action use only for view of the trashed visitor
     * @version 1.0
     * @author mkaur
     */
	public function trashAction() {

    	$flash = $this->_helper->getHelper('FlashMessenger');
    	$message = $flash->getMessages();
    	$this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
    	$this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';
    }


    /**
     * Restore visitor only change status of deleleted
     * column value
     * @param integer $id
     * @version 1.0
     * @author mkaur
     */
    public function restorevisitorAction()  {

		$id = $this->getRequest()->getParam('id');
		if ($id) {
			$uRes = Doctrine_Query::create()->update('Visitor')
					->set('deleted', '0')->where('id=' . $id);
			$uRes->execute();
		} else {
			$id = null;
		}

		$flash = $this->_helper->getHelper('FlashMessenger');
		$message = $this->view->translate('Visitor has been restored successfully.');
		$flash->addMessage(array('success' => $message ));
		echo Zend_Json::encode($id);
		die();
    }

    /**
     * Permanent delete User from database
     * @param integer $id
     * @version 1.0
     * @author mkaur
     */
    public function permanentdeleteAction() {

		$id = $this->getRequest()->getParam('id');
		if ($id) {

			$v= Doctrine_Core::getTable("Visitor")->find($id);
			$del = Doctrine_Query::create()->delete()->from('Visitor v')
			->where("v.id=" . $id)->execute();
			if( (intval($v->imageId)) > 0)
			{
				$del2 = Doctrine_Query::create()->delete()->from('VisitorImage i')
				->where("i.id=" . $v->imageId)->execute();
			}

		} else {
		$id = null;
		}
		$flash = $this->_helper->getHelper('FlashMessenger');
		$message = $this->view->translate('Visitor has been deleted Permanently.');
		$flash->addMessage(array('success' => $message ));
		echo Zend_Json::encode($id);
		die();
    }

    /**
     * function for edit visitor and  fetch data form database
     * @version 1.0
     * @author mkaur
     */
    public function editvisitorAction() {

 	 $id = intval($this->getRequest()->getParam('id'));
 	 $this->view->qstring = $_SERVER['QUERY_STRING'];
 	 	if(intval($id) > 0 ){
 	 		$data = Visitor :: editVisitor($id);
 	 		$this->view->data = $data ;
 	 		$this->view->id = $id;
 	 		$this->view->userDetail = $data;
 	 		//print_r($data);die;
 	 		$this->view->favShopId='';
 	    	//print_r($data);die;
 	    	foreach($data['favoritevisitorshops'] as $key=>$value){
 	    	$this->view->favShopId.= $value['id'].',';
 	    }
 	   $this->view->favShopId = rtrim($this->view->favShopId,',');
 	}

 	/* Date of birth dropdown*/
  	$dataMonth='';
 	$dataDay='';
 	$dataYear='';

 	if(@$data['dateOfBirth']!=''){
 		list($dataYear, $dataMonth, $dataDay) = @split('[/.-]', $data['dateOfBirth']);
 	}
 	//echo $dob;die;
 	$year_limit = 0;
 	$html_output="";

 	/*days*/
 	$html_output .= '<select name="date_day" class="dateofbirth_visitor" id="day_select">'."\n";

 	for ($day = 1; $day <= 31; $day++) {
 		$select = ($day==$dataDay) ? "selected=selected" : "";
 		$html_output .= '<option '.$select.' value="'.$day.'">' . $day . '</option>'."\n";
 	}
 	$html_output .= '</select>'."\n";

 	/*months*/
 	$html_output .= '<select name="date_month" class="dateofbirth_visitor" id="month_select" >'."\n";
 	$months = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

 	for ($month = 1; $month <= 12; $month++) {
 		$select = ($month==$dataMonth) ? "selected=selected" : "";
 		$html_output .= '<option value="'.$month .'" '.$select.'>' . $months[$month] . '</option>'."\n";
 	}
 	$html_output .= '</select>'."\n";

 	/*years*/
 	$html_output .= '<select name="date_year" class="dateofbirth_visitor" id="year_select">'."\n";
 	for ($year = 1900; $year <= (date("Y") - $year_limit); $year++) {
 		$select = ($year==$dataYear) ? "selected=selected" : "";
 		$html_output .= '<option '.$select.' value="'.$year.'">' . $year . '</option>'."\n";
 	}
 	$html_output .= '</select>'."\n";
 	$this->view->dateofbirth = $html_output;


 	if ($this->getRequest()->isPost()) {
 		$params = $this->getRequest()->getParams();
 		//var_dump($params);
 		if ($params) {
 			$visitor = Visitor::updateVisitor($params , true);
 		}
 		$flash = $this->_helper->getHelper('FlashMessenger');
 		$message = $this->view->translate('Visitor details has been updated successfully.');
 		$flash->addMessage(array('success' => $message ));
 		$this->_redirect(HTTP_PATH.'admin/visitor#'.$params['qString']);
 	}
 }

    /**
     * Search top five visitors from database based on search text
     * @author mkaur
     */
    public function searchkeyAction(){
    	$srh = $this->getRequest()->getParam('keyword');
    	$for = $this->getRequest()->getParam('flag');
    	$data = Visitor::searchKeyword($for,$srh);
    	$ar = array();
    	if (sizeof($data) > 0) {
    		foreach ($data as $d) {
    			$ar[] = ucfirst($d['firstName']);
    		}
    	}
    	else{
    		$ar[]="No Record Found.";
    	}
    	echo Zend_Json::encode($ar);
    	die;
    }
    /**
     * Search top five visitors from database based on search text
     * @author kraj
     */
    public function searchemailsAction(){

    	$srh = $this->getRequest()->getParam('keyword');
    	$for = $this->getRequest()->getParam('flag');
    	$data = Visitor::searchEmails($for,$srh);
    	$ar = array();
    	if (sizeof($data) > 0) {
    		foreach ($data as $d) {
    			$ar[] = ucfirst($d['email']);
    		}
    	}
    	else{
    		$ar[]="No Record Found.";
    	}
    	echo Zend_Json::encode($ar);
    	die;
    }
    public function deletefavoriteshopAction(){

	    $params = $this->_getAllParams();
	   	$success = Visitor::delelteFav($params);
	   	echo Zend_Json::encode($success);
	    die();
    }

    public function importvisitorlistAction(){

    	ini_set('max_execution_time',115200);
    	$params = $this->_getAllParams();
    	if($this->getRequest()->isPost ()){
    		//echo "<pre>"; print_r($_FILES); die;
    		if (isset($_FILES['excelFile']['name']) && @$_FILES['excelFile']['name'] != '') {

    			$RouteRedirectObj = new RouteRedirect();
    			$result = @$RouteRedirectObj->uploadExcel($_FILES['excelFile']['name'], true);
                echo '<pre>'.print_r($result, true).'</pre>';
    			$excelFilePath = $result['path'];
				$excelFile = $excelFilePath.$result['fileName'];
                echo $excelFile;
                exit;

    			if($result['status'] == 200){




	    			$flash = $this->_helper->getHelper ( 'FlashMessenger' );
	    			$message = $this->view->translate ('Visitors uploaded successfully');
	    			$flash->addMessage ( array ('success' => $message ) );
	    			$this->_redirect ( HTTP_PATH . 'admin/visitor' );
    			}

		    } else{

		    	$flash = $this->_helper->getHelper ( 'FlashMessenger' );
	    		$message = $this->view->translate ('Problem in your file!!');
	    		$flash->addMessage ( array ('error' => $message ) );
	    		$this->_redirect ( HTTP_PATH . 'admin/visitor' );

		    		//return false;
		    }
    	}
    }

    /**
     * emptyXlx
     *
     * used to download empty xlsx file for visitor imports
     * @author Surinderpal Singh
     */
    public function emptyXlxAction() {

    	# set fiel and its trnslattions
    	$file =  APPLICATION_PATH . '/migration/empty_visitor.xlsx' ;
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

    /**
     * exportXlx
     *
     * used to download visitor export xlsx file of current locale
     * @author Surinderpal Singh
     */
    public function exportXlxAction() {

    	# set fiel and its trnslattions
    	$file =  UPLOAD_EXCEL_PATH . 'visitorList.csv' ;
    	$fileName =  $this->view->translate($file);

    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);

    	# set reponse headers and body
    	$this->getResponse()
    	->setHeader('Content-Disposition', 'attachment;filename=' . basename($fileName))
    	->setHeader('Content-type', 'text/csv')
        ->setHeader('Cache-Control', 'max-age=0')
        ->setHeader('Pragma', 'no-cache')
    	->setHeader('Expires', '0')
    	->setBody(file_get_contents($fileName));

    }


 }


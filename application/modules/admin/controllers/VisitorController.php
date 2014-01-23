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

		if($sessionNamespace->settings['rights']['administration']['rights'] != '1' &&
					$sessionNamespace->settings['rights']['administration']['rights'] != '2' )
		{
			$this->_redirect('/admin/auth/index');
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
     * Export user list in excel with users images
     * @author mkaur
     * @version 1.0
     *
     */
    public function exportvisitorlistAction() {

    	set_time_limit ( 10000 );
    	ini_set('max_execution_time',115200);
    	ini_set("memory_limit","1024M");

		$role =   Zend_Auth::getInstance()->getIdentity()->roleId;
		//get data from database (user table)
				$data = Doctrine_Query::create()
				->select('v.*,k.*,fv.shopId,sp.name')
				->from("Visitor v")
				->leftJoin('v.favoritevisitorshops fv')
				->leftJoin('fv.shops sp')
				->leftJoin('v.keywords k')
				->where('v.deleted=0')
				->orderBy("v.id DESC")->fetchArray();

		//echo "<pre>"; print_r($data); die;
		//CREATE A OBJECT OF PHPECEL CLASS
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', $this->view->translate('Name'));
		$objPHPExcel->getActiveSheet()->setCellValue('B1', $this->view->translate('Email'));
		$objPHPExcel->getActiveSheet()->setCellValue('C1', $this->view->translate('Gender'));
		$objPHPExcel->getActiveSheet()->setCellValue('D1', $this->view->translate('DOB'));
		$objPHPExcel->getActiveSheet()->setCellValue('E1', $this->view->translate('Postal Code'));
		$objPHPExcel->getActiveSheet()->setCellValue('F1', $this->view->translate('Weekly Newsletter'));
		$objPHPExcel->getActiveSheet()->setCellValue('G1', $this->view->translate('Fashion Newsletter'));
		$objPHPExcel->getActiveSheet()->setCellValue('H1', $this->view->translate('Travel Newsletter'));
		$objPHPExcel->getActiveSheet()->setCellValue('I1', $this->view->translate('Code Alert'));
		$objPHPExcel->getActiveSheet()->setCellValue('J1', $this->view->translate('Active'));
		$objPHPExcel->getActiveSheet()->setCellValue('K1', $this->view->translate('Keyword'));
		$objPHPExcel->getActiveSheet()->setCellValue('L1', $this->view->translate('Favorite Shops'));
		$objPHPExcel->getActiveSheet()->setCellValue('M1', $this->view->translate('Registration Date'));

		$column = 2;
		$row = 2;
		foreach ($data as $visitor) {

			$name  =  $visitor['firstName'] . " " . $visitor['lastName'];

			$gender = '';
			if($visitor['gender'] == 0){

				$gender = 'Male';

			}else{

				$gender = 'Female';
			}

			$dob = '';
			if($visitor['dateOfBirth'] != 'undefined'
					|| $visitor['dateOfBirth'] != null
					|| $visitor['dateOfBirth'] != '' ){
				$dob = $visitor['dateOfBirth'];
			}

			$postal = '';
			if($visitor['postalCode'] != 'undefined'
					|| $visitor['postalCode'] != null
					|| $visitor['postalCode'] != '' ){
				$postal = $visitor['postalCode'];
			}

			$weekNews = '';
			if($visitor['weeklyNewsLetter'] == 1 ){
				$weekNews = $this->view->translate('Yes');
			}else{
				$weekNews = $this->view->translate('No');
			}

			$fashionNews = '';
			if($visitor['fashionNewsLetter'] == 1 ){
				$fashionNews = $this->view->translate('Yes');
			}else{
				$fashionNews = $this->view->translate('No');
			}

			$travelNews = '';
			if($visitor['travelNewsLetter'] == 1 ){
				$travelNews = $this->view->translate('Yes');
			}else{
				$travelNews = $this->view->translate('No');
			}

			$codeAlert = '';
			if($visitor['codeAlert'] == 1 ){
				$codeAlert = $this->view->translate('Yes');
			}else{
				$codeAlert = $this->view->translate('No');
			}

			$active = '';
			if($visitor['active'] == 1 ){
				$active = $this->view->translate('Yes');
			}else{
				$active = $this->view->translate('No');
			}

			$keywords = '';
			if(!empty($visitor['keywords'])){
				$prefix = '';
				foreach ($visitor['keywords'] as $key)
				{
					$keywords .= $prefix  . $key['keyword'];
					$prefix = ', ';
				}
			}

			$favoritevisitorshops = '';
			if(!empty($visitor['favoritevisitorshops'])){
				$prefix = '';
				foreach ($visitor['favoritevisitorshops'] as $fav)
				{
					$favoritevisitorshops .= $prefix  . $fav['shops'][0]['name'];
					$prefix = ', ';
				}
			}

			$created_at = $visitor['created_at'];

			//SET VALUE IN CELL
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$column, $name);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$column, $visitor['email']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$column, $gender);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$column, $dob);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$column, $postal);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$column, $weekNews);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$column, $fashionNews);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$column, $travelNews);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$column, $codeAlert);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$column, $active);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$column, $keywords);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$column, $favoritevisitorshops);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$column, $created_at);
			//$objPHPExcel->getActiveSheet()->setCellValue('E'.$column, '35');


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

		$objPHPExcel->getActiveSheet()->getStyle('A1:'.'M1')->applyFromArray($headerStyle);

		//SET ALIGN OF TEXT
		$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B2:M'.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

		//BORDER TO CELL
		//$objPHPExcel->getActiveSheet()->getStyle('A1:'.'E1')->applyFromArray($borderStyle);
		$borderColumn =  (intval($column) -1 );
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.'M'.$borderColumn)->applyFromArray($borderStyle);

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

		//$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		// redirect output to client browser
		$fileName =  $this->view->translate('VisitorList.xlsx');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$fileName);
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		die();

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
    	if($this->getRequest ()->isPost ()){
    		//echo "<pre>"; print_r($_FILES); die;
    		if (isset($_FILES['excelFile']['name']) && @$_FILES['excelFile']['name'] != '') {

    			$RouteRedirectObj = new RouteRedirect();
    			$result = @$RouteRedirectObj->uploadExcel($_FILES['excelFile']['name']);
    			$excelFilePath = $result['path'];
				$excelFile = $excelFilePath.$result['fileName'];



    			if($result['status'] == 200){

    			$objReader = PHPExcel_IOFactory::createReader('Excel2007');
    			$objPHPExcel = $objReader->load($excelFile);
    			$worksheet = $objPHPExcel->getActiveSheet();

    			$data =  array();
    			$emailArray = array();
    			$i = 0;


    			$insert = new Doctrine_Collection('Visitor');


    			foreach ($worksheet->getRowIterator() as $row) {


		    			$cellIterator = $row->getCellIterator();
		    			$cellIterator->setIterateOnlyExistingCells(false);

		    			foreach ($cellIterator as $cell) {
		    				$data[$cell->getRow()][$cell->getColumn()] = $cell->getCalculatedValue();
		    			}

		    			if($i > 0){


			    			$email =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['A']);

			    			$firstName =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['B']);

			    			$lastName =  BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['C']);


			    			$gender = $data[$cell->getRow()]['D'];

			    			if( strtoupper($gender) == 'F' || strtoupper($gender) == 'FEMALE')
			    			{
			    				$gender = 1 ;
			    			} else {

			    				$gender = 0 ;
			    			}


			    			$dob =  PHPExcel_Style_NumberFormat::toFormattedString($data[$cell->getRow()]['E'], "YYYY-MM-DD");
			    			$dob  = date('Y-m-d',strtotime($dob));



			    			$date = PHPExcel_Style_NumberFormat::toFormattedString($data[$cell->getRow()]['F'], "YYYY-MM-DD h:mm:ss");


			    			if($date)
			    			{
			    				$created_at = date('Y-m-d H:i:s',strtotime($date));

			    			} else{

			    				$created_at = date('Y-m-d H:i:s');
			    			}


			    			$keywords = BackEnd_Helper_viewHelper::stripSlashesFromString($data[$cell->getRow()]['G']);

			    			$emailExist = Doctrine_Core::getTable('Visitor')->findBy('email', $email)->toArray();

			    			if(empty($emailExist)){


			    				/**
			    				 * use email as index to avoid duplicate email error in query
			    				 * as email would be always unique
			    				 */
	    						$insert[$email]->firstName = $firstName;
	    						$insert[$email]->lastName = $lastName;
	    						$insert[$email]->created_at = $created_at;
	    						$insert[$email]->email = $email;
	    						$insert[$email]->gender = $gender ;
	    						$insert[$email]->dateOfBirth = $dob ;
	    						$insert[$email]->weeklyNewsLetter = 1;
	    						$insert[$email]->password = BackEnd_Helper_viewHelper::randomPassword();
	    						$insert[$email]->active = 1;

	    						$kw = explode(',',$keywords);
	 			    			foreach ($kw as $words){
	  			    				$insert[$email]->keywords[]->keyword = $words;
	 				    		}

			    			} else {


			    				$insertKeyword = new Doctrine_Collection('VisitorKeyword');


			    				$updateWeekNews = Doctrine_Query::create()->update('Visitor')
			    														  ->set('weeklyNewsLetter',1)
			    														  ->set('firstName','?' , $firstName )
			    														  ->set('lastName', '?' ,$lastName)
			    														  ->set('created_at', '?' , $created_at)
			    														  ->set('dateOfBirth','?',$dob)
			    														  ->set('gender', '?', $gender)
			    														  ->set('active','?',1)
			    				                                          ->where('id = '.$emailExist[0]['id'])
			    														  ->execute();
			    				$j = 0;
			    				$kw = explode(',',$keywords);
	 			    			foreach ($kw as $words) {

			    					$keywordExist = Doctrine_Query::create()->from('VisitorKeyword')
			    														  ->where("keyword = '". $words ."'")
			    														  ->andWhere('visitorId = '.$emailExist[0]['id'])
			    														  ->fetchOne(null,Doctrine::HYDRATE_ARRAY);

			    					if(empty($keywordExist)) {
		 			    				$insertKeyword[$j]->keyword = $words;
		  			    				$insertKeyword[$j]->visitorId = $emailExist[0]['id'];
			    					}

			    					$j++;
	  			    			}

	  			    			$insertKeyword->save();
			    			}
		    			}
		    			$i++;

	    			}

	    			//check for emails from DB to EXCEL.
	    			$emailFromDb = Doctrine_Query::create()->select('email,created_at')->from('Visitor')->fetchArray();


	    			$insert->save();


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
    	$file =  UPLOAD_EXCEL_PATH . 'visitorList.xlsx' ;
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


 }


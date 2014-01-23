<?php


/**
 * ConversionController
 * 
 * This controller is used to store conversion for every outgoing link
 * and update google analytics
 * 
 * @author Surinderpal Singh
 *
 */
class ConversionController extends Zend_Controller_Action {
	
	/**
	 * index action 
	 * It is used to save the conversion and update goolge anlytics data based on post from 
	 * affliate netwotrk. It executed only when affliate network post.
	 * @author Surinderpal Singh
	 */	
	public function indexAction() {

		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		
		
		
		# Get required variables from the URL
		$subid = $this->getRequest()->getParam("subid", false);
		if(! $subid)
		{
			$this->_helper->redirector->setCode(301);
			$this->_redirect(HTTP_PATH_LOCALE);
		}
		
		
		$var_utmhn = $this->view->translate('www.kortingscode.nl'); //enter your domain
		$utmac = $this->view->translate('UA-17691171-1');
		
		
		# get request uri and make complete requrest url
		$uri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
		$urltoWrite =  trim(HTTP_PATH, '/') . $uri ;
		
		# log directory path
		$logDir = APPLICATION_PATH . "/../logs/";
		
		
		# cretae directory if it isn't exists an dwrite log file
		if(!file_exists( $logDir  ))
		mkdir( $logDir , 776, TRUE);
		
		# read cookie values from database 
		$data = Conversions::getConversionDetail($subid);
		
		$fileName = $logDir  . 'conversion';
		
		if($data)
		{
			
			$network = "" ;
			if(isset($data['offer']['shop']['affliatenetwork']))
			{
				$network =  $data['offer']['shop']['affliatenetwork']['name'];
			}
			
			
			# please avoid to format below template
$log = <<<EOD
$network; Incoming; $urltoWrite
EOD;
			
			
			FrontEnd_Helper_viewHelper::writeLog($log , $fileName ) ;
			
			
			# convert a conversion
			Conversions::updateConverted($subid);
			
			
			# Order ID - unique ID for the  transaction
			$orderid = $this->getRequest()->getParam("orderid", false);
			
			# Total and unit price - affiliate fee for the sale
			$total = $this->getRequest()->getParam("total", false);

			# product code
			$sku = $this->getRequest()->getParam("sku", "");
			
			# in case you didn't provide any product code It'll just take "1"..
			if ($sku == "") {
				$sku = "1";
			} 
			  
			# Get some optional variables from the URL
			
			# Affiliation or store name - name of
			# the business your promoting (e.g."WP4FB")
			$storename = $this->getRequest()->getParam("storename", "");
			$tax = $this->getRequest()->getParam("tax", "");
			$shipping = $this->getRequest()->getParam("shipping", "");
			$city =  $this->getRequest()->getParam("city", "");
			$region =  $this->getRequest()->getParam("region", "");
			$country = $this->getRequest()->getParam("country", "");
			$productname = $this->getRequest()->getParam("productname", "");
			$category = $this->getRequest()->getParam("category", "");
			$utmipc = $sku; # Product code / SKU
			$utmipn = $productname; # Product name
			$utmipr = $total; # Unit price
			$utmiqt = '1'; # Unit quantity
			
			# Tax
			if ($tax != "") {
				$utmttx = $tax;
			} else {
				$utmttx = '0.0';
			}
			
			# Shipping cost
			if ($shipping != "") {
				$utmtsp = $shipping;
			} else {
				$utmtsp = '0.0';
			}
			 
			# Billing city
			if ($city != "") {
				$utmtci = $city;
			} else {
				$utmtci = 'na';
			}
			
			# Billing region
			if ($region != "") {
				$utmtrg = $region;
			} else {
				$utmtrg = 'na';
			}
			
			# Billing country
			if ($country != "") {
				$utmtco = $country;
			} else {
				$utmtco = 'na';
			}
			
			if ($tax != "") {
				$utmttx = $tax;
			} else {
				$utmttx = '0.0';
			}
			
			# Page request of the current page	 (required, no need to change this)
			
			$utmp = 'leadtracking.php'; 
			$utmiva = '';
			
			# random request number to prevent caching
			$var_utmn = rand ( 1000000000, 9999999999 );
			 
			# random request number to  prevent caching
			$var_utmn2 = rand ( 1000000000, 9999999999 );
			 
			# random request number to prevent caching
			$var_utmn3 = rand ( 1000000000, 9999999999 ); 
			
			# random request number to prevent caching
			$utmhid = rand ( 1000000000, 9999999999 ); 
			
			# Looks up the original cookie in the DB
			$utma = $data['utma'];
			$utmz = $data['utmz'] ;
			
			$utmipn = str_replace(" ","%20",$utmipn);
			
			$utma = str_replace(" ","%20",$utma);
			$utma = str_replace("|","%7C",$utma);
			
			$utmz = str_replace(" ","%20",$utmz);
			$utmz = str_replace("|","%7C",$utmz);
			
			# Simulates E-Commerce Sale
			$transactionUrl = 'http://www.google-analytics.com/__utm.gif?utmwv=4.6.5&utmn='.$var_utmn
						.'&utmt=tran&utmtid='.$orderid.'&utmtst='.$storename
						.'&utmtto='.$utmipr.'&utmttx='.$utmttx.'&utmtsp='.$utmtsp.'&utmtci='.$utmtci
						.'&utmtrg='.$utmtrg.'&utmtco='.$utmtco
						.'&utmcc=__utma%3D'.$utma.'%3B%2B__utmz%3D'.$utmz.'%3B' .'&utmac='.$utmac.'&utmhn='.$var_utmhn;
			
			$itemUrl = 'http://www.google-analytics.com/__utm.gif?utmwv=4.6.5&utmn='.$var_utmn2
						.'&utmt=item&utmtid='.$orderid.'&utmipc='.$utmipc
						.'&utmipn='.$utmipn.'&utmiva='.$utmiva.'&utmipr='.$utmipr
						.'&utmiqt='.$utmiqt.'&utmcc=__utma%3D'
						.$utma.'%3B%2B__utmz%3D'.$utmz.'%3B'.'&utmac='.$utmac.'&utmhn='.$var_utmhn;
			
			$urchinUrl ='http://www.google-analytics.com/__utm.gif?utmwv=4.6.5&utmn='.$var_utmn3
						.'&utmp='.$utmp.'&utmcc=__utma%3D'
						.$utma.'%3B%2B__utmz%3D'.$utmz.'%3B'.'&utmac='.$utmac.'&utmhn='.$var_utmhn;
			
			

			# add  $transactionUrl into log file
			
			# please avoid to format below template
$trLog = <<<EOD
$network; Outgoing-transactionUrl; $transactionUrl
EOD;
			FrontEnd_Helper_viewHelper::writeLog($trLog , $fileName ) ;
			
			

			# add $itemUrl into log file
				
			# please avoid to format below template
$itemLog = <<<EOD
$network; Outgoing-itemUrl; $itemUrl
EOD;
			FrontEnd_Helper_viewHelper::writeLog($itemLog , $fileName ) ;
		
			
			
			# add $urchinUrl into log file
			
			# please avoid to format below template
$urLog = <<<EOD
$network; Outgoing-urchinUrl; $urchinUrl
EOD;
			FrontEnd_Helper_viewHelper::writeLog($urLog , $fileName ) ;
			
			
						
			
			$handle = fopen ($transactionUrl, "r");
			$trancsaction = fgets($handle);
			
			$handle2 = fopen ($itemUrl, "r");
			$item = fgets($handle2);
			
			$handle3 = fopen ($urchinUrl, "r");
			$urchin = fgets($handle3);
			
			fclose($handle);
			fclose($handle2);
			fclose($handle3);
		}else {
			
			
			# please avoid to format below template
			$log = <<<EOD
Invalid url; Incoming; $urltoWrite
EOD;
				
			FrontEnd_Helper_viewHelper::writeLog($log , $fileName ) ;
		
			$this->_helper->redirector->setCode(301);
			$this->_redirect(HTTP_PATH_LOCALE);
		}
	
	}

}


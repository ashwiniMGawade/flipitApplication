<?php

class MandrillController extends Zend_Controller_Action {

	public $dataShopName = array();
	public $dataOfferName = array();
	public $dataShopImage = array();
	public $expDate = array();
	public $shopPermalink = array();
	
	public $category = array();
	public $dataShopNameCat = array();
	public $dataOfferNameCat = array();
	public $dataShopImageCat = array();
	public $expDateCat = array();
	public $shopPermalinkCat = array();
	
	public $headerMail = array();
	public $loginLink = array();
	
	/**
	 * override views based on modules if exists
	 * @see Zend_Controller_Action::init()
	 * @author Bhart
	 */
	public function init() {

		$module   = strtolower($this->getRequest()->getParam('lang'));
		$controller = strtolower($this->getRequest()->getControllerName());
		$action     = strtolower($this->getRequest()->getActionName());

		# check module specific view exists or not
		if (file_exists (APPLICATION_PATH . '/modules/'  . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")){
			
			# set module specific view script path
			$this->view->setScriptPath( APPLICATION_PATH . '/modules/'  . $module . '/views/scripts' );
		}
		else{
			
			# set default module view script path
			$this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
		}
	}

	public function indexAction() {
		
		$topVouchercodes = FrontEnd_Helper_viewHelper::gethomeSections("popular", 10);
		$topCategories = array_slice(FrontEnd_Helper_viewHelper::gethomeSections("category", 10),0,1);
		
		$this->getTopVouchercodesData($topVouchercodes);
		$this->getVouchercodesOfCategories($topCategories);
		$this->getDirectLoginLinks();
		
		$this->headerMail = array(array('name' => 'headerMail', 'content' => "<img src='".HTTP_PATH_CDN."images/HeaderMail.gif'/>"));
		
		$data = array_merge($this->dataShopName, $this->dataOfferName, 
							$this->dataShopImage, $this->expDate,
							$this->headerMail, $this->dataShopNameCat,
							$this->dataOfferNameCat, $this->dataShopImageCat,
							$this->expDateCat, $this->category
						);
		
		$dataPermalink = array_merge($this->shopPermalink, $this->shopPermalinkCat);
		//echo "<pre>"; print_r($data); die;
			
		$mandrill = new Mandrill_Init( $this->getInvokeArg('mandrillKey'));
		$template_name = 'newsletter';
		$template_content = $data;
		$message = array(
				'subject'    => 'Some nice subject',
				'from_email' => 'rhyme2chetan@gmail.com',
				'from_name'  => 'Test',
				'to'         => array(
						array(
								'email' => 'cbhopal@seasiaconsulting.com',
								'name'  => 'Chetan'
						)
				),
				'inline_css' => true,
				"global_merge_vars" => $dataPermalink
		);
		
		$mandrill->messages->sendTemplate($template_name, $template_content, $message);
		echo "Mail sent";
		die;
		
	}

	public function getTopVouchercodesData($topVouchercodes)
	{
		foreach ($topVouchercodes as $key => $value) {
			$this->dataShopName[$key]['name'] = "shopTitle_".($key+1);
			$this->dataShopName[$key]['content'] = $value['offer']['shop']['name'];
			
			$this->dataOfferName[$key]['name'] = "offerTitle_".($key+1);
			$this->dataOfferName[$key]['content'] = $value['offer']['title'];
			
			if(count($value['offer']['shop']['logo']) > 0):
				$img = PUBLIC_PATH_CDN.$value['offer']['shop']['logo']['path'].'thum_medium_store_'. $value['offer']['shop']['logo']['name'];
				
			else:
				$img = HTTP_PATH_CDN."images/NoImage/NoImage_200x100.jpg";
			endif;
			
			$this->dataShopImage[$key]['name'] = 'shopLogo_'.($key+1);
			$this->dataShopImage[$key]['content'] = "<img src='$img'>";
			
			//echo "<pre>"; print_r($value); die;
			$expiryDate = new Zend_Date($value['offer']['endDate']);
			$this->expDate[$key]['name'] = 'expDate_'.($key+1);
			$this->expDate[$key]['content'] = $this->view->translate('Verloopt op:') ." ". $expiryDate->get(Zend_Date::DATE_MEDIUM);
			
			$this->shopPermalink[$key]['name'] = 'shopPermalink_'.($key+1);
			$this->shopPermalink[$key]['content'] = HTTP_PATH . $value['offer']['shop']['permaLink'].
				'?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y') ;
		}
	}
	
	public function getVouchercodesOfCategories($topCategories)
	{
		if(count($topCategories[0]['category']['categoryicon']) > 0):
			$img = PUBLIC_PATH_CDN.$topCategories[0]['category']['categoryicon']['path'].'thum_medium_store_'. $topCategories[0]['category']['categoryicon']['name'];
			
		else:
			$img = HTTP_PATH_CDN."images/NoImage/NoImage_70x60.png";
		endif;
		$this->category = array(array('name' => 'categoryImage', 
									  'content' => "<img src='".$img."'/>"
									 ),
								array('name' => 'categoryName',
									  'content' => 'Populairste categorie: ' . $topCategories[0]['category']['name']
								)
							   );
		
		$vouchers = array_slice(Category::getCategoryVoucherCodes($topCategories[0]['categoryId']),0,3);
		//echo "<pre>"; print_r($vouchers); die;
				
		foreach ($vouchers as $key => $value) {
			$this->dataShopNameCat[$key]['name'] = "shopTitleCat_".($key+1);
			$this->dataShopNameCat[$key]['content'] = $value['shop']['name'];
				
			$this->dataOfferNameCat[$key]['name'] = "offerTitleCat_".($key+1);
			$this->dataOfferNameCat[$key]['content'] = $value['title'];
				
			if(count($value['shop']['logo']) > 0):
					$img = PUBLIC_PATH.$value['shop']['logo']['path'].'thum_medium_store_'. $value['shop']['logo']['name'];
				
			else:
				$img = HTTP_PATH_CDN."images/NoImage/NoImage_200x100.jpg";
			endif;
				
			$this->dataShopImageCat[$key]['name'] = 'shopLogoCat_'.($key+1);
			$this->dataShopImageCat[$key]['content'] = "<img src='$img'>";
				
			$expiryDate = new Zend_Date($value['endDate']);
			$this->expDateCat[$key]['name'] = 'expDateCat_'.($key+1);
			$this->expDateCat[$key]['content'] = $this->view->translate('Verloopt op:') ." ". $expiryDate->get(Zend_Date::DATE_MEDIUM);
			
			$this->shopPermalinkCat[$key]['name'] = 'shopPermalinkCat_'.($key+1);
			$this->shopPermalinkCat[$key]['content'] = HTTP_PATH_LOCALE . $value['shop']['permalink'].
				'?utm_source=transactional&utm_medium=email&utm_campaign='.date('d-m-Y') ;
		}
	}
	
	public function getDirectLoginLinks()
	{
		$this->loginLink = array(array('name' => 'loginLink', 'content' => HTTP_PATH_LOCALE."login"));
		
	}
}

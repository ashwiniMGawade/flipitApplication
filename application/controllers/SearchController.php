<?php

class SearchController extends Zend_Controller_Action {

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
		
		
		# get cononical link
		$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
		$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononicalForSearch($permalink) ;
		
		$pageId = 36;
		$this->pageDetail = Page::getPageFromPageAttr($pageId);
		$this->view->pageTitle = @$this->pageDetail->pageTitle;
		
		if($this->pageDetail->customHeader)
		{
			$this->view->layout()->customHeader = "\n" . $this->pageDetail->customHeader;
		}
		
		$this->view->headTitle(@$this->pageDetail->metaTitle);
		$this->view->headMeta()->setName('description', @trim($this->pageDetail->metaDescription));
		//echo $this->getRequest()->getParam('searchField');
		//die();
		$this->view->flag=1;
		$search = $this->getRequest()->getParam('searchField');
		if($search=="" || $search==null){
			$this->view->flag=0;
		}else{
			$this->view->keyword = $search;
		}
		
		$link =  explode('/',ltrim($_SERVER['REQUEST_URI'],'/'));
		$this->view->searchtype = @$link[1];
		$getMoneySaving = MoneySaving::getAllMoneySavingArticleForSearch($this->getRequest()->getParam('searchField'),6);
		
		$suggestions = Offer::searchRelatedOffers($this->_getAllParams());
		
		$searchBarExclusion =  ExcludedKeyword::getKeywordForFront($search);
		if(!empty($searchBarExclusion)):
			
			if($searchBarExclusion[0]['action'] == 0):
				
				$url = $searchBarExclusion[0]['url'];
				$this->_redirect($url);
				exit();

			else:
				$shopsIds = array();
				foreach($searchBarExclusion[0]['shops'] as $shops):
					
					$shopsIds[] = $shops['shopsofKeyword'][0]['id'];
					
				endforeach;
			endif;
		endif;
		
		if(!empty($shopsIds)):
			$exclusiveShops = Shop::getShopsExclusive($shopsIds);
			$exShops = array();
			foreach($exclusiveShops as $eShops):
			
				$exShops["'".$eShops['id']."'"] = $eShops;
			
			endforeach;
		endif;
		
		$popularStore = Shop::getAllStoresForSearch($this->getRequest()->getParam('searchField'),8);
		$popularShops = array();
		foreach($popularStore as $popShops):
			
			$popularShops["'".$popShops['id']."'"] = $popShops;
			
		endforeach;
		
		$newArrayShops = array();
		
		if(!empty($exShops)):
			if(!empty($popularShops)):
				$newArrayShops = array_merge($exShops, $popularShops);
			else:
			
				$newArrayShops = $exShops;
			endif;
		else: 
			if(!empty($popularShops)):
				$newArrayShops = $popularShops;
			endif;
		endif;
		
		//make array usable for view
		$newArrShops = array();
		
		foreach($newArrayShops as $nas):
			
			$newArrShops[] = $nas;
			
		endforeach;
		
		$lastFinalArrayShops = array_slice($newArrShops, 0,8);
		$shopsIds = "";
		$result = Offer::searchOffers($this->_getAllParams(),$shopsIds, 12);
		$this->view->paginatorCodes = $result;
		$this->view->popularstores  =@$lastFinalArrayShops;//show popular store
		$this->view->suggestion = @$suggestions;
		$this->view->gudes = @$getMoneySaving;
		$this->view->controllerName = $this->getRequest()->getControllerName();
		$this->view->param = $search;
	}

/**

* This is the suggestion action and is used to retrieve four search results from database

* according to given search query

* @return $suggestions array

* @author cbhopal

* @version 1.0

*/

public function suggestionAction(){ 
	# get cononical link
	$permalink = ltrim(Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(), '/');
	$this->view->canonical = FrontEnd_Helper_viewHelper::generatCononicalForSearch($permalink) ;
	

	$this->view->suggestion = Offer::searchRelatedOffers($this->_getAllParams());

	$this->view->controllerName = $this->getRequest()->getControllerName();
}

}


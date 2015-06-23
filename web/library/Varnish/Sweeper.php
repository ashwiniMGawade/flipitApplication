<?php 
/*
		This Sweeper script is used to delete chached pages from varnish
			Example of how to use this Sweeper
    	$varnish = new Varnish_Sweeper();
    	$varnish->clearCategories();
*/
class Varnish_Sweeper{

	//private $liveServerIP = '141.138.196.192';
	private $liveServerIP = '127.0.0.1';
	private $url 					= 'http://www.kortingscode.nl';

	/* TODO Clear pages for an Offer */
	public function clearOfferPages(){
		$pages = array('http://www.kortingscode.nl/', 'http://www.kortingscode.nl/nieuw');
	}


	public function clearCategories(){
		if ( $this->checkIP() ) {
    	$catObj 		= new Category();
    	$categories = $catObj->getAllCategories();
    	if (!empty($categories)) {
    		foreach ($categories as $category) {
    			$this->refresh( $category['permaLink'] );
    		}
    	}
		}
	}

	/* 
		 this method clears the url from Varnish and also calls the same url again to set the url in Varnish
		 @param $link is the permalink part, the query part of the url
	*/
	private function refresh($link){
		$url = $this->url.'/'.$link;
		// preform the PURGE for this url
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PURGE");
		curl_setopt($curl,  CURLOPT_RETURNTRANSFER, true);
		curl_exec($curl);
		curl_close($curl);
		// recall the url to bring it back in Varnish
		$curl = curl_init($url);
		curl_setopt($curl,  CURLOPT_RETURNTRANSFER, true);
		curl_exec($curl);
		curl_close($curl);
	}

	// check if the request is from the live server. We don't clear cache from development servers.
	private function checkIP(){
		if ($_SERVER['REMOTE_ADDR'] == $this->liveServerIP) {
			return true;
		}else{
			return false;
		}
	}

}

?>
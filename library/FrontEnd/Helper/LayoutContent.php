<?php
class FrontEnd_Helper_LayoutContent {
	
	#################################################################
	#################### REFACTORED CODE ##############################
	#################################################################
	public static function loadFlipitHomePage($flipitUrl) {
		$htmlPath = '';
		$flipit = new Zend_View();
		$flipit->setBasePath(APPLICATION_PATH . '/modules/flipit/views');
	
		if($flipitUrl == 'http://www.flipit.com'
				|| $flipitUrl == 'flipit.com'
				|| $flipitUrl =='http://flipit.com') :
	
				zend_Controller_Front::getInstance()->getRequest()->getControllerName() == 'index'
				? $htmlPath = 'index/index.phtml'
				: $htmlPath =  'error/error.phtml';
			
		return $flipitHomePage = array('obj'=>$flipit,'path'=>$htmlPath);
		endif;
	}
	
	public static function loadCanonical($canonical) {
		$canonicalUrl = '';
		if(isset($canonical)):
	
			if($canonical=='' || $canonical==null):
			    $canonicalUrl =  array('rel' => 'canonical','href' => rtrim(HTTP_PATH, '/'));
			else:
			    $canonicalUrl =  array('rel' => 'canonical','href' => HTTP_PATH  . strtolower($canonical));
			endif;
	
		endif;
		return $canonicalUrl ;
	}
	
	public static function loadRobots($page, $robotOfDummyPages) {
		$robots = '';
	
		if(isset($page) && $page != ''
				&& zend_Controller_Front::getInstance()
				->getRequest()->getControllerName() == 'search'):
	
				$robots  = 'noindex, follow';
	
		elseif (strtolower( 
				zend_Controller_Front::getInstance()
				->getRequest()->getControllerName()) == 'login'
				&& strtolower(
						zend_Controller_Front::getInstance()
						->getRequest()->getActionName() == 'forgotpassword'
						)
				):
	
				$robots =  'noindex, follow';
			
		else:
			#add noindex for every page after first page
			if(Zend_Controller_Front::getInstance()->getRequest()->getParam('page' , null) > 1) :
		
			    $robots  = 'noindex, follow';
				
			else:
				# robot keyword property is set by any action
				if($robotOfDummyPages) :
					
				    $robots  =  $robotOfDummyPages;
			
				else :
					
				    $robots  =  'index, follow';
					
				endif;
			endif;
		endif;
	
		return $robots;
	}
	
	public static function loadGoogleAnalyticsCode() {
	
		$googleAnalyticsCode = '';
	
		if(strtolower(zend_Controller_Front::getInstance()
				->getRequest()->getControllerName()) == 'error') :
				
			$googleAnalyticsCode = "<script type='text/javascript'>
				
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			
		    ga('create', 'UA-17691171-4', 'kortingscode.nl');
		    ga('send', 'pageview');
		    ga('send', 'event', 'error', '404', 'page:ref' , document.location.pathname
		   + document.location.search + ':' + document.referrer  );
			
		   </script>";
				
		 else :
				
			$googleAnalyticsCode = 	"<script type='text/javascript'>
	
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
		    ga('create', 'UA-17691171-4', 'kortingscode.nl');
		    ga('send', 'pageview');
	
		    var _gaq = _gaq || [];
		   _gaq.push(['_setAccount', 'UA-17691171-1']);
		   _gaq.push(['_trackPageview']);
	
		   (function()
		   { var ga = document.createElement('script'); ga.type = 'text/javascript';
		   ga.async = true; ga.src = ('https:' == document.location.protocol ?
		   'https://' : 'http://')
		   + 'stats.g.doubleclick.net/dc.js'; var s = document.getElementsByTagName('script')[0];
		   s.parentNode.insertBefore(ga, s); }
		   )();
		   </script>";
		 
	  endif;
	  
	  return $googleAnalyticsCode;
	}
	#################################################################
	#################### END REFACTORED CODE ##########################
	#################################################################
}

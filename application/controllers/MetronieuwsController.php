<?php

class MetronieuwsController extends  Zend_Controller_Action
{

    public function init()
    {
    /* Initialize action controller here */
    //	$this->_helper->layout()->disableLayout();
    }

    public function top10XmlAction()
    {

    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);

    	# fetch 10 Popular voucher offers for http://www.metronieuws.nl

   		$topVouchercodes = PopularCode::gethomePopularvoucherCodeForMarktplaatFeeds(10);


                    # if top korting are less than 20 then add newest code to fill up the list upto 20
        if(count($topVouchercodes) < 10 )
         {
            # the limit of popular oces
            $additionalCodes = 10 - count($topVouchercodes) ;

            # GET TOP 5 POPULAR CODE
            $additionalTopVouchercodes = $offers = Offer::commongetnewestOffers('newest', $additionalCodes);


            foreach ($additionalTopVouchercodes as $key => $value) {

                $topVouchercodes[] =     array('id'=> $value['shop']['id'],
                                                'permalink' => $value['shop']['permalink'],
                                                'offer' => $value
                                              );
            }
         }



    	$domain1 = $_SERVER['HTTP_HOST'];
    	$domain = 'http://'.$domain1;

    	# set locale
    	$locale = '';
    	if(isset($_COOKIE['locale']) && ($_COOKIE['locale']) != 'en') {
			$locale = '/' . $_COOKIE['locale'];
    	}

    	#  doamin path with locale
    	$domainPath = $domain . $locale;

    	// Create the RSS array
    	$title =  $this->view->translate('Kortingscode.nl populairste kortingscodes') ;
        $link = $domainPath ;
    	$desc  = $this->view->translate('Populairste kortingscodes') ;

    	$xml = new XMLWriter();

    	// Output directly to the user
    	$xml->openURI('php://output');
    	$xml->startDocument('1.0');
    	$xml->setIndent(2);

    	//channel
    	$xml->startElement("channel");

    	//title, desc, link, date
    	$xml->writeElement('title', $title);
    	$xml->writeElement('description', $desc);
    	$xml->writeElement('link', $link );
    	$xml->writeElement('language', 'nl');

    	// Cycle through the rankings, creating an array storing
    	// each, and push the array onto the $entries array

    	foreach ($topVouchercodes as  $offer) {

	    	$offerData = $offer['offer'] ;

 	    	//item !
	    	$xml->startElement("item");
	    	$xml->writeElement('shopname', $offerData['shop']['name']);
            if(mb_strlen($offerData['title'], 'UTF-8') > 42) {
                $xml->writeElement('title', mb_substr($offerData['title'], 0,42,  'UTF-8')."...");
            } else {
                $xml->writeElement('title', $offerData['title']);
            }
	    	$xml->writeElement('link', $domainPath . '/' . $offerData['shop']['permaLink']);
	    	$xml->endElement();

    	}

    	$xml->writeElement('More', 'nl');
    	$xml->writeElement('moreLink', $link);

    	//end channel
    	$xml->endElement();

    	//end doc
    	$xml->endDocument();
    	//flush
    	$xml->flush();

  		$this->_response->setHeader('Content-Type', 'text/xml; charset=utf-8'); 

    }

}


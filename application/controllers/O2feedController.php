<?php

class O2feedController extends  Zend_Controller_Action
{

    public function init()
    {
   
    }

    public function top10XmlAction()
    {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        # fetch 10 Popular voucher offers for http://www.metronieuws.nl
        $topVouchercodes = \KC\Repository\PopularCode::gethomePopularvoucherCodeForMarktplaatFeeds(9);
        $topVouchercodes =  \FrontEnd_Helper_viewHelper::fillupTopCodeWithNewest($topVouchercodes, 9);
        $domain1 = $_SERVER['HTTP_HOST'];
        $domain = 'http://'.$domain1;
        $locale = LOCALE;
        $domainPath = $domain . '/' . $locale;

        // Create the RSS array
        $title =  $this->view->translate('Flipit.com/pl populairste kortingscodes') ;
        $link = $domainPath ;
        $desc  = $this->view->translate('Populairste kortingscodes') ;

        $xml = new XMLWriter();
        $xml->openURI('php://output');
        $xml->startDocument('1.0');
        $xml->setIndent(2);
        $xml->startElement("channel");

        $lang = str_replace("_", "-", Zend_Registry::get('Zend_Locale'));
        $xml->writeElement('title', $title);
        $xml->writeElement('description', $desc);
        $xml->writeElement('link', $link);
        $xml->writeElement('language', $lang);

        // Cycle through the rankings, creating an array storing
        // each, and push the array onto the $entries array
        foreach ($topVouchercodes as $offer) {

            $offerData = $offer['offer'] ;
            $xml->startElement("item");
            $xml->writeElement('shopname', $offerData['shop']['name']);
            $xml->writeElement('title', $offerData['title']);
            $xml->writeElement('link', $domainPath . '/' . $offerData['shop']['permaLink']);
            $xml->endElement();
        }

        $xml->writeElement('More', 'nl');
        $xml->writeElement('moreLink', $link);
        $xml->endElement();
        $xml->endDocument();
        $xml->flush();
        $this->_response->setHeader('Content-Type', 'text/xml; charset=utf-8');
    }
}

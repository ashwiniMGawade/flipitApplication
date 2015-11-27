<?php

/**
 * Rss feeds  Generation
 *
 * @author Surinderpal Singh
 *
 */
class Admin_RssController extends Application_Admin_BaseController
{
    public function init()
    {
        ini_set ("display_errors", "1");
        error_reporting(E_ALL);

            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $flash->getMessages();
            $this->view->messageSuccess = isset($message[0]['success']) ?
            $message[0]['success'] : '';
            $this->view->messageError = isset($message[0]['error']) ?
            $message[0]['error'] : '';
    }

    /**
     * check authentication before load the page
     * @see Zend_Controller_Action::preDispatch()
     * @author Kraj
     * @version 1.0
     */
    public function preDispatch()
    {
        $conn2 = \BackEnd_Helper_viewHelper::addConnection (); // connection
        $params = $this->_getAllParams ();
        if (! \Auth_StaffAdapter::hasIdentity ()) {
            $referer = new Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->_redirect ( '/admin/auth/index' );
        }
        \BackEnd_Helper_viewHelper::closeConnection ( $conn2 );
        $this->view->controllerName = $this->getRequest ()->getParam ( 'controller' );
        $this->view->action = $this->getRequest ()->getParam ( 'action' );

    }

    public function indexAction()
    {
    }

    public function newOffersAction()
    {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_getAllParams ();

        $offers = \KC\Repository\Offer::getNewestOffersForRSS();

        $domain1 = $_SERVER['HTTP_HOST'];
        $domain = 'http://www.'.$domain1;

        # set locale
        $locale = '';
        if (isset($_COOKIE['locale']) && ($_COOKIE['locale']) != 'en') {
            $locale = '/' . $_COOKIE['locale'];
        }

        #  doamin path with locale
        $domainPath = $domain . $locale;

        // Create array to store the RSS feed entries
        $entries = array();

        // Cycle through the rankings, creating an array storing
        // each, and push the array onto the $entries array
        foreach ($offers as  $offer) {

            $entry = array(
                            'title'       => $offer['title'] ,
                            'link'        => $domainPath . '/' . $offer['permalink'],
                            'description' => "{$offer['terms']}",
                            'guid' => $offer['id']
                    );
            if ($entry) {
                array_push($entries, $entry);
            }
        }

        // Create the RSS array
        $feedData = array(
                'title'=> \FrontEnd_Helper_viewHelper::__form('form_Newest offers') ,
                'link'=> $domainPath ,
                'charset'=>'UTF-8',
                'entries'=>$entries
        );

        // create our feed object and import the data
        $feed = \Zend_Feed::importArray ( $feedData, 'rss' );

        # rss dirrectory path
        $mainDir = ROOT_PATH ."rss/";

        # generate translated file name
        $fileName = \FrontEnd_Helper_viewHelper::__form('form_newest-offers');

        # complete path for offer rss feed file
        $offerXml = $mainDir. "{$fileName}.xml";

        # create dir if not exists
        if(!file_exists($mainDir))
            mkdir($mainDir, 0775, TRUE);

        # save rss as xml data
        $rssFeed = $feed->saveXML();

        # write rss data into file
        $offerHandle = fopen($offerXml, 'w');
        fwrite($offerHandle, $rssFeed);
        fclose($offerHandle);

        $message = \FrontEnd_Helper_viewHelper::__form('form_RSS feed  for newest offers has been created successfully!!!');
        $flash = $this->_helper->getHelper('FlashMessenger');
        $flash->addMessage(array('success' => $message));
        $this->_helper->redirector(index , 'rss' , null ) ;

    }

    public function popularOffersAction()
    {

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $params = $this->_getAllParams ();

        $offers = \KC\Repository\Offer::getPopularOffersForRSS();

        $domain1 = $_SERVER['HTTP_HOST'];
        $domain = 'http://www.'.$domain1;

        # set locale
        $locale = '';
        if (isset($_COOKIE['locale']) && ($_COOKIE['locale']) != 'en') {
        $locale = '/' . $_COOKIE['locale'];
        }

        #  doamin path with locale
        $domainPath = $domain . $locale;

        // Create array to store the RSS feed entries
        $entries = array();

        // Cycle through the rankings, creating an array storing
        // each, and push the array onto the $entries array
        foreach ($offers as  $offer) {

        $entry = array(
            'title'       => $offer['title'] ,
            'link'        => $domainPath . '/' . $offer['permalink'],
            'description' => "{$offer['terms']}",
            'guid' => $offer['id']
            );
            if ($entry) {
                    array_push($entries, $entry);
        }
        }

        // Create the RSS array
        $feedData = array(
            'title'=> \FrontEnd_Helper_viewHelper::__form('form_Popular offers') ,
            'link'=> $domainPath ,
            'charset'=>'UTF-8',
            'entries'=>$entries
            );

            // create our feed object and import the data
            $feed = Zend_Feed::importArray ( $feedData, 'rss' );

            # rss dirrectory path
            $mainDir = ROOT_PATH ."rss/";

            # generate translated file name
            $fileName = \FrontEnd_Helper_viewHelper::__form('form_popular-offers');

            # complete path for offer rss feed file
            $offerXml = $mainDir. "{$fileName}.xml";

            # create dir if not exists
            if(!file_exists($mainDir))
                mkdir($mainDir, 0775, TRUE);

            # save rss as xml data
            $rssFeed = $feed->saveXML();

            # write rss data into file
            $offerHandle = fopen($offerXml, 'w');
            fwrite($offerHandle, $rssFeed);
            fclose($offerHandle);

            $message = \FrontEnd_Helper_viewHelper::__form('form_RSS feed  for popular offers has been created successfully!!!');
            $flash = $this->_helper->getHelper('FlashMessenger');
            $flash->addMessage(array('success' => $message));
            $this->_helper->redirector(index , 'rss' , null ) ;

    }

}

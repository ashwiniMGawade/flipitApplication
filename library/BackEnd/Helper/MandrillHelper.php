<?php
class BackEnd_Helper_MandrillHelper
{
    public static function getHeaderFooterContent($currentObject)
    {
        $data = Signupmaxaccount::getEmailHeaderFooter();
        $currentObject->headerContent = $data['email_header'];
        $currentObject->footerContent = $data['email_footer'];
    }

    public static function getDirectLoginLinks($currentObject, $linkType = '', $visitorEmail = '', $mandrillKey = '')
    {
        if ($linkType != 'script') {
            $testEmail = $currentObject->getRequest()->getParam('testEmail');
            $passwordKey = MD5('12345678');
            $sendParameter = $currentObject->getRequest()->getParam('send');
        }
        $visitorDirectLoginInformation = array();
        $visitorRefererInformation = array();
        $visitorInformation = array();

        if (isset($sendParameter) && $sendParameter == 'test') {
            $getTestEmaildata =  Visitor::getVisitorDetailsByEmail($testEmail);
            $key = 0;
            $visitorDirectLoginInformation[$key]['rcpt'] = $testEmail;
            $visitorDirectLoginInformation[$key]['vars'][0]['name'] = 'loginLink';
            
            $visitorDirectLoginInformation[$key]['vars'][0]['content'] =
                HTTP_PATH_FRONTEND
                .FrontEnd_Helper_viewHelper::__link("link_login")
                . "/" .FrontEnd_Helper_viewHelper::__link("link_directlogin")
                . "/"
                .base64_encode($getTestEmaildata[0]['email']) ."/". $getTestEmaildata[0]['password'];
            $visitorDirectLoginInformation[$key]['vars'][1]['name'] = 'loginLinkWithUnsubscribe';
            
            $visitorDirectLoginInformation[$key]['vars'][1]['content'] =
                HTTP_PATH_FRONTEND
                . FrontEnd_Helper_viewHelper::__link("link_login")
                . "/" .FrontEnd_Helper_viewHelper::__link("link_directloginunsubscribe")
                . "/" . base64_encode($testEmail) ."/". $passwordKey;
            $visitorInformation[$key]['email'] = $testEmail;
            
            $visitorInformation[$key]['name'] =
                !empty($getTestEmaildata[0]['firstName']) ? $getTestEmaildata[0]['firstName']
                . ' ' .$getTestEmaildata[0]['lastName'] : 'member';
            $currentObject->_loginLinkAndData = $visitorDirectLoginInformation;
            $currentObject->_to = $visitorInformation;

        } elseif (isset($linkType) && $linkType == 'frontend') {
            $getTestEmaildata =  Visitor::getVisitorDetailsByEmail($visitorEmail);
            $key = 0;
            $visitorDirectLoginInformation[$key]['rcpt'] = $visitorEmail;
            $visitorDirectLoginInformation[$key]['vars'][0]['name'] = 'loginLink';
           
            $visitorDirectLoginInformation[$key]['vars'][0]['content'] =
                HTTP_PATH_LOCALE
                .FrontEnd_Helper_viewHelper::__link("link_login")
                . "/" .FrontEnd_Helper_viewHelper::__link("link_directlogin")
                . "/"
                .base64_encode($getTestEmaildata[0]['email']) ."/". $getTestEmaildata[0]['password'];
            $visitorDirectLoginInformation[$key]['vars'][1]['name'] = 'loginLinkWithUnsubscribe';
            
            $visitorDirectLoginInformation[$key]['vars'][1]['content'] =
                HTTP_PATH_LOCALE
                . FrontEnd_Helper_viewHelper::__link("link_login")
                . "/" .FrontEnd_Helper_viewHelper::__link("link_directloginunsubscribe")
                . "/" . base64_encode($visitorEmail) ."/". $passwordKey;
            $visitorInformation[$key]['email'] = $visitorEmail;
            
            $visitorInformation[$key]['name'] =
                !empty($getTestEmaildata[0]['firstName']) ? $getTestEmaildata[0]['firstName']
                . ' ' .$getTestEmaildata[0]['lastName'] : 'member';
            $currentObject->_loginLinkAndData = $visitorDirectLoginInformation;
            $currentObject->_to = $visitorInformation;
        } else {
            $visitors = new Visitor();
            $visitors = $visitors->getVisitorsToSendNewsletter();
            if ($linkType == 'script') {
                $frontendPath = $currentObject->_linkPath;
                $currentMandrillKey = $mandrillKey;
            } else {
                $frontendPath = HTTP_PATH_FRONTEND;
                $currentMandrillKey = $currentObject->getInvokeArg('mandrillKey');
            }
            $mandrill = new Mandrill_Init($currentMandrillKey);
            $getUserDataFromMandrill = $mandrill->users->senders();

            foreach ($getUserDataFromMandrill as $key => $value) {
                if ($value['soft_bounces'] >= 6 || $value['hard_bounces'] >= 2) {
                    $updateActive = Visitor::updateVisitorActiveStatus($value['address']);
                }
            }

            foreach ($visitors as $key => $value) {
                $keywords ='' ;
                foreach ($value['keywords'] as $k => $word) {
                    $keywords .= $word['keyword'] . ' ';
                }

                $visitorDirectLoginInformation[$key]['rcpt'] = $value['email'];
                $visitorDirectLoginInformation[$key]['vars'][0]['name'] = 'loginLink';
                $visitorRefererInformation[$key]['rcpt'] = $value['email'];
                $visitorRefererInformation[$key]['values']['referrer'] = trim($keywords) ;
                $visitorDirectLoginInformation[$key]['vars'][0]['content'] =
                    $frontendPath . FrontEnd_Helper_viewHelper::__link("link_login")
                    . "/" .FrontEnd_Helper_viewHelper::__link("link_directlogin")
                    . "/" . base64_encode($value['email']) ."/". $value['password'];
                $visitorDirectLoginInformation[$key]['vars'][1]['name'] = 'loginLinkWithUnsubscribe';
                $visitorDirectLoginInformation[$key]['vars'][1]['content'] =
                    $frontendPath
                    . FrontEnd_Helper_viewHelper::__link("link_login")
                    . "/" .FrontEnd_Helper_viewHelper::__link("link_directloginunsubscribe")
                    . "/" . base64_encode($value['email']) ."/". $value['password'];
                $visitorInformation[$key]['email'] = $value['email'];
                $visitorInformation[$key]['name'] = !empty($value['firstName']) ? $value['firstName'] : 'Member';
            }

            $currentObject->_recipientMetaData = $visitorRefererInformation;
            $currentObject->_loginLinkAndData = $visitorDirectLoginInformation;
            $currentObject->_to = $visitorInformation;
        }
    }
    public static function getOfferDates($currentOffer, $daysTillOfferExpires)
    {
        $stringAdded = FrontEnd_Helper_viewHelper::__email('Added');
        $stringOnly = FrontEnd_Helper_viewHelper::__email('Only');
        $startDate = new Zend_Date(strtotime($currentOffer->startDate));
        $offerDates = '';
        if($currentOffer->discountType == "CD"):
            $offerDates .= $stringAdded;
            $offerDates .= ': ';
            $offerDates .= ucwords($startDate->get(Zend_Date::DATE_MEDIUM));
            $offerDates .= ', ';

            if (
                $daysTillOfferExpires ==5
                || $daysTillOfferExpires ==4
                || $daysTillOfferExpires ==3
                || $daysTillOfferExpires ==2
            ) {
                $offerDates .= $stringOnly;
                $offerDates .= '&nbsp;';
                $offerDates .= $daysTillOfferExpires;
                $offerDates .= '&nbsp;';
                $offerDates .= FrontEnd_Helper_viewHelper::__email('days left!');
            } elseif ($daysTillOfferExpires == 1) {
                $offerDates .= $stringOnly;
                $offerDates .= '&nbsp;';
                $offerDates .= $daysTillOfferExpires;
                $offerDates .= '&nbsp;';
                $offerDates .= FrontEnd_Helper_viewHelper::__email('day left!');
            } elseif ($daysTillOfferExpires == 0) {
                    $offerDates .= FrontEnd_Helper_viewHelper::__email('Expires today');
            } else {
                    $endDate = new Zend_Date(strtotime($currentOffer->endDate));
                    $offerDates .= FrontEnd_Helper_viewHelper::__email('Expires on').': ';
                    $offerDates .= ucwords($endDate->get(Zend_Date::DATE_MEDIUM));
            } elseif (
                $currentOffer->discountType == "PR"
                || $currentOffer->discountType == "SL"
                || $currentOffer->discountType == "PA"
            ):
                $offerDates .= $stringAdded;
                $offerDates .= ': ';
                $offerDates .= ucwords($startDate->get(Zend_Date::DATE_MEDIUM));
        endif;
        return $offerDates;
    }
}

<?php
class BackEnd_Helper_MandrillHelper
{
    public static function getHeaderFooterContent($currentObject)
    {
        $emailHeaderFooterContent = Signupmaxaccount::getEmailHeaderFooter();
        $currentObject->headerContent = $emailHeaderFooterContent[0]['email_header'];
        $currentObject->footerContent = $emailHeaderFooterContent[0]['email_footer'];
    }

    public static function getDirectLoginLinks($currentObject, $linkType = '', $visitorEmail = '', $mandrillKey = '')
    {
        if ($linkType != 'scheduleNewsletterSender') {
            $testEmail = $currentObject->getRequest()->getParam('testEmail');
            $passwordKey = MD5('12345678');
            $sendParameter = $currentObject->getRequest()->getParam('send');
        }

        $visitorDirectLoginInformation = array();
        $visitorRefererInformation = array();
        $visitorInformation = array();

        if (isset($sendParameter) && $sendParameter == 'test') {
            self::setMandrillMergeVars(
                $visitorDirectLoginInformation,
                $visitorInformation,
                $testEmail,
                $passwordKey,
                $currentObject
            );
        } elseif ((isset($linkType) && $linkType == 'frontend')) {
            self::setMandrillMergeVars(
                $visitorDirectLoginInformation,
                $visitorInformation,
                $visitorEmail,
                $passwordKey,
                $currentObject
            );
        } else {
            $visitorId = isset($currentObject->visitorId) && $currentObject->visitorId != ''
                ? $currentObject->visitorId: '';
            $unsubscribeLink = isset($currentObject->visitorId) && $currentObject->visitorId != ''
                ? 'directcodealertunsubscribe': 'directloginunsubscribe';
            $shopId = isset($currentObject->shopId) && $currentObject->shopId != ''
                ? '/'.base64_encode($currentObject->shopId): '';
            $visitors = new Visitor();
            $visitors = $visitors->getVisitorsToSendNewsletter($visitorId);
            if ($linkType == 'scheduleNewsletterSender') {
                $frontendPath = $currentObject->_linkPath;
                $currentMandrillKey = $mandrillKey;
            } else {
                $frontendPath = HTTP_PATH_FRONTEND;
                $currentMandrillKey = $currentObject->getInvokeArg('mandrillKey');
            }
            $mandrill = new Mandrill_Init($currentMandrillKey);
            $usersInformationFromMandrill = $mandrill->users->senders();

            foreach ($usersInformationFromMandrill as $usersInformationFromMandrillKey => $usersInformationFromMandrillValue) {
                if (
                    $usersInformationFromMandrillValue['soft_bounces'] >= 6
                    || $usersInformationFromMandrillValue['hard_bounces'] >= 2) {
                    $updateActive = Visitor::updateVisitorActiveStatus($usersInformationFromMandrillValue['address']);
                }
            }

            foreach ($visitors as $visitorKey => $visitorValue) {
                $keywords ='' ;
                foreach ($visitorValue['keywords'] as $keyword) {
                    $keywords .= $keyword['keyword'] . ' ';
                }

                $visitorDirectLoginInformation[$visitorKey]['rcpt'] = $visitorValue['email'];
                $visitorDirectLoginInformation[$visitorKey]['vars'][0]['name'] = 'loginLink';
                $visitorRefererInformation[$visitorKey]['rcpt'] = $visitorValue['email'];
                $visitorRefererInformation[$visitorKey]['values']['referrer'] = trim($keywords);
                
                $visitorDirectLoginInformation[$visitorKey]['vars'][0]['content'] =
                    $frontendPath . FrontEnd_Helper_viewHelper::__link("link_login")
                    . "/" .FrontEnd_Helper_viewHelper::__link("link_directlogin")
                    . "/" . base64_encode($visitorValue['email']) ."/". $visitorValue['password'];
                $visitorDirectLoginInformation[$visitorKey]['vars'][1]['name'] = 'loginLinkWithUnsubscribe';
                
                $visitorDirectLoginInformation[$visitorKey]['vars'][1]['content'] =
                    $frontendPath
                    . FrontEnd_Helper_viewHelper::__link("link_login")
                    . "/" .$unsubscribeLink
                    . "/" . base64_encode($visitorValue['email']) ."/". $visitorValue['password'].$shopId;
                
                $visitorInformation[$visitorKey]['email'] = $visitorValue['email'];
                $visitorInformation[$visitorKey]['name'] =
                        !empty($visitorValue['firstName']) ? $visitorValue['firstName'] : 'Member';
            }

            $currentObject->_recipientMetaData = $visitorRefererInformation;
            $currentObject->_loginLinkAndData = $visitorDirectLoginInformation;
            $currentObject->_to = $visitorInformation;
        }
    }

    public static function setMandrillMergeVars(
        $visitorDirectLoginInformation,
        $visitorInformation,
        $visitorEmail,
        $passwordKey,
        $currentObject
    ) {
        $visitorDetails = Visitor::getVisitorDetailsByEmail($visitorEmail);
        $key = 0;
        if (defined('HTTP_PATH_FRONTEND')) {
            $httpPathLocale = HTTP_PATH_FRONTEND;
        } else {
            $httpPathLocale = HTTP_PATH_LOCALE;
        }
        $visitorDirectLoginInformation[$key]['rcpt'] = $visitorEmail;
        $visitorDirectLoginInformation[$key]['vars'][0]['name'] = 'loginLink';
       
        $visitorDirectLoginInformation[$key]['vars'][0]['content'] =
            $httpPathLocale
            .FrontEnd_Helper_viewHelper::__link("link_login")
            . "/" .FrontEnd_Helper_viewHelper::__link("link_directlogin")
            . "/"
            .base64_encode($visitorDetails['email']) ."/". $visitorDetails['password'];
        
        $visitorDirectLoginInformation[$key]['vars'][1]['name'] = 'loginLinkWithUnsubscribe';
        
        $visitorDirectLoginInformation[$key]['vars'][1]['content'] =
            $httpPathLocale
            . FrontEnd_Helper_viewHelper::__link("link_login")
            . "/" ."directloginunsubscribe"
            . "/" . base64_encode($visitorEmail) ."/". $visitorDetails['password'];
        $visitorInformation[$key]['email'] = $visitorEmail;
        
        $visitorInformation[$key]['name'] =
            !empty($visitorDetails['firstName']) ? $visitorDetails['firstName']
            . ' ' .$visitorDetails['lastName'] : 'member';
       
        $currentObject->_loginLinkAndData = $visitorDirectLoginInformation;
        $currentObject->_to = $visitorInformation;
    }

    public static function getOfferDates($currentOffer, $daysTillOfferExpires, $locale, $type = '')
    {
        if ($type == 'doc2') {
            $startDateObject = (object) $currentOffer->startDate;
            $endDateObject = (object) $currentOffer->endDate;
            $startDateString = isset($startDateObject->date) ? $startDateObject->date : $startDateObject->format('Y-m-d');
            $endDateString = isset($endDateObject->date) ? $endDateObject->date : $endDateObject->format('Y-m-d');
            $startDate = new Zend_Date(strtotime($startDateString));
            $endDate = new Zend_Date(strtotime($endDateString));
        } else {
            $startDate = new Zend_Date(strtotime($currentOffer->startDate));
            $endDate = new Zend_Date(strtotime($currentOffer->endDate));
        }
        $offerDates = '';
        $startDateFormat = $locale == 'us' ? Zend_Date::MONTH_NAME.' '.Zend_Date::DAY : Zend_Date::DAY.' '.Zend_Date::MONTH_NAME;
        $endDateFormat = $locale == 'us' ? Zend_Date::MONTH_NAME.' '.Zend_Date::DAY.', '.Zend_Date::YEAR: Zend_Date::DATE_LONG;
        if ($currentOffer->discountType == "CD") {
            $offerDates .= FrontEnd_Helper_viewHelper::__email('email_valid from');
            $offerDates .= ' ';
            $offerDates .= ucwords($startDate->get($startDateFormat));
            $offerDates .= ' '.FrontEnd_Helper_viewHelper::__email('email_t/m');
        } else {
            $offerDates .= FrontEnd_Helper_viewHelper::__email('email_valid t/m');
        }
        $offerDates .= ' ';
        $offerDates .= ucwords($endDate->get($endDateFormat));
        return $offerDates;
    }
}

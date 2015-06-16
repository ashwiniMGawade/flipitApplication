<?php
class ConversionController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $subId = $this->getRequest()->getParam("subid", false);

        if (!$subId) {
            $this->_helper->redirector->setCode(301);
            $this->_redirect(HTTP_PATH_LOCALE);
        }

        $websiteName = $this->view->translate('www.kortingscode.nl');
        $googleAnalysticsId = $this->view->translate('UA-17691171-1');
        $currentUrl = \Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $urltoWrite =  trim(HTTP_PATH, '/') . $currentUrl ;
        $logDirectoryPath = APPLICATION_PATH . "/../logs/";

        if (!file_exists($logDirectoryPath)) {
            mkdir($logDirectoryPath, 776, true);
        }

        $conversionDetails = \FrontEnd_Helper_viewHelper::
            getRequestedDataBySetGetCache(
                'all_conversion_details',
                array(
                    'function' => '\KC\Repository\Conversions::getConversionDetail', 'parameters' => array($subId)
                )
            );
        $fileName = $logDirectoryPath  . 'conversion';
        
        if ($conversionDetails) {
            $networkName = "" ;
            if (isset($conversionDetails['offer']['shop']['affliatenetwork'])) {
                $networkName = $conversionDetails['offer']['shop']['affliatenetwork']['name'];
            }
            //please ignore the formatting of EDO
            $log = <<<EOD
            $networkName; Incoming; $urltoWrite
EOD;

            \FrontEnd_Helper_viewHelper::writeLog($log, $fileName) ;
            \KC\Repository\Conversions::updateConverted($subId);
            $orderId = $this->getRequest()->getParam("orderid", false);
            $total = $this->getRequest()->getParam("total", false);
            $sku = $this->getRequest()->getParam("sku", "");
            if ($sku == "") {
                $sku = "1";
            }
            $storeName = $this->getRequest()->getParam("storename", "");
            $tax = $this->getRequest()->getParam("tax", "");
            $shipping = $this->getRequest()->getParam("shipping", "");
            $city =  $this->getRequest()->getParam("city", "");
            $region =  $this->getRequest()->getParam("region", "");
            $country = $this->getRequest()->getParam("country", "");
            $productName = $this->getRequest()->getParam("productname", "");
            $category = $this->getRequest()->getParam("category", "");
            $utmipc = $sku; # Product code / SKU
            $utmipn = $productName; # Product name
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

            $utmp = 'leadtracking.php';
            $utmiva = '';
            # random request number to prevent caching
            $var_utmn = rand(1000000000, 9999999999);
            # random request number to  prevent caching
            $var_utmn2 = rand(1000000000, 9999999999);
            # random request number to prevent caching
            $var_utmn3 = rand(1000000000, 9999999999);
            # random request number to prevent caching
            $utmhid = rand(1000000000, 9999999999);
            # Looks up the original cookie in the DB
            $utma = $conversionDetails['utma'];
            $utmz = $conversionDetails['utmz'] ;

            $utmipn = str_replace(" ", "%20", $utmipn);
            $utma = str_replace(" ", "%20", $utma);
            $utma = str_replace("|", "%7C", $utma);
            $utmz = str_replace(" ", "%20", $utmz);
            $utmz = str_replace("|", "%7C", $utmz);

            $transactionUrl = 'http://www.google-analytics.com/__utm.gif?utmwv=4.6.5&utmn='.$var_utmn
                        .'&utmt=tran&utmtid='.$orderId.'&utmtst='.$storeName
                        .'&utmtto='.$utmipr.'&utmttx='.$utmttx.'&utmtsp='.$utmtsp.'&utmtci='.$utmtci
                        .'&utmtrg='.$utmtrg.'&utmtco='.$utmtco
                        .'&utmcc=__utma%3D'.$utma.'%3B%2B__utmz%3D'.$utmz.'%3B' .'&utmac='
                        .$googleAnalysticsId.'&utmhn='.$websiteName;

            $itemUrl = 'http://www.google-analytics.com/__utm.gif?utmwv=4.6.5&utmn='.$var_utmn2
                        .'&utmt=item&utmtid='.$orderId.'&utmipc='.$utmipc
                        .'&utmipn='.$utmipn.'&utmiva='.$utmiva.'&utmipr='.$utmipr
                        .'&utmiqt='.$utmiqt.'&utmcc=__utma%3D'
                        .$utma.'%3B%2B__utmz%3D'.$utmz.'%3B'.'&utmac='.$googleAnalysticsId.'&utmhn='.$websiteName;

            $urchinUrl ='http://www.google-analytics.com/__utm.gif?utmwv=4.6.5&utmn='.$var_utmn3
                        .'&utmp='.$utmp.'&utmcc=__utma%3D'
                        .$utma.'%3B%2B__utmz%3D'.$utmz.'%3B'.'&utmac='.$googleAnalysticsId.'&utmhn='.$websiteName;

            $trLog = <<<EOD
            $networkName; Outgoing-transactionUrl; $transactionUrl
EOD;
            \FrontEnd_Helper_viewHelper::writeLog($trLog, $fileName);
            $itemLog = <<<EOD
           $networkName; Outgoing-itemUrl; $itemUrl
EOD;
            \FrontEnd_Helper_viewHelper::writeLog($itemLog, $fileName);
            $urLog = <<<EOD
            $networkName; Outgoing-urchinUrl; $urchinUrl
EOD;
            \FrontEnd_Helper_viewHelper::writeLog($urLog, $fileName);
            $handle = fopen($transactionUrl, "r");
            $trancsaction = fgets($handle);
            $handle2 = fopen($itemUrl, "r");
            $item = fgets($handle2);
            $handle3 = fopen($urchinUrl, "r");
            $urchin = fgets($handle3);

            fclose($handle);
            fclose($handle2);
            fclose($handle3);

        } else {
            $log = <<<EOD
            Invalid url; Incoming; $urltoWrite
EOD;
            \FrontEnd_Helper_viewHelper::writeLog($log, $fileName);
            $this->_helper->redirector->setCode(301);
            $this->_redirect(HTTP_PATH_LOCALE);
        }

    }
}

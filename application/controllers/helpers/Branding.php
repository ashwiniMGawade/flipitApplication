<?php
class Zend_Controller_Action_Helper_Branding extends Zend_Controller_Action_Helper_Abstract
{
    public function start()
    {
        setcookie('passCache', '1', time() + \Application_Service_Session_Timeout::getSessionTimeout(), '/');

        $storeUrl             = $this->getRequest()->getParam('storeUrl', false);
        $linkValidationHash   = $this->getRequest()->getParam('hash', false);
        $shopID               = $this->getRequest()->getParam('shopID', false);

        $session        = new Zend_Session_Namespace('Branding');
        $shopBranding   = \KC\Repository\Shop::getShopBranding($shopID);

        if (!empty($shopBranding)) {
            $session->data = $shopBranding;
        } else {
            $session->data = $this->defaultStyles();
        }

        if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'flipit.com')) {
            date_default_timezone_set('Europe/Amsterdam');
            $securityCheckHash = md5($shopID.date('Y').'-'.date('m').'-'.date('d').':'.date('H'));
            if ($securityCheckHash == $linkValidationHash) {
                $session->brandingActivated = true;
            } else {
                echo "Error - Wrong linkValidationHash, please try the link again from Admin";
                exit;
            }

        } else {
            $session->brandingActivated = false;
            echo "Error - This function can only be activated from the admin";
            exit;
        }
        return $storeUrl;
    }

    public function save()
    {
        $redirectUrl = $_SERVER['HTTP_REFERER'];
        $session = new Zend_Session_Namespace('Branding');
        foreach ($_POST as $cssSelector => $value) {
            if (!empty($session->data[$cssSelector])) {
                $session->data[$cssSelector]['value'] = $value;
            }
        }

        if (!empty($_POST['delete'])) {
            foreach ($_POST['delete'] as $brandingItem) {
                unlink(ROOT_PATH.$session->data[$brandingItem]['img']);
                unset($session->data[$brandingItem]);
            }
        }

        if (!empty($_FILES["newsletter_store_logo"]["tmp_name"])) {
            $newletterStoreLogo = "images/upload/shop/".time().'_'.$_FILES["newsletter_store_logo"]["name"];
            move_uploaded_file($_FILES["newsletter_store_logo"]["tmp_name"], ROOT_PATH.$newletterStoreLogo);
            $session->data['newsletter_store_logo']['img'] = $newletterStoreLogo;
        }

        if (!empty($_FILES["header_background"]["tmp_name"])) {
            $headerBackgroundImage = "images/upload/shop/".time().'_'.$_FILES["header_background"]["name"];
            move_uploaded_file($_FILES["header_background"]["tmp_name"], ROOT_PATH.$headerBackgroundImage);
            $session->data['header_background']['img'] = $headerBackgroundImage;
        }

        if (empty($_POST['preview'])) {
            $shop =  \Zend_Registry::get('emLocale')->find('KC\Entity\Shop', $_POST['shop_id']);
            if (empty($_POST['reset'])) {
                $shop->brandingcss =  serialize($session->data);
                $redirectUrl = self::stop();
            } else {
                $shop->brandingcss  = null;
                $session->data = $this->defaultStyles();
            }
            \Zend_Registry::get('emLocale')->persist($shop);
            \Zend_Registry::get('emLocale')->flush();
        }
        return $redirectUrl;
    }

    public function stop()
    {
        setcookie('passCache', '1', '1', '/');
        $session = new Zend_Session_Namespace('Branding');
        $session->data = array();
        $session->brandingActivated = false;
        $httpScheme = FrontEnd_Helper_viewHelper::getServerNameScheme();
        return 'http://'.$httpScheme.'.flipit.com/admin/shop';
    }

    private function defaultStyles()
    {
        $defaultStyles                                                  = array();

        $defaultStyles['link_color']['css-selector']                    = '.section .block .link';
        $defaultStyles['link_color']['css-property']                    = 'color';
        $defaultStyles['link_color']['value']                           = '#0077cc';

        $defaultStyles['store_title']['css-selector']                   = '.header-block h1';
        $defaultStyles['store_title']['css-property']                   = 'color';
        $defaultStyles['store_title']['value']                          = '#32383e';

        $defaultStyles['store_sub_title']['css-selector']               = '.header-block h2';
        $defaultStyles['store_sub_title']['css-property']               = 'color';
        $defaultStyles['store_sub_title']['value']                      = '#878a8d';

        $defaultStyles['newsletter_background_color']['css-selector']   = '.section .block-form .holder';
        $defaultStyles['newsletter_background_color']['css-property']   = 'background-color';
        $defaultStyles['newsletter_background_color']['value']          = '#f6f6f6';

        $defaultStyles['newsletter_title_color']['css-selector']        = '.section .block-form h4';
        $defaultStyles['newsletter_title_color']['css-property']        = 'color';
        $defaultStyles['newsletter_title_color']['value']               = '#33383e';

        $defaultStyles['overwrite']['value']                            = '';

        return $defaultStyles;
    }
}

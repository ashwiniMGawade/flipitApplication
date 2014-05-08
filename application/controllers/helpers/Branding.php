<?php 
class Zend_Controller_Action_Helper_Branding extends Zend_Controller_Action_Helper_Abstract
{
    function start()
    {
        $storeUrl   = $this->getRequest()->getParam('storeUrl', false);
        $hash       = $this->getRequest()->getParam('hash', false);
        $shopID     = $this->getRequest()->getParam('shopID', false);

        $session = new Zend_Session_Namespace('Branding');

        $shopBranding   = Shop::getShopBranding($shopID);

        if (!empty($shopBranding)) {
            $session->data = $shopBranding;
        }else{
            $session->data = $this->defaultStyles();
        }
        
        if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'flipit.com')) {
            $security = md5($shopID.date('Y').'-'.date('m').'-'.date('d').':'.date('H'));
            if ($security == $hash) {
                $session->brandingActivated = true;
            }else{
                echo "Error - Wrong hash, please try the link again from Admin";
                exit;
            }
            
        }else{
            $session->brandingActivated = false;
            echo "Error - This function can only be activated from the admin";
            exit;
        }

        return $storeUrl;
    }

    function save()
    {
        $session = new Zend_Session_Namespace('Branding');
        foreach ($_POST as $key => $value) {
            if(!empty($session->data[$key])) $session->data[$key]['value'] = $value;
        }

        // unlink images?
        if (!empty($_POST['delete'])) {
            foreach ($_POST['delete'] as $item) {
                unlink(ROOT_PATH.$session->data[$item]['img']);
                unset($session->data[$item]);
            }
        }

        if (!empty($_FILES["newsletter_store_logo"]["tmp_name"])){
            $logo = "images/upload/shop/".time().'_'.$_FILES["newsletter_store_logo"]["name"];
            move_uploaded_file($_FILES["newsletter_store_logo"]["tmp_name"], ROOT_PATH.$logo);
            $session->data['newsletter_store_logo']['img'] = $logo;
        }
        
        if (!empty($_FILES["header_background"]["tmp_name"])){
            $bg = "images/upload/shop/".time().'_'.$_FILES["header_background"]["name"];
            move_uploaded_file($_FILES["header_background"]["tmp_name"], ROOT_PATH.$bg);
            $session->data['header_background']['img'] = $bg;
        }

        // save settings to store if not preview
        if (empty($_POST['preview'])) {
            $shop =  Doctrine_Core::getTable("Shop")->find( $_POST['shop_id'] );
            if (empty($_POST['reset'])) {
                $shop->brandingcss =  serialize($session->data);
            }else{
                $shop->brandingcss  = null;
                $session->data = $this->defaultStyles();
            }
            $shop->save();
        }
    }   

    function stop(){
        setcookie('passCache', '1' , '1' , '/');
        $session = new Zend_Session_Namespace('Branding');
        $session->data = array();
        $session->brandingActivated = false;
        return 'http://www.flipit.com/admin';
    } 

    private function defaultStyles(){
       $defaultStyles                                                  = array();

       $defaultStyles['link_color']['css-selector']                    = '.section .block .link';
       $defaultStyles['link_color']['css-property']                    = 'color';
       $defaultStyles['link_color']['value']                           = '#0077cc';

       $defaultStyles['store_title']['css-selector']                   = '.header-block h1';
       $defaultStyles['store_title']['css-property']                   = 'color';
       $defaultStyles['store_title']['value']                          = '#32383e';

       $defaultStyles['newsletter_background_color']['css-selector']   = '.section .block-form .holder';
       $defaultStyles['newsletter_background_color']['css-property']   = 'background-color';
       $defaultStyles['newsletter_background_color']['value']          = '#f6f6f6';

       $defaultStyles['overwrite']['value']                            = '';       
       
       return $defaultStyles; 
    }
}
?>
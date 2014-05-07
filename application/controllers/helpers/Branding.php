<?php 
class Zend_Controller_Action_Helper_Branding extends Zend_Controller_Action_Helper_Abstract
{
    function start()
    {

        $session = new Zend_Session_Namespace('Branding');

        $session->data                                                  = array();

        $session->data['link_color']['css-selector']                    = '.section .block .link';
        $session->data['link_color']['css-property']                    = 'color';
        $session->data['link_color']['value']                           = 'red';

        $session->data['store_title']['css-selector']                   = '.header-block h1';
        $session->data['store_title']['css-property']                   = 'color';
        $session->data['store_title']['value']                          = 'black';

        $session->data['newsletter_background_color']['css-selector']   = '.section .block-form .holder';
        $session->data['newsletter_background_color']['css-property']   = 'background-color';
        $session->data['newsletter_background_color']['value']          = '#0091c6';

        $session->data['overwrite']['value']                            = '';

        $storeUrl   = $this->getRequest()->getParam('storeUrl', false);
        $hash       = $this->getRequest()->getParam('hash', false);

        //if (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'flipit.com')) {
            $session->brandingActivated = true;
            //$this->_redirect( $storeUrl );
        // }else{
        //     $session->brandingActivated = false;
        //     echo "This function can only be activated from the admin";
        // }

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
            $shop =  Doctrine_Core::getTable("Shop")->find( (int)$_POST['shop_id'] );
            $shop->brandingcss = serialize($session->data);
            $shop->save();
        }
    }   

    function stop(){
        setcookie('passCache', '1' , '1' , '/');
        $session->brandingActivated = false;
    } 
}
?>
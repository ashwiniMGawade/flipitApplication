<?php
/**
 * all the regarding Email Lightbox functionality
 * @author Er.kundal
 *
 */
class Admin_HomepageController extends Zend_Controller_Action
{

    # holds settings regarding user rights
    protected $_settings = false ;

    /**
     * check authentication before load the page
     *
     * @see Zend_Controller_Action::preDispatch()
     * @author Er.kundal dd
     * @version 1.0
     */
    public function preDispatch()
    {
        $conn2 = \BackEnd_Helper_viewHelper::addConnection(); // connection
        // generate with second
        // database
        $params = $this->getAllParams();
        if (! \Auth_StaffAdapter::hasIdentity()) {
            $referer = new \Zend_Session_Namespace('referer');
            $referer->refer = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $this->redirect('/admin/auth/index');
        }
        \BackEnd_Helper_viewHelper::closeConnection($conn2);
        $this->view->controllerName = $this->getRequest()->getParam('controller');
        $this->view->action = $this->getRequest()->getParam('action');


        # redirect of a user don't have any permission for this controller
        $sessionNamespace = new \Zend_Session_Namespace();
        $this->_settings  = $sessionNamespace->settings['rights'] ;

    }
    public function init()
    {
        /* Initialize action controller here */

    }


    public function getanothertabAction()
    {
        if ($this->_settings['content']['rights'] != '1') {
            $this->getResponse()->setHttpResponseCode(404);
            echo $this->_helper->json('This page does not exist');
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $params = $this->getAllParams();
        $abouthtml = '';

        if ($params['tab'] == 'anothertab') {
                    $no = $params['tabnumber'];

                    $abouthtml = '<div id="multidivchild_'.$no.'" ><div class="clear"></div>
                <div class="mainpage-content-left">
                    <label><strong>'.$this->view->translate("Tab") .'&nbsp;'. $no.'</strong></label>
                </div>
                <div class="clear"></div>
                <div class="mainpage-content-line mb10">
                    <div class="mainpage-content-left">
                        <label><strong>'.$this->view->translate('Remove Tab').'</strong></label>
                    </div>
                    <div class="mainpage-content-right">
                        <div class="mainpage-content-right-inner-left-other">
                            <div data-toggle="buttons-checkbox" class="btn-group">
                                <button type="button" style="border-radius: 4px 4px 4px 4px;"
                                name="remove_tab" onclick="removeajaxtab()" class="btn ">'.$this->view->translate("Remove").'</button>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="mainpage-content-line mb10">
                    <div class="mainpage-content-left">
                        <label><strong>'.$this->view->translate("Active").'</strong></label>
                    </div>
                    <div class="mainpage-content-right">
                        <div class="mainpage-content-right-inner-left-other">
                            <div data-toggle="buttons-checkbox" class="btn-group">
                                <input type="checkbox" checked="checked" class="display-none"
                                    name="status[]"
                                    value="">
                                <button type="button" style="border-radius: 4px 0 0 4px;"
                                    name="on" onclick="changeStatus(this)"
                                    class="btn ">'.$this->view->translate("On").'</button>
                                <button type="button" name="off" onclick="changeStatus(this)"
                                    class="btn btn-primary">'.$this->view->translate("Off").'</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mainpage-content-line mb10">
                    <div class="mainpage-content-left">
                        <label><strong>'.$this->view->translate("Title").'</strong></label>
                    </div>
                    <div class="mainpage-content-right">
                        <div class="mainpage-content-right-inner-right-other"></div>
                        <div class="mainpage-content-right-inner-left-other">
                            <input type="text" placeholder="'.$this->view->translate("Title").'" name="title[]"
                                class="span3 mbot" maxlength="55"
                                value="">
                        </div>
                    </div>
                </div>
                <div class="mainpage-content-line mb10">
                    <div class="mainpage-content-left">
                        <label><strong>'.$this->view->translate("Permalink").'</strong></label>
                    </div>
                    <div class="mainpage-content-right">
                        <div class="mainpage-content-right-inner-right-other"></div>
                        <div class="mainpage-content-right-inner-left-other">
                            <input type="text" placeholder="'.$this->view->translate("Permalink").'" name="permalink[]"
                                class="span3 mbot" maxlength="55"
                                value="">
                        </div>
                    </div>
                </div>
                <div class="mainpage-content-line mb10">
                    <div class="mainpage-content-left">
                        <label><strong>'.$this->view->translate('Description').'</strong></label>
                    </div>
                    <div class="mainpage-content-right">
                        <div class="mainpage-content-line">
                            <div class="mainpage-content-line">
                                <div class="mainpage-content-right-inner-right-other"></div>
                                <div class="mainpage-content-right-inner-left-other">
                                    <textarea name="content[]"  class="ckeditor ckeditorDynamic"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>';
        }
        echo $abouthtml;
        //return $abouthtml;
        die;
    }

    /**
     * delete about tab
     * @author Er.kundal
     * @version 1.0
     */
    public function deleteaboutAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $params = $this->getAllParams();
        $id = $params['removeid'];
        if ($id) {
            $about = \KC\Repository\About::removeeabouttab($id);
            $deleted = \KC\Repository\Settings::removesettingabouttab($id);
            if ($deleted) {
                echo "1";
            } else {
                echo 0;
            }
            die();
        }
    }

    /**
     * delete Menu Records
     * @author Raman
     * @version 1.0
     */
    public function deletemenuAction()
    {
        $deleted = \KC\Repository\Menu::deleteAllMenuRecord();

        if ($deleted) {
            echo "Deleted";
            die();
        } else {
            echo 0;
        }
        die();
    }

    public function indexAction()
    {
        // action body
        $u = \Auth_StaffAdapter::getIdentity();
        $role = $u->users->id;
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $flash->getMessages();
        $this->view->messageSuccess = isset($message[0]['success']) ? $message[0]['success'] : '';
        $this->view->messageError = isset($message[0]['error']) ? $message[0]['error'] : '';

        $this->view->rights = $this->_settings['content'];

        // check if asSeenIn form is submitted
        if ($this->_request->isPost() && $this->getRequest()->getParam("form") == "asSeenIn") {
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Changes has been saved successfully.');
            $flash->addMessage(array('success' => $message ));
            $params = $this->getAllParams();
            \KC\Repository\SeenIn::update($params);
            $this->redirect('/admin/homepage');
        }
        // check if asSeenIn form is submitted
        if ($this->_request->isPost() && $this->getRequest()->getParam("form") == "homepage_banner") {
            $flash = $this->_helper->getHelper('FlashMessenger');
            $message = $this->view->translate('Changes has been saved successfully.');
            $flash->addMessage(array('success' => $message ));
            $params = $this->getAllParams();
            $result = \KC\Repository\Signupmaxaccount::updateHeaderImage($params);
            self::updateVarnish();
            $this->redirect('/admin/homepage');
        }

        // check if about form is submitted
        if ($this->_request->isPost() && $this->getRequest()->getParam("form") == "about") {
            if ($this->_settings['content']['rights'] == '1') {
                $params = $this->getAllParams();
                $pattern = array("/&amp;/", "/[\,+@#$%'&*!;&\"<>\^()]+/", '/\s/', "/-{2,}/");
                $replaceWith = array("", "", "-", "-");
                $isValidPermalink = true;
                foreach ($params['permalink'] as $index => $permalink) {
                    unset($params['permalink'][$index]);
                    $urlString = preg_replace($pattern, $replaceWith, trim($permalink));
                    $permalink = strtolower($urlString);
                    if ($permalink != '' && in_array($permalink, $params['permalink'])) {
                        $isValidPermalink = false;
                    }
                    $params['permalink'][$index] = $permalink;
                }
                if (!$isValidPermalink) {
                    $message = $this->view->translate('All about tab permalink must be unique.');
                    $flash->addMessage(array('error' => $message ));
                    $this->redirect('/admin/homepage');
                }
                \KC\Repository\About::update($params);
                $message = $this->view->translate('Changes has been saved successfully.');
                $flash->addMessage(array('success' => $message ));
                self::updateVarnish();
            } else {
                $this->redirect('/admin/index');
            }
        }
        // return updated about content
        $this->view->about = \KC\Repository\About::getAboutContent();
        // return updated about content
        $this->view->seenIn = \KC\Repository\SeenIn::getSeenInContent();
        $this->view->data = \KC\Repository\PopularShop::getPopularShop();
        //Return Popular voucher code from database
        $data = KC\Repository\PopularVouchercodes::getPopularvoucherCode();
        $this->view->code = $data;
        //Return Popular category code from database
        $catg = \KC\Repository\PopularCategory::getPopularCategories();
        $this->view->category = @$catg;
        //Return Popular category code from database
        $special = \KC\Repository\SpecialList::getsplpage();
        
        $this->view->Speciallist = @$special;
        // Return Money saving Article from database
        $this->view->articles = \KC\Repository\MoneysavingArticle::getSaving();
        // Return locale for front end
        $this->view->locale = \KC\Repository\Signupmaxaccount::getAllMaxAccounts();
        $this->view->localeSettings = \KC\Repository\LocaleSettings::getLocaleSettings();
        $this->view->timezones_list = \KC\Repository\Signupmaxaccount::$timezones;
        $site_name = "kortingscode.nl";
        if (isset($_COOKIE['site_name'])) {
            $site_name =  $_COOKIE['site_name'];
        }
        $this->view->localeStatus = \KC\Repository\Website::getLocaleStatus($site_name);
    }

    ///********End shop section function for popular shop ***********///
    /**
     * Search to 10 best popular shop from database
     * @author Er.kundal
     * @version 1.0
     */
    public function searchtoptenshopAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = 0;
        // $ststus = 1;
        //call to seach top 10 offer function in model class
        $data = \KC\Repository\PopularShop::searchTopTenshop($srh, $flag);
        $ar = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                $ar[] = $d['name'];
            }
        } else {
            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }
        echo Zend_Json::encode($ar);
        die();
    }

    /**
     * add manual a offer in popular shop
     * @author Er.kundal
     * @version 1.0
     */
    public function addshopAction()
    {
        $data = $this->getRequest()->getParam('name');
        //call to add offer function from model
        $flag = \KC\Repository\PopularShop::addShopInList($data);
        #if popular shop  is addedd then update varnsih as well
        if ($flag && $flag != "2" && $flag != "1") {
            self::updateVarnish();
        }
        echo \Zend_Json::encode($flag);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularShopsForDropdown_list');
        die();
    }
    /**
     * delete popular shop
     * @author Er.kundal
     * @version 1.0
     */
    public function deletepopularshopAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        \KC\Repository\PopularShop::deletePapularCode($id, $position);
        self::updateVarnish();
        //get popular code from database
        $data = \KC\Repository\PopularShop::getPopularShop();
        echo \Zend_Json::encode($data);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularShopsForDropdown_list');
        die();
    }

    public function savepopularshopspositionAction()
    { 
        \KC\Repository\PopularShop::savePopularShopsPosition($this->getRequest()->getParam('shopid'));
        $popularShop = \KC\Repository\PopularShop::getPopularShop();
        echo \Zend_Json::encode($popularShop);
        exit();
    }
    /**
     * move up one position  popular shop list
     * @author Er.kundal
     * @version 1.0
     */
    public function moveupAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated = \KC\Repository\PopularShop::moveUp($id, $position);
        #if popular shop list  is updated then update varnsih as well
        if ($isUpdated) {
            self::updateVarnish();
        }
        //get popular code from database
        $data = \KC\Repository\PopularShop::getPopularShop();
        echo \Zend_Json::encode($data);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularShopsForDropdown_list');
        die();
    }
    /**
     * move down one position  popular shop list
     * @author Er.kundal
     * @version 1.0
     */
    public function movedownAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated  = \KC\Repository\PopularShop::moveDown($id, $position);
        #if popular shop  is updated then update varnsih as well
        if ($isUpdated) {
            self::updateVarnish();
        }
        //get popular code from database
        $data = \KC\Repository\PopularShop::getPopularShop();
        echo \Zend_Json::encode($data);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularShops_list');
        FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll('all_popularShopsForDropdown_list');
        die();
    }

///*****************************End shop section function for popular shop *****************************///
///*****************************Offer section function for voucher code *******************************///

    public function searchtoptenofferAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = 0;
        //call to seach top 10 offer function in model class
        $data = KC\Repository\PopularVouchercodes::searchTopTenOffer($srh, $flag);
        $ar = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                $ar[] = $d['offer']['title'];
            }
        } else {
            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }
        echo \Zend_Json::encode($ar);
        die();
    }


    /**
     * add manual a offer in popular code
     * @author Er.kundal
     * @version 1.0
     */
    public function addoffercodeAction()
    {
        $data = $this->getRequest()->getParam('name');
        //call to add offer function from model
        $flag = KC\Repository\PopularVouchercodes::addOfferInVouchercode($data);
        echo \Zend_Json::encode($flag);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        die();
    }


    /**
     * delete popular voucher code
     * @author kundal
     * @version 1.0
     */
    public function deletepopularvochercodeAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        KC\Repository\PopularVouchercodes::deletePapularvocherCode($id, $position);
        //get popular code from database
        $data = KC\Repository\PopularVouchercodes::getPopularvoucherCode();
        echo \Zend_Json::encode($data);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        die();
    }

    /**
     * move up one position  popular voucher code list
     * @author Er.kundal
     * @version 1.0
     */
    public function moveupcodeAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        KC\Repository\PopularVouchercodes::moveUpCode($id, $position);
        //get popular code from database
        $data = KC\Repository\PopularVouchercodes::getPopularvoucherCode();
        echo \Zend_Json::encode($data);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        die();
    }

    /**
     * move down one position  popular Voucher Code list
     * @author Er.kundal
     * @version 1.0
     */
    public function movedowncodeAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        KC\Repository\PopularVouchercodes::moveDownCode($id, $position);
        //get popular code from database
        $data = KC\Repository\PopularVouchercodes::getPopularvoucherCode();
        echo \Zend_Json::encode($data);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        die();
    }


///********Offer section function for Popular Categories ***********///

    public function searchtoptencategoryAction()
    {
        $srh = $this->getRequest()->getParam('keyword');

        $flag = 0;
        $ststus = 1;
        // call to seach top 10 offer function in model class
        $data = KC\Repository\PopularCategory::searchTopTenPopulerCategory($ststus, $srh, $flag);
        $ar = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                $ar[] = $d['name'];
            }
        } else {
            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }
        echo \Zend_Json::encode($ar);
        die();
    }


    /**
     * add manual a offer in popular category
     * @authorEr.kundal
     * @version 1.0
     */
    public function addnewcategoryAction()
    {
        $data = $this->getRequest()->getParam('name');
        //call to add offer function from model
        $flag = \KC\Repository\PopularCategory::addCategoryInPopulerCategory($data);
        #if popular category is addedd then update varnsih as well
        if ($flag && $flag != "2" && $flag != "1") {
            self::updateVarnish();
        }
        echo \Zend_Json::encode($flag);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        die();
    }


    /**
     * delete popular category
     * @author Er.kundal
     * @version 1.0
     */
    public function deletepopularcategoryAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated = \KC\Repository\PopularCategory::deletePopulerCategory($id, $position);
        #if category list is updated then update varnsih as well
        if ($isUpdated) {
            self::updateVarnish();
        }
        //get popular code from database
        $data = \KC\Repository\PopularCategory::getPopularCategories();
        echo \Zend_Json::encode($data);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        die();
    }

    /**
     * move up one position  popular category list
     * @author Er.kundal
     * @version 1.0
     */
    public function moveupcategoryAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated = \KC\Repository\PopularCategory::moveUpPopulerCategory($id, $position);
        #if money saving is updated then update varnsih as well
        if ($isUpdated) {
            self::updateVarnish();
        }
        //get popular code from database
        $data = \KC\Repository\PopularCategory::getPopularCategories();
        //echo "<pre>";print_r($data);die;
        echo \Zend_Json::encode($data);
        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        die();
    }

    /**
     * move down one position  popular category list
     * @author Er.kundal
     * @version 1.0
     */
    public function movedowncategoryAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated = \KC\Repository\PopularCategory::moveDownPopulerCategory($id, $position);
        #if money saving is updated then update varnsih as well
        if ($isUpdated) {
            self::updateVarnish();
        }
        //get popular code from database
        $data = \KC\Repository\PopularCategory::getPopularCategories();
        echo \Zend_Json::encode($data);

        $key = 'all_widget5_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        $key = 'all_widget6_list';
        \FrontEnd_Helper_viewHelper::clearCacheByKeyOrAll($key);
        die();
    }


///********Page section function for Special List ***********///
    /**
     * Search to 10 best Pages from database
     * @author Er.kundal
     * @version 1.0
     */
    public function searchtoptenspecialAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = 0;
        //call to seach top 10 offer function in model class
        $data = \KC\Repository\SpecialList::searchTopTenOffer($srh, $flag);
        $ar = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                $ar[] = $d['title'];
            }
        } else {
            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }
        echo \Zend_Json::encode($ar);
        die();
    }
    /**
     * add manual a offer in Special List
     * @author Er.kundal
     * @version 1.0
     */
    public function addspecialAction()
    {
        $data = $this->getRequest()->getParam('name');
        //call to add offer function from model
        $flag = \KC\Repository\SpecialList::addOfferInList($data);
        #if popular shop  is addedd then update varnsih as well
        if ($flag && $flag != "2" && $flag != "1") {
            self::updateVarnish();
        }
        echo Zend_Json::encode($flag);
        die();
    }
    /**
     * delete Special List offer
     * @author Er.kundal
     * @version 1.0
     */
    public function deletespecialAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated = \KC\Repository\SpecialList::deletePapularCode($id, $position);
        #if special list is updated then update varnsih as well
        if ($isUpdated) {
            self::updateVarnish();
        }
        //get popular code from database
        $data = \KC\Repository\SpecialList::getsplpage();
        echo \Zend_Json::encode($data);
        die();
    }
    /**
     * move up one position  Special List offer
     * @author Er.kundal
     * @version 1.0
     */
    public function moveupspecialAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated = \KC\Repository\SpecialList::moveUpSpecial($id, $position) ;
        #if special list  is updated then update varnsih as well
        if ($isUpdated) {
            self::updateVarnish();
        }
        //get popular code from database
        $data = \KC\Repository\SpecialList::getsplpage();
        echo \Zend_Json::encode($data);
        die();
    }
    /**
     * move down one position  Special List
     * @author Er.kundal
     * @version 1.0
     */
    public function movedownspecialAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated = \KC\Repository\SpecialList::moveDownSpecial($id, $position);
        #if special list is updated then update varnsih as well
        if ($isUpdated) {
            self::updateVarnish();
        }
        //get popular code from database
        $data = \KC\Repository\SpecialList::getsplpage();
        echo \Zend_Json::encode($data);
        die();
    }

///*********************************** Article section function for Article ***********************************///
    /**
     * Search to 10 best Article from database
     * @author Er.kundal
     * @version 1.0
     */

    public function searchtoptensavingAction()
    {
        $srh = $this->getRequest()->getParam('keyword');
        $flag = 0;
        // call to seach top 10 Article function in model class
        $data = \KC\Repository\MoneysavingArticle::searchTopTenSaving($srh, $flag);
        $ar = array();
        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                $ar[] = $d['title'];
            }
        } else {
            $msg = $this->view->translate('No Record Found');
            $ar[] = $msg;
        }
        echo \Zend_Json::encode($ar);
        die();

    }
    /**
     * add manual a offer in Special List
     * @author Er.kundal
     * @version 1.0
     */
    public function addsavingAction()
    {
        $data = $this->getRequest()->getParam('name');
        //call to add articler function from model
        $flag = \KC\Repository\MoneysavingArticle::addSaving($data);
        # if an artticle is added tehn update varnish
        if ($flag && $flag != "2" && $flag != "1") {
            self::updateVarnish();
        }
        echo Zend_Json::encode($flag);
        die();
    }
    /**
     * delete Money saving article
     * @author Er.kundal
     * @version 1.0
     */
    public function deletesavingAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated  = \KC\Repository\MoneysavingArticle::deleteSaving($id, $position);
        #if movey saving  list is updated then update varnsih as well
        if ($isUpdated) {
            self::updateVarnish();
        }
        //get Money saving article from database
        $data = \KC\Repository\MoneysavingArticle::getSaving();
        echo \Zend_Json::encode($data);
        die();
    }
    /**
     * move up one position Money saving article
     * @author Er.kundal
     * @version 1.0
     */
    public function moveupsavingAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated = \KC\Repository\MoneysavingArticle::moveUpSaving($id, $position);
        #if money saving is updated then update varnsih as well
        if ($isUpdated) {
            self::updateVarnish();
        }
        //get popular code from database
        $data = \KC\Repository\MoneysavingArticle::getSaving();
        echo \Zend_Json::encode($data);
        die();
    }
    /**
     * move down one position Money saving article
     * @author Er.kundal
     * @version 1.0
     */
    public function movedownsavingAction()
    {
        $id = $this->getRequest()->getParam('id');
        $position = $this->getRequest()->getParam('pos');
        //call model class function pass position and id
        $isUpdated = \KC\Repository\MoneysavingArticle::moveDownSaving($id, $position);
        #if money saving is updated then update varnsih as well
        if ($isUpdated) {
            self::updateVarnish();
        }
        //get Money saving article from database
        $data = \KC\Repository\MoneysavingArticle::getSaving();
        echo \Zend_Json::encode($data);
        die();
    }

    public function clearcacheAction()
    {
        $cache = \Zend_Registry::get('cache');
        $cache->clean();
        echo 'cache is cleared';
        exit;
    }

    public function deletedatabaseentryAction()
    {
        $del1 = Doctrine_Query::create()->delete()
        ->from('About')
        ->execute();
        $del2 = Doctrine_Query::create()->delete()
        ->from('Settings')
        ->execute();
        $del3 = Doctrine_Query::create()->delete()
        ->from('SeenIn')
        ->execute();
        $del4 = Doctrine_Query::create()->delete()
        ->from('Special')
        ->execute();
        return true;

    }

    /**
     *  updateVarnish
     *
     *  update varnish table whenever an home page is updated
     */
    public function updateVarnish()
    {
        // Add urls to refresh in Varnish
        $varnishObj = new \KC\Repository\Varnish();
        $varnishObj->addUrl(rtrim(HTTP_PATH_FRONTEND, '/'));
        # make markplaatfeed url's get refreashed only in case of kortingscode
        if (LOCALE == '') {
            $varnishObj->addUrl(HTTP_PATH_FRONTEND  . 'marktplaatsfeed');
            $varnishObj->addUrl(HTTP_PATH_FRONTEND . 'marktplaatsmobilefeed');
        }
    }

    /**
     *  saveEmail
     *
     *  saves the current Email which is to be used in front end
     */

    public function saveemailAction()
    {
        \KC\Repository\Signupmaxaccount::saveemail($this->getRequest()->getParam('emailperlocale'));
        $flash = $this->_helper->getHelper('FlashMessenger');
        $message = $this->view->translate('Email has been changed successfully.');
        $flash->addMessage(array('success' => $message ));
        die;
    }

    public function getemailAction()
    {
        $email_data = \KC\Repository\Signupmaxaccount::getAllMaxAccounts();
        echo \Zend_Json::encode($email_data[0]['emailperlocale']);
        die;
    }

    public function updateHeaderImageAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            if ($this->_request->isPost()) {
                if (isset($_FILES['homepageBanner']['name']) && @$_FILES['homepageBanner']['name'] != '') {
                    $params = $this->getAllParams();
                    $result = \KC\Repository\Signupmaxaccount::updateHeaderImage($params);
                    self::updateVarnish();
                    $this->_helper->json($result);
                }
            }
        }
        $this->redirect('/admin/homepage');
    }

    /**
     *  deleteHeaderImage
     *
     *  Deletes the current Header Image
     *  @author Amit Sharma
     */
    public function deleteHeaderImageAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            if ($this->_request->isPost()) {
                $params = $this->_getAllParams();
                $result = \KC\Repository\Signupmaxaccount::deleteHeaderImage($params);
                self::updateVarnish();
                $this->_helper->json($result);
            }
        }
        $this->redirect('/admin/homepage');
    }


    public function updateWidgetBackgroundImageAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            if ($this->_request->isPost()) {
                if (isset($_FILES['homepageWidgetBackground']['name']) && @$_FILES['homepageWidgetBackground']['name'] != '') {
                    $params = $this->getAllParams();
                    $result = \KC\Repository\Signupmaxaccount::updateWidgetBackgroundImage($params);
                    self::updateVarnish();
                    $this->_helper->json($result);
                }
            }
        }
        $this->redirect('/admin/homepage');
    }

    /**
     *  deleteWidgetImage
     *
     *  Deletes the current Widget Image
     *  @author Amit Sharma
     */

    public function deleteWidgetImageAction()
    {
        if ($this->_request->isXmlHttpRequest()) {
            if ($this->_request->isPost()) {
                $params = $this->_getAllParams();
                $result = \KC\Repository\Signupmaxaccount::deleteWidgetImage($params);
                self::updateVarnish();
                $this->_helper->json($result);
            }
        }
        $this->redirect('/admin/homepage');
    }
}

<?php
/**
 * this class is used for about page
 * @author Raman
 *
 */
class ViewcountController extends Zend_Controller_Action
{
    /**
     * override views based on modules if exists
     * @see Zend_Controller_Action::init()
     * @author Bhart
     */
    public function init()
    {
        $module   = strtolower($this->getRequest()->getParam('lang'));
        $controller = strtolower($this->getRequest()->getControllerName());
        $action     = strtolower($this->getRequest()->getActionName());

        # check module specific view exists or not
        if (file_exists (APPLICATION_PATH . '/modules/'  . $module . '/views/scripts/' . $controller . '/' . $action . ".phtml")){

            # set module specific view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/modules/'  . $module . '/views/scripts' );
        } else{

            # set default module view script path
            $this->view->setScriptPath( APPLICATION_PATH . '/views/scripts' );
        }
    }

    public function indexAction()
    {
         $slug = "about";

        // get top categories (for now i just fetch the available category list)
        $cache = Zend_Registry::get('cache');
        $pagedatakey ="all_". "pagedata".$slug ."_list";
            // no cache available, lets query.
            $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($pagedatakey);
            //key not exist in cache
            if($flag){
                //get Page data from database and store in cache
                $page = Page::getPageDetailFromSlug($slug);
                FrontEnd_Helper_viewHelper::setInCache($pagedatakey, $page);
            } else {
                //get from cache
                $page = FrontEnd_Helper_viewHelper::getFromCacheByKey($pagedatakey);
            }


            $id = $page[0]['id'];
            $alluserkey ="all_". "alluser".$id ."_list";
            $flag =  FrontEnd_Helper_viewHelper::checkCacheStatusByKey($alluserkey);
            //key not exist in cache
            if($flag){
                //get  data from database and store in cache
                $allUserDetails = User::getAllUsersDetails();
                FrontEnd_Helper_viewHelper::setInCache($alluserkey, $allUserDetails);
            } else {
                //get from cache
                $allUserDetails = FrontEnd_Helper_viewHelper::getFromCacheByKey($alluserkey);
            }
            $limit = 5;

        $this->view->page = $page;
        $paginator = FrontEnd_Helper_viewHelper::renderPagination($allUserDetails,$this->_getAllParams(),$limit,7);
        $this->view->paginator = $paginator;

    }




    public function clearcacheAction()
    {
        $cache = Zend_Registry::get('cache');
        $cache->clean();
        echo 'cache is cleared';
        exit;
    }

    /**
     * view counter common function
     * @author Raman
     * @version 1.0
     */
    public function storecountAction()
    {
        $type = $this->_getParam('type');
        $event = $this->_getParam('event');
        $id = $this->_getParam('id');

        $cnt  = FrontEnd_Helper_viewHelper::viewCounter($type, $event, $id);

        if($cnt == "false") {
            echo Zend_Json::encode(false);

        } else {

            echo Zend_Json::encode(true);
        }

        die();
    }

}

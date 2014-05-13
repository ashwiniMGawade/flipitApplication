<?php
/**
 * this class is used for Money Saving Guides (Bespaar Wijzers)
 * get value from database and display on home page
 *
 * @author Raman
 *
 */
class PreviewController extends Zend_Controller_Action
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
            $pageId = $this->getRequest ()->getParam ('attachedpage');
            $this->pageDetail = Page::getdefaultPageProperties($pageId);
            $this->view->pageTitle = @$this->pageDetail[0]['pageTitle'];

            if($this->pageDetail[0]['customHeader']) {
                $this->view->layout()->customHeader = "\n" . @$this->pageDetail[0]['customHeader'];
            }

            $this->view->headTitle(@$this->pageDetail[0]['metaTitle']);
            $this->view->headMeta()->setName('description', @trim($this->pageDetail[0]['metaDescription']));
    }

    public function notpreviewAction()
    {
        $params = $this->_getAllParams();

    }

    public function articleinfoAction()
    {
        $params = $this->_getAllParams();
        $view = Articles :: getArticleData($params);
        $this->view->articleview = $view[0];
        $uobj = new User();
        $this->view->udetails = $uobj->getUserProfileDetails($this->view->articleview['authorid']);
    }

}

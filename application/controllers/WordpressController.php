<?php

class WordpressController extends Zend_Controller_Action
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

    public function getheaderAction()
    {
        // disable layout rendering
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        // include the stylesheets and javascripts hardcoded in the WP theme
        echo $this->view->headLink ()->appendStylesheet ( PUBLIC_PATH . "css/front_end/style.css" );
        //echo $this->view->headScript ()->appendFile ( PUBLIC_PATH . "js/jquery-1.7.2.min.js" );
        //echo $this->view->headScript ()->appendFile ( PUBLIC_PATH . "js/front_end/layout.js" );
        // render the partials for the header
        echo $this->view->render('partials/_header.phtml');
        echo $this->view->render('partials/_top_nav.phtml');

        // now we still need to ensure that the rendering is not sent to the browser
        $this->getResponse()->clearBody();
    }

    public function getfooterAction()
    {
        // disable layout rendering
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        // render the partials for the header
        //echo '<div class="innerwrapper">';
            echo $this->view->render('partials/_footer.phtml');
        //echo '</div>';

        // now we still need to ensure that the rendering is not sent to the browser
        $this->getResponse()->clearBody();
    }
}

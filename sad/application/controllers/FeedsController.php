<?php

class FeedsController extends  Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function casandosemgranaAction()
    {
        if(LOCALE == 'br'):
            $topPopularCoupons = \FrontEnd_Helper_viewHelper::gethomeSections('popular', 5);
            $this->view->topPopularCoupons = $topPopularCoupons;
        else:
            header('location:'.HTTP_PATH);
        endif;
    }

    public function patatasbravaAction()
    {
        if(LOCALE == 'es'):
            $topPopularCoupons = \FrontEnd_Helper_viewHelper::gethomeSections('popular', 20);
            $this->view->topPopularCoupons = $topPopularCoupons;
        else:
            header('location:'.HTTP_PATH);
        endif;
    }

    public function divamomsAction()
    {
        if(LOCALE == 'us'):
            $topPopularCoupons = \FrontEnd_Helper_viewHelper::gethomeSections('popular', 9);
            $this->view->topPopularCoupons = $topPopularCoupons;
        else:
            header('location:'.HTTP_PATH);
        endif;
    }
}

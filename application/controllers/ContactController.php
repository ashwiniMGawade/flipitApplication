<?php
# we set a passcache cookie for Flipit and Kortingscode.nl so that admin can bypass varnish
class ContactController extends Zend_Controller_Action
{

    public function getcontactformdetailsAction()
    {
        $parameters = $this->_getAllParams();
        echo "<pre>";print_r($parameters);die;

    }
}

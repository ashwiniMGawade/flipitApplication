<?php
class Admin_HelloworldController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $frontEndHelperObject = new \FrontEnd_Helper_viewHelper();
        $unSortedNames = array('Amit', 'Rajbir', 'Kraj');
        $sortedNamesBySort = $frontEndHelperObject->sortNamesByOrder($unSortedNames, 'asc');
        $this->view->sortedNamesBySort = $sortedNamesBySort;
    }
}
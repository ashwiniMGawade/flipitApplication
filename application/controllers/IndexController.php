<?php
use domain\entity\test;
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }
    public function indexAction()
    {
        echo $a  = test::showText('Kuldeep');
    }
}


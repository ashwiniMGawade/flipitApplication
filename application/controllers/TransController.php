<?php
	class TransController extends Zend_Controller_Action {

		public function init()
		{
		}

		public function getformdataAction()
		{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			return $this->getHelper('Transl8')->getFormDataAction();
		}

		public function submitAction()
		{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			return $this->getHelper('Transl8')->submitAction();
		}

		public function testAction()
		{

		}
	}


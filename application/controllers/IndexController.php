<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$grid = new Grid_City();
    	
    	$this->view->grid = $grid;
    }
    
    public function editAction()
    {
    	$request = $this->getRequest();
    	Zend_Debug::dump($request->getParam('ID'));
    	Zend_Debug::dump($request->getParam('selected'));
    }
    
    public function deleteAction()
    {
    	$request = $this->getRequest();
    	Zend_Debug::dump($request->getParam('ID'));
    	Zend_Debug::dump($request->getParam('selected'));
    }
}

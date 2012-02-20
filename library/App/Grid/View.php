<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class App_Grid_View
{
    protected $_view = null;
    
    public function getView()
    {
        if ($this->_view === null) {
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            if ($viewRenderer->view === null) {
                $viewRenderer->initView();
            }
            $this->_view = $viewRenderer->view;
        }
        return $this->_view;
    }
    
    function __call($method, $params)
    {
        if (substr($method, 0, 3) == 'set') {
            $method = strtolower(substr($method, 3));
            $this->getView()->{$method} = $params[0];
        }
        return $this;
    }
    
    public function setJsonActions($jsonActions)
    {
        $this->getView()->headScript()->appendScript('var jsonActions = ' . $jsonActions);
    }
    
    public function setJavascriptFormVariable($formId)
    {
        $this->getView()->headScript()->appendScript('var gridolaFormId = "#' . $formId . '";');
    }
    
    public function setJavascriptInclude($javascriptFile)
    {
        $this->getView()->headScript()->appendFile($javascriptFile);
    }
    
    public function render($template)
    {
        return $this->getView()->render($template);
    }
}
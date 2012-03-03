<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class Gridola_View
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
        return $this;
    }
    
    public function setJavascriptFormVariable($formId)
    {
        $this->getView()->headScript()->appendScript('var gridolaFormId = "#' . $formId . '";');
        return $this;
    }
    
    public function setJavascriptInclude()
    {
        $this->getView()->headScript()->appendFile('/js/gridola.js');
        return $this;
    }
    
    public function render($template)
    {
        return $this->getView()->render($template);
    }
}
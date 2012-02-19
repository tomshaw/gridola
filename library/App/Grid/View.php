<?php

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
	
	public function assignResults($paginator)
	{
		$this->getView()->rows = $paginator;
	}
	
	public function assignGrid($elements)
	{
		$this->getView()->gridElements = $elements;
	}
	
	public function assignSort($sort)
	{
		$this->getView()->sort = $sort;
	}
	
	public function assignActions($actions)
	{
		$this->getView()->actions = $actions;
	}
	
	public function assignMassActions($massactions)
	{
		$this->getView()->massActions = $massactions;
	}
	
	public function assignMassActionField($field)
	{
		$this->getView()->massActionField = $field;
	}
	
	public function assignFormId($formId)
	{
		$this->getView()->formId = $formId;
	}
	
	public function assignFormAction($path)
	{
		$this->getView()->formAction = $path;
	}
	
	public function assignRoute($route)
	{
		$this->getView()->gridRoute = $route;
	}
	
	public function assignJsonActions($jsonActions)
	{
		$this->getView()->headScript()->appendScript('var jsonActions = ' . $jsonActions);
	}
	
	public function assignHeadScript($formId)
	{	
		$this->getView()->headScript()->appendScript('var gridolaFormId = "#'.$formId.'";');
		
		$this->getView()->headScript()->appendFile('/js/gridola.js');
	}
	
	public function assignRowClickData($rowClickData)
	{
		$this->getView()->rowClickUrl = $rowClickData;
	}
	
	public function assignCyclecolors($cycleColors)
	{
		$this->getView()->cycleColors = $cycleColors;
	}
	
	public function assignOnMouseOverColor($onMouseOverColor)
	{
		$this->getView()->onMouseOverColor = $onMouseOverColor;
	}
	
	public function render($template)
	{
		return $this->getView()->render($template);
	}
	
}
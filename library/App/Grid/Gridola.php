<?php
/*!
* Gidola Zend Framework 1.x Grid
* Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
* MIT Licensed
*/
abstract class App_Grid_Gridola
{	
	protected $_request = null;
	
	protected $_session = null;
	
	protected $_view = null;
	
	protected $_db = null;
	
	protected $_element = null;
	
	protected $_jsonActions = null;
	
	protected $_searchParams = array();
	
	protected $_rowSets = null;
	
	protected $_urlHelper = null;
	
	protected $_route = null;
	
	protected function getRequest()
	{
		if ($this->_request === null) {
			$this->_request = Zend_Controller_Front::getInstance()->getRequest();
		}
		return $this->_request;
	}
	
	protected function getSession()
	{
		if ($this->_session === null) {
			$this->_session = new Zend_Session_Namespace('store');
		}
		return $this->_session;
	}
	
	protected function getDb()
	{
		if ($this->_db === null) {
			$this->_db = new App_Grid_Db();
		}
		return $this->_db;
	}
	
	protected function getView()
	{
		if ($this->_view === null) {
			$this->_view = new App_Grid_View();
		}
		return $this->_view;
	}
	
	protected function getElement()
	{
		if ($this->_element === null) {
			$this->_element = new App_Grid_Element();
		}
		return $this->_element;
	}
	
	protected function getSearchParams()
	{
		if(!sizeof($this->_searchParams)) {
			if($this->getRequest()->isPost()) {
				$this->_searchParams = $this->getRequest()->getPost();
			}
		}		
		return $this->_searchParams;
	}
	
	protected function getRequestedSort()
	{
		return ($this->getRequest()->getParam('sort') == 'desc') ? 'asc' : 'desc';
	}
	
	protected function getUrlHelper()
	{
		if($this->_urlHelper === null) {
			$this->_urlHelper = new Zend_Controller_Action_Helper_Url();
		}
		return $this->_urlHelper;
	}
	
	protected function getUrl($action, $params)
	{
		return $this->getUrlHelper()->simple($action, null, null, $params);
	}
	
	protected function getRoute($params = array())
	{
		if ($this->_route === null) {
			$action = $this->getRequest()->getActionName();
			$this->_route = $this->getUrl($action, null, null, $params);
		}
		return $this->_route;
	}
	
	protected function _prepareData()
	{
		$this->buildData();
		$this->buildGridElements();
		$this->buildActionUrls();
		$this->encodeJsonMassaction();
		$this->prepareRowClickUrl();
		$this->initView();
	}
	
	protected function buildGridElements()
	{	
		$searchParams = $this->getSearchParams();
		foreach($this->getGrid() as $_index => $column) {
				
			if(isset($searchParams[$column['index']])) {
				$column['value'] = $searchParams[$column['index']];
			} elseif(isset($this->getSession()->data[$column['index']])) {
				$column['value'] = $this->getSession()->data[$column['index']];
			} else {
				$column['value'] = '';
			}
		
			if(isset($this->_grid[$_index])) {
				$this->_grid[$_index]['element'] = $this->getElement()->addElement($column);
				$this->_grid[$_index]['style'] = $this->getElement()->addStyle($column);
			}
		}
	}
	
	protected function buildActionUrls()
	{
		if(sizeof($this->_actions)) {
			foreach($this->_actions as $_index => $value) {
				if(sizeof($value['url'])) {
					if(isset($value['url']['action'])) {
						$this->_actions[$_index]['url'] = $this->getUrlHelper()->simple($value['url']['action']);
					}
				}
			}
		}
	}
	
	protected function encodeJsonMassaction()
	{
		if(sizeof($this->_massactions)) {
			foreach($this->_massactions as $_index => $value) {
				if(sizeof($value['url'])) {
					$this->_massactions[$_index]['url'] = $this->getUrlHelper()->url($value['url']);
				}
			}
			$this->_jsonActions = str_replace('\\/', '/', Zend_Json::encode($this->_massactions));
		}
	}
	
	protected function prepareRowClickUrl()
	{	
		$rowClickUrl = $this->getRowClickUrl();
		if(sizeof($rowClickUrl)) {
			if(!array_key_exists('field', $rowClickUrl)) {
				throw new App_Grid_Exception('A database field name must be specified when creating a clickable row.');
			}
			if(isset($rowClickUrl['url']) && is_array($rowClickUrl['url'])) {
				foreach($rowClickUrl['url'] as $_index => $value) {
					if(in_array($_index, array('module','controller','action'))) {
						$data[$_index] = $value;
					}
				}
				$this->_rowClickUrl['url'] = $this->getUrlHelper()->url($data) . '/' . $this->_rowClickUrl['field'] . '/';
			}
		}
	}
	
	protected function buildData()
	{
		$this->getDb()->setSelect($this->getSelect());
		$this->getDb()->init();
		$this->getDb()->setSortOrder($this->getSort(), $this->getOrder());
		$this->_rowSets = $this->getDb()->paginateResults();
	}
	
	protected function initView()
	{	
		$this->getView()->setRows($this->_rowSets);
		
		$this->getView()->setDataGrid($this->getGrid());
		
		$this->getView()->setSort($this->getRequestedSort());
		
		$this->getView()->setActions($this->getActions());
		
		$this->getView()->setMassActions($this->getMassActions());
		
		$this->getView()->setMassActionField($this->getMassactionField());
		
		$this->getView()->setFormId($this->getFormId());
		
		$this->getView()->setFormAction($this->getRoute());
		
		$this->getView()->setRoute($this->getRoute());
		
		if(sizeof($this->_jsonActions)) {
			$this->getView()->setJsonActions($this->_jsonActions);
		}
		
		$this->getView()->setJavascriptFormVariable($this->getFormId());
		
		$this->getView()->setJavascriptInclude('/js/gridola.js');
		
		$this->getView()->setRowClickData($this->getRowClickUrl());
		
		$this->getView()->setCycleColors($this->getCycleColors());
		
		$this->getView()->setOnMouseOverColor($this->getOnMouseOverColor());
	}
	
	public function __toString()
	{
		try {
			return $this->getView()->render($this->getTemplate());
		} catch(Exception $e) {
			throw new App_Grid_Exception($e);
		}
	}
	
}
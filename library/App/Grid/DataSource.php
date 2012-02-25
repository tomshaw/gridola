<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
abstract class App_Grid_DataSource
{
	protected $_dataSource = null;
	
	protected $_columns = array();
	
	protected $_request = null;
	
	protected $_session = null;
	
	protected $_order = null;
	
	protected $_sort = null;
	
	public function __construct()
	{

	}
	
	public function setDataSource($dataSource)
	{
		$this->_dataSource = $dataSource;
		return $this;
	}
	
	public function getDataSource()
	{
		return $this->_dataSource;
	}
	
	public function getData()
	{
		return $this->paginateResults();
	}
	
	public function getRequest()
	{
		if ($this->_request === null) {
			$this->_request = Zend_Controller_Front::getInstance()->getRequest();
		}
		return $this->_request;
	}
	
	public function getParam($param, $default = null)
	{
		return $this->getRequest()->getParam($param, $default);
	}
	
	public function getSession()
	{
		if ($this->_session === null) {
			$this->_session = new Zend_Session_Namespace('store');
		}
		return $this->_session;
	}
	
	public function init()
	{
		if ($this->getRequest()->isPost()) {
			$this->clearSession()->searchResults();
		} elseif ((null === $this->getParam('page')) && (null === ($this->getParam('sort')))) {
			$this->clearSession();
		} else {
			$this->results();
		}
	}
	
	public function paginateResults()
	{
		$paginator = Zend_Paginator::factory($this->getDataSource());
	
		$paginator->setCurrentPageNumber($this->getPage());
	
		$paginator->setItemCountPerPage(20);
	
		return $paginator;
	}
	
	protected function clearSession()
	{
		unset($this->getSession()->data);
		return $this;
	}
}
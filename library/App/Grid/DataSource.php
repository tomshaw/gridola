<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
abstract class App_Grid_DataSource extends App_Grid_Gridola
{
	protected $_dataSource = null;
	
	protected $_dataGrid = null;
	
	protected $_columns = array();
	
	protected $_order = null;
	
	protected $_sort = null;
	
	abstract public function processDataSource(); 
	
	public function setDataSource($dataSource)
	{
		$this->_dataSource = $dataSource;
		return $this;
	}
	
	public function getDataSource()
	{
		return $this->_dataSource;
	}
	
	public function setDataGrid($dataGrid)
	{
		$this->_dataGrid = $dataGrid;
		return $this;
	}
	
	public function getDataGrid()
	{
		return $this->_dataGrid;
	}
	
	protected function setSort($sort)
	{
		$this->_sort = $sort;
		return $this;
	}
	
	protected function getSort()
	{
		return $this->_sort;
	}
	
	protected function setOrder($order)
	{
		$this->_order = $order;
		return $this;
	}
	
	protected function getOrder()
	{
		return $this->_order;
	}
	
	public function initialize($dataGrid, $sort, $order)
	{
		$this->setOrder($order)->setSort($sort);
		
		$this->setDataGrid($dataGrid);
		
		if ($this->getRequest()->isPost()) {
			$this->clearSession();
		} elseif ((null === $this->getRequest()->getParam('page')) && (null === ($this->getRequest()->getParam('sort')))) {
			$this->clearSession();
		}
		
		$this->processDataSource();
	}
	
	public function getData()
	{
		$paginator = Zend_Paginator::factory($this->getDataSource());
	
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page', '1'));
	
		$paginator->setItemCountPerPage(20);
	
		return $paginator;
	}
	
	protected function arraySortByColumn($dataSets, $column, $dir = SORT_ASC)
	{
		$sortColumn = array();
		foreach ($dataSets as $key=> $row) {
			$sortColumn[$key] = $row[$column];
		}
		array_multisort($sortColumn, $dir, $dataSets);
		return $dataSets;
	}
	
	protected function clearSession()
	{
		unset($this->getSession()->data);
		return $this;
	}
}
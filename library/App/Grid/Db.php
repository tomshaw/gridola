<?php 
/*!
* Gidola Zend Framework 1.x Grid
* Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
* MIT Licensed
*/
class App_Grid_Db
{
	protected $_select = null;

	protected $_columns = array();
	
	protected $_request = null;
	
	protected $_session = null;
	
	protected $_order = null;
	
	protected $_sort = null;
	
	protected $_arrayNotationKeys = array('start','end');
	
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

	public function setSelect(Zend_Db_Table_Select $select)
	{
		$this->_select = $select;
		return $this;
	}

	public function getSelect()
	{
		return $this->_select;
	}
	
	protected function setSort($sort)
	{
		$this->_sort = $sort;
	}
	
	protected function getSort()
	{
		return $this->_sort;
	}
	
	protected function setOrder($order)
	{
		$this->_order = $order;
	}
	
	protected function getOrder()
	{
		return $this->_order;
	}
	
	protected function getPage()
	{
		return $this->getParam('page','1');
	}

	public function getColumns()
	{
		if(!sizeof($this->_columns)) {
			$this->_columns = $this->getSelect()->getPart(Zend_Db_Select::COLUMNS);
		}
		return $this->_columns;
	}

	public function getColumnsToTable()
	{
		$columnData = array();
		foreach($this->getColumns() as $column) {
			foreach($column as $_index => $value) {
				if($_index == 0) {
					$table = $value;
				}
				if($_index == 1) {
					$field = $value;
				}
			}
			$columnData[$field] = $table;
		}
		return $columnData;
	}
	
	public function postedArrayNotation($params)
	{
		$postedArrayNotation = array();
		foreach($params as $key => $values) {
			if(in_array($key, $this->_arrayNotationKeys)) {
				foreach($values as $column => $value) {
					if(empty($value)) {
						continue;
					}
					$postedArrayNotation[$column][$key] = $value;
				}
			}
		}
		return $postedArrayNotation;
	}
	
	public function init()
	{
		if($this->getRequest()->isPost()) {
			unset($this->getSession()->data);
			$this->searchResults();
		} else {
			if((null === $this->getParam('page')) && (null === ($this->getParam('sort')))) {
				unset($this->getSession()->data);
			}
			$this->results();
		}
	}
	
	protected function searchResults()
	{
		$columnData = $this->getColumnsToTable();
				
		$params = $this->getRequest()->getPost();
			
		$arrayNotation = $this->postedArrayNotation($params);

		foreach($params as $_index => $value) {
			if(empty($value)) {
				continue;
			}
			if($_index == 'selected') {
				continue;
			}
			if($value == '-1') {
				continue;
			}
			if(is_array($value)) {
				foreach($value as $key => $val) {
					if(empty($val)) {
						continue;
					}
					if(isset($arrayNotation[$key])) {
						$this->getSession()->data{$key} = $arrayNotation[$key];
					}
					if(in_array($_index, $this->_arrayNotationKeys)) {
						if($_index == $this->_arrayNotationKeys[0]) {
							if(isset($columnData[$key])) {
								$table = $columnData[$key];
								$this->getSelect()->where($table . '.' . $key . ' >= ?', $val);
							}
						}
						if($_index == $this->_arrayNotationKeys[1]) {
							if(isset($columnData[$key])) {
								$table = $columnData[$key];
								$this->getSelect()->where($table . '.' . $key . ' <= ?', $val);
							}
						}
					}
				}
			} else {
				if(isset($columnData[$_index])) {
					$table = $columnData[$_index];
					$this->getSession()->data{$_index} = $value;
					$this->getSelect()->where('LOWER('.$table.'.'.$_index.') LIKE ?', '%'.strtolower($value).'%');
				}
			}
		}
	}
			
	protected function results() 
	{
		$columnData = $this->getColumnsToTable();
		if(sizeof($this->getSession()->data)) {
			foreach($this->getSession()->data as $_column => $value) {
				if(isset($columnData[$_column])) {
					$table = $columnData[$_column];
					if(is_array($value)) {
						foreach($value as $key => $var) {
							$operand = ($key == 'start') ? '>=' : '<=';
							$this->getSelect()->where($table . '.' . $_column . ' '. $operand . ' ?', $var);
						}
					} else {
						$this->getSelect()->where('LOWER('.$table.'.'.$_column.') LIKE ?', '%'.strtolower($value).'%');
					}
				}
			}
		}
	}
	
	public function setSortOrder($sort, $order)
	{
		$this->setOrder($this->getParam('order') ? $this->getParam('order'): $order);
		
		$this->setSort($this->getParam('sort') ? $this->getParam('sort') : $sort);
		
		if($this->getSort() && $this->getOrder()) {
			$this->getSelect()->order($this->getOrder() . ' ' . strtoupper($this->getSort()));
		}
	}
	
	public function paginateResults()
	{
		$paginator = Zend_Paginator::factory($this->getSelect());
			
		$paginator->setCurrentPageNumber($this->getPage());
			
		$paginator->setItemCountPerPage(10);
		
		return $paginator;
	}
	
}

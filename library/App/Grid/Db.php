<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
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
    
    protected $_arrayNotationKeys = array('start', 'end');
    
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
        return $this->getParam('page', '1');
    }
    
    public function getColumns()
    {
        if (!sizeof($this->_columns)) {
            $this->_columns = $this->getSelect()->getPart(Zend_Db_Select::COLUMNS);
        }
        return $this->_columns;
    }
    
    public function getColumnsToTable()
    {
        $columnData = array();
        foreach ($this->getColumns() as $column) {
            foreach ($column as $_index => $value) {
                if ($_index == 0) {
                    $table = $value;
                }
                if ($_index == 1) {
                    $field = $value;
                }
            }
            $columnData[$field] = $table;
        }
        return $columnData;
    }
    
    public function postedArrayNotation()
    {
        $postedArrayNotation = array();
        foreach ($this->getRequest()->getPost() as $key => $values) {
            if (in_array($key, $this->_arrayNotationKeys)) {
                foreach ($values as $column => $value) {
                    if (empty($value)) {
                        continue;
                    }
                    $postedArrayNotation[$column][$key] = $value;
                }
            }
        }
        return $postedArrayNotation;
    }
    
    public function checkData($dataGrid)
    {
        $columns = $this->getColumnsToTable();
        if(sizeof($dataGrid)) {
            $errors = array();
            foreach($dataGrid as $_index => $data) {
                $column = isset($data['index']) ? $data['index'] : null;
                if(null === $column) {
                    throw new App_Grid_Exception('A column index must be specified when creating your data grid.');
                }
                if(!isset($columns[$column])) {
                    $errors[] = $column;
                }
            }
            if(sizeof($errors)) {
                throw new App_Grid_Exception('The following grid columns have been created but do not exist in your database query: ' . implode(', ', $errors) . '.');
            }
        }
        return $this;
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
    
    protected function searchResults()
    {
        $columnData = $this->getColumnsToTable();
        
        $postedArrayNotation = $this->postedArrayNotation();
        
        foreach ($this->getRequest()->getPost() as $_index => $value) {
            if (empty($value)) {
                continue;
            }
            if ($_index == 'selected') {
                continue;
            }
            if ($value == '-1') {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $key => $val) {
                    if (empty($val)) {
                        continue;
                    }
                    if (isset($postedArrayNotation[$key])) {
                        $this->getSession()->data{$key} = $postedArrayNotation[$key];
                    }
                    if (in_array($_index, $this->_arrayNotationKeys)) {
                        if ($_index == $this->_arrayNotationKeys[0]) {
                            if (isset($columnData[$key])) {
                                $table = $columnData[$key];
                                $this->getSelect()->where($table . '.' . $key . ' >= ?', $val);
                            }
                        }
                        if ($_index == $this->_arrayNotationKeys[1]) {
                            if (isset($columnData[$key])) {
                                $table = $columnData[$key];
                                $this->getSelect()->where($table . '.' . $key . ' <= ?', $val);
                            }
                        }
                    }
                }
            } else {
                if (isset($columnData[$_index])) {
                    $table = $columnData[$_index];
                    $this->getSession()->data{$_index} = $value;
                    $this->getSelect()->where('LOWER(' . $table . '.' . $_index . ') LIKE ?', '%' . strtolower($value) . '%');
                }
            }
        }
    }
    
    protected function results()
    {
        $columnData = $this->getColumnsToTable();
        if (sizeof($this->getSession()->data)) {
            foreach ($this->getSession()->data as $_column => $value) {
                if (isset($columnData[$_column])) {
                    $table = $columnData[$_column];
                    if (is_array($value)) {
                        foreach ($value as $key => $var) {
                            $operand = ($key == 'start') ? '>=' : '<=';
                            $this->getSelect()->where($table . '.' . $_column . ' ' . $operand . ' ?', $var);
                        }
                    } else {
                        $this->getSelect()->where('LOWER(' . $table . '.' . $_column . ') LIKE ?', '%' . strtolower($value) . '%');
                    }
                }
            }
        }
    }
    
    public function setSortOrder($sort, $order)
    {
        $this->setOrder($this->getParam('order') ? $this->getParam('order') : $order);
        
        $this->setSort($this->getParam('sort') ? $this->getParam('sort') : $sort);
        
        if ($this->getSort() && $this->getOrder()) {
            $this->getSelect()->order($this->getOrder() . ' ' . strtoupper($this->getSort()));
        }
    }
    
    public function paginateResults()
    {
        $paginator = Zend_Paginator::factory($this->getSelect());
        
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
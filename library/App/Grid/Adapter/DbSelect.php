<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class App_Grid_Adapter_DbSelect extends App_Grid_DataSource
{
    protected $_arrayNotationKeys = array('start', 'end');
    
    public function __construct(Zend_Db_Select $dataSource)
    {
        $this->setDataSource($dataSource);
    }
    
    public function getColumns()
    {
        if (!sizeof($this->_columns)) {
            $this->_columns = $this->getDataSource()->getPart(Zend_Db_Select::COLUMNS);
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
        if (isset($columnData['*'])) {
            throw new App_Grid_Exception('Wild cards column types are not allowed. Please narrow down your query to specific columns.');
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
        
        if (sizeof($dataGrid)) {
            $errors = array();
            foreach ($dataGrid as $_index => $data) {
                $column = isset($data['index']) ? $data['index'] : null;
                if (null === $column) {
                    throw new App_Grid_Exception('A column index must be specified when creating your data grid.');
                }
                if (!isset($columns[$column])) {
                    $errors[] = $column;
                }
            }
            if (sizeof($errors)) {
                throw new App_Grid_Exception('The following grid columns do not exist in your database select statement: ' . implode(', ', $errors) . '.');
            }
        }
        return $this;
    }
    
    public function getColumnTypes()
    {
        $dataGrid  = $this->getDataGrid();
        $dataTypes = array();
        foreach ($dataGrid as $grid) {
            $dataTypes[$grid['index']] = $grid['type'];
        }
        return $dataTypes;
    }
    
    public function processDataSource()
    {
        $this->checkData($this->getDataGrid());
        if ($this->getRequest()->isPost()) {
            $columnData = $this->getColumnsToTable();
            
            $columnTypes = $this->getColumnTypes();
            
            $postedArrayNotation = $this->postedArrayNotation();
            
            foreach ($this->getPostFilter() as $_index => $value) {
                $dataType = 'string';
                if (isset($columnTypes[$_index])) {
                    $dataType = $columnTypes[$_index];
                }
                if (is_array($value)) {
                    foreach ($value as $key => $val) {
                        if (isset($postedArrayNotation[$key])) {
                            $this->getSession()->data{$key} = $postedArrayNotation[$key];
                        }
                        if (in_array($_index, $this->_arrayNotationKeys)) {
                            if ($_index == $this->_arrayNotationKeys[0]) {
                                if (isset($columnData[$key])) {
                                    $table = $columnData[$key];
                                    $this->getDataSource()->where($table . '.' . $key . ' >= ?', $val);
                                }
                            }
                            if ($_index == $this->_arrayNotationKeys[1]) {
                                if (isset($columnData[$key])) {
                                    $table = $columnData[$key];
                                    $this->getDataSource()->where($table . '.' . $key . ' <= ?', $val);
                                }
                            }
                        }
                    }
                } else {
                    if (isset($columnData[$_index])) {
                        $table = $columnData[$_index];
                        $this->getSession()->data{$_index} = $value;
                        if ($dataType == 'string') {
                            $this->getDataSource()->where('LOWER(' . $table . '.' . $_index . ') LIKE ?', '%' . strtolower($value) . '%');
                        } else {
                            $this->getDataSource()->where('LOWER(' . $table . '.' . $_index . ') = ?', $value);
                        }
                    }
                }
            }
            
        } else {
            $columnData = $this->getColumnsToTable();
            if (sizeof($this->getSession()->data)) {
                foreach ($this->getSession()->data as $_column => $value) {
                    $dataType = 'string';
                    if (isset($columnTypes[$_column])) {
                        $dataType = $columnTypes[$_column];
                    }
                    if (isset($columnData[$_column])) {
                        $table = $columnData[$_column];
                        if (is_array($value)) {
                            foreach ($value as $key => $var) {
                                $operand = ($key == 'start') ? '>=' : '<=';
                                $this->getDataSource()->where($table . '.' . $_column . ' ' . $operand . ' ?', $var);
                            }
                        } else {
                            if ($dataType == 'string') {
                                $this->getDataSource()->where('LOWER(' . $table . '.' . $_column . ') LIKE ?', '%' . strtolower($value) . '%');
                            } else {
                                $this->getDataSource()->where('LOWER(' . $table . '.' . $_column . ') = ?', $value);
                            }
                        }
                    }
                }
            }
        }
        
        $this->setOrder($this->getRequest()->getParam('order') ? $this->getRequest()->getParam('order') : $this->_order);
        
        $this->setSort($this->getRequest()->getParam('sort') ? $this->getRequest()->getParam('sort') : $this->_sort);
        
        if ($this->getSort() && $this->getOrder()) {
            $this->getDataSource()->order($this->getOrder() . ' ' . strtoupper($this->getSort()));
        }
    }
    
}
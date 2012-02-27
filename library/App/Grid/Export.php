<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
abstract class App_Grid_Export extends App_Grid_Gridola
{
    protected $_exportType = null;
    
    protected $_dataSource = null;
    
    protected $_dataGrid = null;
    
    protected $_gridFileName = null;
    
    protected $_export = null;
    
    public function __construct($dataSource, $dataGrid, $dataGridName)
    {
        $this->setDataSource($dataSource)->setDataGrid($dataGrid)->setDataGridName($dataGridName);
        $this->header()->deploy();
        //$this->deploy();
    }
    
    abstract protected function deploy();
    
    abstract protected function header();
    
    protected function setDataSource($dataSource)
    {
        $this->_dataSource = $dataSource;
        return $this;
    }
    
    protected function getDataSource()
    {
    	$dataSource = $this->_dataSource;
        if ($this->_dataSource instanceof Zend_Db_Select) {
        	$dataSource = $this->_dataSource->getAdapter()->fetchAll($dataSource);
        }
        return $dataSource;
    }
    
    protected function setDataGrid($dataGrid)
    {
        $this->_dataGrid = $dataGrid;
        return $this;
    }
    
    protected function getDataGrid()
    {
        return $this->_dataGrid;
    }
    
    protected function setDataGridName($dataGridName)
    {
        $this->_dataGridName = $dataGridName;
        return $this;
    }
    
    protected function getExportType()
    {
        return $this->_exportType;
    }
    
    protected function getDataGridName()
    {
        return $this->_dataGridName;
    }
    
    protected function getColumns($upperCaseNames = true)
    {
        $columns = array();
        foreach($this->getDataGrid() as $row) {
            $columns[] = ($upperCaseNames) ? strtoupper($row['index']) : $row['index'];
        }
        return $columns;
    }
    
    protected function disableLayout()
    {
        if (null !== ($layout = Zend_Layout::getMvcInstance())) {
            $layout->disableLayout();
        }
        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
        return $this;
    }
    
    protected function clean($str)
    {
        $str = str_replace(array("\r", "\n", ','), ' ', $str);
        $str = str_replace('"', '""', $str);
        return stripslashes($str);
    }
    
    protected function cleanData($str)
    {
    	$str = str_replace('"', '""', $str);
    	if(preg_match('/,/', $str) || preg_match("/\n/", $str) || preg_match('/"/', $str)) {
    		return '"'.$str.'"';
    	}
    	return $str;
    }
    
    protected function getGridFileName()
    {
        if (!$this->_gridFileName) {
            $this->_gridFileName = strtolower($this->getDataGridName()) . '-' . date("Ymd") . '.' . $this->getExportType();
        }
        return $this->_gridFileName;
    }
    
    protected function setExport($export)
    {
        $this->_export = $export;
        return $this;
    }
    
    protected function getExport()
    {
        return $this->_export;
    }
    
    protected function export()
    {
        $this->disableLayout()->printIt();
    }
    
    protected function printIt()
    {
        print $this->getExport();
        exit;
    }
}
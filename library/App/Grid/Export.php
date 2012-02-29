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
    
    protected $_settings = array();
    
    public function __construct($dataSource, $dataGrid, $dataGridName, $settings)
    {
        $this->setDataSource($dataSource)->setDataGrid($dataGrid)->setDataGridName($dataGridName)->setSettings($settings);
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
    
    protected function getColumns($upperCaseWords = true)
    {
        $columns = array();
        foreach($this->getDataGrid() as $row) {
            $columns[] = (true === $upperCaseWords) ? ucwords(strtolower($row['header'])) : $row['header'];
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
    
    protected function filter($string)
    {
        $string = str_replace(array("\r","\t","\n",","), " ", $string);
        $string = str_replace('"', '""', $string);
        return stripslashes($string);
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
        $this->_export = (string) $export;
        return $this;
    }
    
    protected function getExport()
    {
        return $this->_export;
    }
    
    protected function setSettings($settings)
    {
        $this->_settings = $settings;
    }
    
    protected function getSettings()
    {
        return $this->_settings;
    }
    
    protected function getRowCount()
    {
    	return count($this->getDataSource());
    }
    
    protected function getColumnCount()
    {
        return count($this->getDataGrid());
    }
    
    protected function showHeader()
    {
        $settings = $this->getSettings();
        if(array_key_exists('header', $settings)) {
            return (true === $settings['header']) ? false : true;
        }
        return true;
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
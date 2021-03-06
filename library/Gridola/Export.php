<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
abstract class Gridola_Export extends Gridola_Grid
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
    
    protected function getDataGridName($pop = true)
    {
        if($pop) {
            $parts = explode('_', $this->_dataGridName);
            $this->_dataGridName = array_pop($parts);
        }
        return $this->_dataGridName;
    }
    
    protected function getColumns($upperCaseWords = true)
    {
        $columns = array();
        foreach ($this->getDataGrid() as $row) {
            $columns[] = (true === $upperCaseWords) ? ucwords(strtolower($row['header'])) : $row['header'];
        }
        return $columns;
    }
    
    protected function getColumnTypes()
    {
        $columnTypes = array();
        foreach ($this->getDataGrid() as $row) {
            $columnTypes[$row['index']] = $row['type'];
        }
        return $columnTypes;
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
        $string = str_replace(array("\r","\t","\n"), " ", $string);
        $string = str_replace('"', '""', $string);
        return stripslashes($string);
    }
    
    protected function getGridFileName()
    {
        if (!$this->_gridFileName) {
            $this->_gridFileName = ucfirst(strtolower($this->getDataGridName())) . '-' . date("Y-m-d") . '-' . substr(md5(microtime().mt_rand()), 0,7) . '.' . $this->getExportType();
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
    
    protected function header()
    {
        header('Content-Description: File Transfer');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        switch($this->getExportType()) {
            case 'csv':
                header("Content-Type: application/" . $this->getExportType());
                break;
            case 'xml';
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                break;
        }
        header('Content-Disposition: attachment; filename="' . $this->getGridFileName() . '"');
        header('Content-Transfer-Encoding: binary');
        return $this;
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
        if (array_key_exists('header', $settings)) {
            return (true === $settings['header']) ? false : true;
        }
        return true;
    }
    
    protected function hasWrite()
    {
        $settings = $this->getSettings();
        if (array_key_exists('write', $settings)) {
            return $settings['write'];
        }
        return false;
    }
    
    protected function writeFile()
    {
        if (true === ($this->hasWrite())) {
        	
            foreach (array('fopen','fwrite','fclose','tempnam','rename','is_dir','mkdir','sys_get_temp_dir') as $_index => $method) {
                if (!function_exists($method)) {
                    return $this;
                }
            }
        	
            $export = $this->getExport();
            
            $writePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR;
            
            if (false === (is_dir($writePath))) {
                mkdir($writePath);
            }
            
            $gridFileName = $this->getGridFileName();
            
            $temporaryName = tempnam(sys_get_temp_dir(), $gridFileName);
            
            $handle = fopen($temporaryName, 'w');
            
            fwrite($handle, $export);
            
            fclose($handle);
            
            rename($temporaryName, $writePath . $gridFileName);    
        }
        
        return $this;
    }
    
    protected function export()
    {
        $this->disableLayout()->writeFile()->printIt();
    }
    
    protected function printIt()
    {
        print $this->getExport();
        exit;
    }
}
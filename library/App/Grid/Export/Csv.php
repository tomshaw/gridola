<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class App_Grid_Export_Csv extends App_Grid_Export
{
    protected $_exportType = 'csv';
    
    protected function header()
    {
    	header('Content-Description: File Transfer');
    	header('Cache-Control: public, must-revalidate, max-age=0');
    	header('Pragma: public');
    	header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    	header("Content-Type: application/csv");
    	header('Content-Disposition: attachment; filename="' . $this->getGridFileName() . '"');
    	header('Content-Transfer-Encoding: binary');
    	return $this;
    }
    
    protected function deploy()
    {
    	//$dataGrid = $this->getDataGrid();
    	
    	$dataSource = $this->getDataSource();
    	if($dataSource instanceof Zend_Db_Select) {
    		$dataSource = $dataSource->getAdapter()->fetchAll($dataSource);
    	}

    	$flag = false;
    	$string = '';
    	foreach($dataSource as $row) {
    		array_map(array($this, 'cleanData'), $row);
    		$data = (array) $row;
    		if(!$flag) {
    			$string.= strtoupper(preg_replace('/[_]+/', ' ', implode(", ", array_keys($data)))) . "\n";
    			$flag = true;
    		}
    		$string.= implode(", ", array_values($data)) . "\n";
    	}
    	
    	$this->setExport($string)->export();
    	
    	exit;
    }
}
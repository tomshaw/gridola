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
        header("Content-Type: application/" . $this->getExportType());
        header('Content-Disposition: attachment; filename="' . $this->getGridFileName() . '"');
        header('Content-Transfer-Encoding: binary');
        return $this;
    }
    
    /**
     * Important note. A column header that start with an upper case ID in column A1 
     * throws errors when opening in excel. This is a qwirk with Microsoft Excel. The
     * error message is when Excel thinks the file is in SLYK format.
     * 
     * (non-PHPdoc)
     * @see App_Grid_Export::deploy()
     */
    protected function deploy()
    {
        $rows = $this->getDataSource();
        
        $rowCount = $this->getRowCount();
        
        $columns = $this->getColumns(true);
        
        $columnCount = $this->getColumnCount();
        
        $header = $this->showHeader();
        
        $string = '';
        foreach($rows as $row) {
            array_map(array($this, 'filter'), $row);
            $data = (array) $row;
            if(!$header) {
                $string .= ucwords(strtolower(implode(", ", array_keys($data)))) . "\n";
                $header = true;
            }
            $string .= implode(", ", array_values($data)) . "\n";
        }
        
        $this->setExport($string);
    }
}
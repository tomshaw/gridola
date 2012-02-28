<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class App_Grid_Export_Xml extends App_Grid_Export
{
    protected $_exportType = 'xml';
    
    protected function header()
    {
        header('Content-Description: File Transfer');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header("Content-Type: application/excel");
        header('Content-Disposition: attachment; filename="' . $this->getGridFileName() . '"');
        header('Content-Transfer-Encoding: binary');
        return $this;
    }
    
    protected function deploy()
    {
        $rows = $this->getDataSource();
        
        $dataCount = count($rows);
        
        $columns = $this->getColumns(true);
        
        $columnCount = count($columns);
        
        $string = '';
        for ($i = 0; $i < $columnCount; $i++) {
            $string .= $columns[$i];
            $string .= ($i < $columnCount - 1) ? "\t" : "\n";
        }
        
        foreach ($rows as $row) {
            $count = 0;
            foreach ($row as $_index => $value) {
                if (is_null($value) || empty($value)) {
                    $string .= '-';
                } elseif ($value == '0' || $value != '') {
                    $string .= str_replace("\t", "", str_replace("\r", "", $value));
                }
                $string .= ($count < $columnCount - 1) ? "\t" : "\n";
                $count++;
            }
        }
        
        $this->setExport($string);
    }
}
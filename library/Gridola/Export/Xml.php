<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class Gridola_Export_Xml extends Gridola_Export
{
    protected $_exportType = 'xml';
    
    protected function header()
    {
        header('Content-Description: File Transfer');
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $this->getGridFileName() . '"');
        header('Content-Transfer-Encoding: binary');
        return $this;
    }
    
    /**
     * SpreadsheetML
     * 
     * @see http://msdn.microsoft.com/en-us/library/aa140066(v=office.10).aspx
     * @see http://www.codeproject.com/Articles/11775/Generating-Excel-XML-Spreadsheet-in-C
     * @see http://blogs.msdn.com/b/brian_jones/archive/2005/06/27/433152.aspx
     * 
     * (non-PHPdoc)
     * @see Gridola_Export::deploy()
     */
    protected function deploy()
    {
        $rows = $this->getDataSource();
        
        $rowCount = $this->getRowCount();
        
        $columns = $this->getColumns(true);
        
        $columnCount = $this->getColumnCount();
        
        $columnTypes = $this->getColumnTypes();
        
        $header = $this->showHeader();
        
        $spreadsheet = '<?xml version="1.0" encoding="utf-8"?>';
        $spreadsheet .= '<?mso-application progid="Excel.Sheet"?>';
        $spreadsheet .= '<Workbook xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        
        $spreadsheet .= '<ss:Styles>';
        $spreadsheet .= '<ss:Style ss:ID="Default" ss:Name="Normal"><ss:Font ss:Color="blue"/></ss:Style>';
        $spreadsheet .= '<ss:Style ss:ID="ColumnHeader"><ss:Font ss:Bold="1" ss:Color="red"/></ss:Style>';
        $spreadsheet .= '</ss:Styles>';
        
        $spreadsheet .= '<Worksheet ss:Name="' . $this->getGridFileName() . '" ss:Description="' . $this->getGridFileName() . '">';
        
        $spreadsheet .= '<ss:Table>';
        
        for ($i = 0; $i < $columnCount; $i++) {
            $spreadsheet .= '<ss:Column ss:Width="120"/>';
        }
        
        if ($header == false) {
            $spreadsheet .= '<ss:Row ss:StyleID="ColumnHeader">';
            foreach ($columns as $column) {
                $spreadsheet .= '<ss:Cell><Data ss:Type="String">' . $column . '</Data></ss:Cell>';
            }
            $spreadsheet .= '</ss:Row>';
        }
        
        foreach ($rows as $data) {
            $spreadsheet .= '<ss:Row>';
            foreach ($data as $_index => $value) {
                if (isset($columnTypes[$_index])) {
                    $dataTypes = $columnTypes[$_index];
                    switch ($dataTypes) {
                        case 'number';
                            $dataType = 'Number';
                            break;
                        case 'text';
                            $dataType = 'String';
                            break;
                        case 'datetime';
                            $dataType  = 'DateTime';
                            $timestamp = strtotime($value);
                            $value     = strftime("%Y-%m-%d", $timestamp);
                            break;
                        case 'options';
                            $dataType = 'String';
                            break;
                        default:
                            $dataType = 'String';
                            break;
                    }
                    $spreadsheet .= '<ss:Cell><Data ss:Type="' . $dataType . '">' . $value . '</Data></ss:Cell>';
                }
            }
            $spreadsheet .= '</ss:Row>';
        }
        
        $spreadsheet .= '</ss:Table>';
        $spreadsheet .= '</Worksheet>';
        $spreadsheet .= '</Workbook>';
        
        $this->setExport($spreadsheet);
    }
}
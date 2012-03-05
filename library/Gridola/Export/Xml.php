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
    
    protected function findDataType($value)
    {
        // preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $value)
        if (preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-3]:[0-5][0-9]:[0-5][0-9]$/", $value)) {
            $type = 'DateTime';
        } else if (!is_numeric($value)) {
            $type = 'String';
        } else {
            $type = 'Number';
        }
        return $type;
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
        
        $header = $this->showHeader();
        
        $spreadsheet = '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?><Workbook xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        
        $spreadsheet .= '<ss:Styles>';
        $spreadsheet .= '<ss:Style ss:ID="Default" ss:Name="Normal"><ss:Font ss:Color="blue"/></ss:Style>';
        //$spreadsheet .= '<ss:Style ss:ID="xl1"><ss:Font ss:Bold="1"/></ss:Style>';
        $spreadsheet .= '<ss:Style ss:ID="Row1"><ss:Font ss:Bold="1" ss:Color="red"/></ss:Style>';
        $spreadsheet .= '</ss:Styles>';
        
        $spreadsheet .= '<Worksheet ss:Name="' . $this->getGridFileName() . '" ss:Description="' . $this->getGridFileName() . '"><ss:Table>';
        
        foreach ($rows[0] as $_index => $value) {
            $spreadsheet .= '<ss:Column ss:Width="120"/>';
        }
        
        if ($header == false) {
            $spreadsheet .= '<ss:Row ss:StyleID="Row1">';
            foreach ($columns as $column) {
                $spreadsheet .= '<ss:Cell><Data ss:Type="' . $this->findDataType($column) . '">' . $column . '</Data></ss:Cell>';
            }
            $spreadsheet .= '</ss:Row>';
        }
        
        foreach ($rows as $data) {
            $spreadsheet .= '<ss:Row>';
            foreach ($data as $_index => $value) {
                $dataType = $this->findDataType($value);
                if ($dataType == 'DateTime') {
                    $timestamp = strtotime($value);
                    $value     = strftime("%Y-%m-%d", $timestamp);
                }
                $spreadsheet .= '<ss:Cell><Data ss:Type="' . $this->findDataType($value) . '">' . $value . '</Data></ss:Cell>';
            }
            $spreadsheet .= '</ss:Row>';
        }
        
        $spreadsheet .= '</ss:Table></Worksheet>';
        
        $spreadsheet .= '</Workbook>';
        
        $this->setExport($spreadsheet);
    }
}
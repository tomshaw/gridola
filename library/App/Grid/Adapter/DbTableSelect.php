<?php 

class App_Grid_Adapter_DbTableSelect extends App_Grid_DataSource
{

	public function __construct(Zend_Db_Table_Select $dataSource)
	{
		$this->setDataSource($dataSource);
	}
	
	public function processDataSource()
	{
		return $this;
	}	
}
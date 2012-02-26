<?php 

class App_Grid_Adapter_Table extends App_Grid_DataSource
{

	public function __construct(Zend_Db_Table_Rowset $dataSource)
	{
		$this->setDataSource($dataSource);
	}
	
	public function processDataSource()
	{
		return $this;
	}	
}
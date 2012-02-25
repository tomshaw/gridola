<?php 

class App_Grid_Adapter_Table extends App_Grid_DataSource
{

	public function __construct(Zend_Db_Table_Rowset $dataSource)
	{
		// $dataSource->toArray()
		$this->setDataSource($dataSource);
		parent::__construct();
	}
	
	public function checkData($grid)
	{
		return $this;
	}
	
	public function results()
	{
		return $this;
	}
	
	public function setSortOrder()
	{
		return $this;
	}
	
}
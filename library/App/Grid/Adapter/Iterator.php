<?php 

class App_Grid_Adapter_Iterator extends App_Grid_DataSource
{

	public function __construct(Iterator $dataSource)
	{
		$this->setDataSource($dataSource);
		parent::__construct();
	}
	
	public function checkData($grid)
	{
		return $this;
	}
	
}
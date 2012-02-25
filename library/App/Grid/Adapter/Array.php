<?php 

class App_Grid_Adapter_Array extends App_Grid_DataSource
{

	public function __construct(array $dataSource)
	{
		$this->setDataSource($dataSource);
		parent::__construct();
	}
	
}
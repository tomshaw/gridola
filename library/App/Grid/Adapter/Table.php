<?php 

class App_Grid_Adapter_Table extends App_Grid_DataSource
{

	public function __construct(Zend_Db_Select $dataSource)
	{
		//$rowsetArray = $dataSource->toArray();
		$this->setDataSource($dataSource);
		parent::__construct();
	}
	
}
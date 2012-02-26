<?php 

class App_Grid_Adapter_DbTableSelect extends App_Grid_Adapter_DbSelect
{

	public function __construct(Zend_Db_Table_Select $dataSource)
	{
		$this->setDataSource($dataSource);
	}
}
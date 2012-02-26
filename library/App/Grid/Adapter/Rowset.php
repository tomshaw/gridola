<?php 

class App_Grid_Adapter_Rowset extends App_Grid_DataSource
{

	public function __construct(Zend_Db_Table_Rowset $dataSource)
	{
		$this->setDataSource($dataSource);
	}
	
	public function processDataSource()
	{	
		$array = $this->getDataSource()->toArray();
		
		$order = $this->getRequest()->getParam('order') ? $this->getRequest()->getParam('order') :  $this->getOrder();
		
		$sort = $this->getRequest()->getParam('sort') == 'desc' ? SORT_ASC : SORT_DESC;
		
		$this->setDataSource($this->arraySortByColumn($array, $order, $sort));

	}
		
}
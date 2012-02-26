<?php 

class App_Grid_Adapter_Array extends App_Grid_DataSource
{

	public function __construct(array $dataSource)
	{
		$this->setDataSource($dataSource);
	}
	
	public function processDataSource()
	{
		$array = $this->getDataSource();
	
		$order = $this->getRequest()->getParam('order') ? $this->getRequest()->getParam('order') :  $this->getOrder();
	
		$sort = $this->getRequest()->getParam('sort') == 'desc' ? SORT_ASC : SORT_DESC;
	
		$data = $this->arraySortByColumn($array, $order, $sort);
	
		$this->setDataSource($data);
	
		return $this;
	}
	
}
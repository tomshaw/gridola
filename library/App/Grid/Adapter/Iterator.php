<?php 

class App_Grid_Adapter_Iterator extends App_Grid_DataSource
{

	public function __construct(Iterator $dataSource)
	{
		$this->setDataSource($dataSource);
	}
	
	public function processDataSource()
	{
		$array = $this->getDataSource();
	
		$order = $this->getRequest()->getParam('order') ? $this->getRequest()->getParam('order') :  $this->getOrder();
	
		$sort = $this->getRequest()->getParam('sort') == 'desc' ? SORT_ASC : SORT_DESC;
	
		//$data = $this->arraySortByColumn($array, $order, $sort);
	
		//$this->setDataSource($data);
	
		return $this;
	}
	
	private function arraySortByColumn($dataSets, $column, $dir = SORT_ASC)
	{
		$sortColumn = array();
		foreach ($dataSets as $key=> $row) {
			$sortColumn[$key] = $row[$column];
		}
		array_multisort($sortColumn, $dir, $dataSets);
		return $dataSets;
	}
	
}
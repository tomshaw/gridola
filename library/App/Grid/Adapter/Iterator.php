<?php 

class App_Grid_Adapter_Iterator extends App_Grid_DataSource
{

	public function __construct(Iterator $dataSource)
	{
		$this->setDataSource($dataSource);
	}
	
	public function processDataSource()
	{
		$iterator = $this->getDataSource();
	
		$array = array();
		foreach($iterator as $_index => $value) {
			if(is_array($value)) {
				foreach($value as $r) {
					$array[] = $r;
				}
			}
		}

		$order = $this->getRequest()->getParam('order') ? $this->getRequest()->getParam('order') :  $this->getOrder();
	
		$sort = $this->getRequest()->getParam('sort') == 'desc' ? SORT_ASC : SORT_DESC;
		
		$this->setDataSource($this->arraySortByColumn($array, $order, $sort));

	}
	
}
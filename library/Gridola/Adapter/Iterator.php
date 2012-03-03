<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class Gridola_Adapter_Iterator extends Gridola_DataSource
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
                foreach($value as $row) {
                    $array[] = $row;
                }
            }
        }

        $order = $this->getRequest()->getParam('order') ? $this->getRequest()->getParam('order') :  $this->getOrder();
	
        $sort = $this->getRequest()->getParam('sort') == 'desc' ? SORT_ASC : SORT_DESC;
		
        $this->setDataSource($this->arraySortByColumn($array, $order, $sort));
    }	
}
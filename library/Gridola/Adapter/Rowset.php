<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class Gridola_Adapter_Rowset extends Gridola_DataSource
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
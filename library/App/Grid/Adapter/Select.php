<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class App_Grid_Adapter_Select extends App_Grid_DataSource
{
	protected $_select = null;
	
	public function __construct(Zend_Db_Select $select)
	{
		$this->_select = $select;
		Zend_Debug::dump($this->_select);
	}	
}
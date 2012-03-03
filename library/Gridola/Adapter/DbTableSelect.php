<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class Gridola_Adapter_DbTableSelect extends Gridola_Adapter_DbSelect
{
    public function __construct(Zend_Db_Table_Select $dataSource)
    {
        $this->setDataSource($dataSource);
    }
}
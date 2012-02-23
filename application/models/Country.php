<?php

class Model_Country extends Zend_Db_Table_Abstract
{
    protected $_name = 'country';
    
    public function fetchUniqueContinents()
    {
        $select = $this->select()
        	->distinct()
        	->from($this->_name, array('Continent'))
        	->order('Continent ASC');
        return $this->fetchAll($select);
    }
    
    public function fetchUniqueRegions()
    {
        $select = $this->select()
        	->distinct()
        	->from($this->_name, array('Region'))
        	->order('Region ASC');
        return $this->fetchAll($select);
    }
}
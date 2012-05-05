<?php

class Model_City extends Zend_Db_Table_Abstract
{
    protected $_name = 'city';
    
    public function getColumns()
    {
    	return $this->info(self::COLS);
    }
    
    public function getTableName()
    {
    	return $this->_name;
    }
    
    public function fetchCityData()
    {
    	$select = $this->select()
    	    ->setIntegrityCheck(false)
    	    ->from($this->_name, array('ID','Name','CountryCode','District','Population'))
    	    ->joinLeft(array('country' => 'country'), 'country.Code = city.CountryCode', array('Region','Continent'));
    	return $select; // Zend_Db_Select
    	//return $this->fetchAll($select); // Zend_Db_Table_Rowset
    	//return $this->getAdapter()->fetchAll($select); // Array
    	//return new ArrayIterator((array) $this->fetchAll($select)); // Iterator
    }
    
    public function getCountryCodeOptions()
    {
        $select = $this->select()
            ->from($this->_name, array('CountryCode'))
            ->order('CountryCode ASC');
        return $this->fetchAll($select);
    }
}
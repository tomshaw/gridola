<?php

class Model_City extends Zend_Db_Table_Abstract
{
    protected $_name = 'city';
    
    public function fetchCityData()
    {
    	$select = $this->select()
    	    ->setIntegrityCheck(false)
    	    ->from($this->_name, array('ID','Name','CountryCode','District','Population'))
    	    ->joinLeft(array('country' => 'country'), 'country.Code = city.CountryCode', array('Region','Continent'));
    	return $select;
    	//return $this->fetchAll($select);
    	//return $this->getAdapter()->fetchAll($select);
    	//return new ArrayIterator((array) $this->fetchAll($select));
    }
    
    public function getCountryCodeOptions()
    {
        $select = $this->select()
            ->from($this->_name, array('CountryCode'))
            ->order('CountryCode ASC');
        return $this->fetchAll($select);
    }
}
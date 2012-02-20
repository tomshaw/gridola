<?php

class Model_City extends Zend_Db_Table_Abstract
{
    protected $_name = 'city';
    
    public function getColumns()
    {
        return $this->info(self::COLS);
    }
    
    public function findCities()
    {
        return $this->select()
            ->setIntegrityCheck(false)
            ->from($this->_name, array('ID','Name','CountryCode','District','Population'));
    }
    
    public function findCityData()
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this->_name, array('ID','Name','CountryCode','District','Population'))
            ->joinLeft(array('country' => 'country'), 'country.Code = city.CountryCode', array('Region','Continent'));
        return $select;
    }
    
    public function getCountryCodeOptions()
    {
        $select = $this->select()
            ->from($this->_name, array('CountryCode'))
            ->order('CountryCode ASC');
        return $this->fetchAll($select);
    }
}
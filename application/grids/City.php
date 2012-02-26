<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class Grid_City extends App_Grid_Abstract
{
    protected $_exportTypes = array('csv', 'xml');
    // table-striped table-condensed table-bordered
    protected $_tableClass = 'table table-condensed';
    
    public function __construct()
    {
        $this->setFormId('city_grid');
        $this->setOrder('ID');
        $this->setSort('ASC');
        $this->setScrollType('Jumping'); // All, Elastic, Jumping, Sliding
        //$this->setTemplate('index/citiesgrid');
        //$this->setPaginatorPartial('index/gridpagination');
        parent::__construct();
    }
    
    protected function _prepareDataSource()
    {
        $model = new Model_City();
        $this->setDataSource($model->fetchCityData());
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => 'ID',
            'align' => 'center',
            'width' => '7%',
            'type' => 'number',
            'index' => 'ID'
        ));
        
        $this->addColumn('name', array(
            'header' => 'City Name',
            'align' => 'right',
            'width' => '200px',
            'type' => 'text',
            'index' => 'Name'
        ));
        
        $this->addColumn('code', array(
            'header' => 'Country Code',
            'align' => 'center',
            'width' => '200px',
            'type' => 'options',
            'index' => 'CountryCode',
            'options' => $this->getCountryCodeOptions()
        ));
        
        $this->addColumn('district', array(
            'header' => 'District',
            'width' => '200px',
            'type' => 'text',
            'index' => 'District'
        ));
        
        $this->addColumn('population', array(
            'header' => 'Population',
            'align' => 'left',
            'width' => '200px',
            'type' => 'text',
            'index' => 'Population'
        ));
        
        $this->addColumn('region', array(
            'header' => 'Region',
            'width' => '200px',
            'type' => 'options',
            'index' => 'Region',
            'options' => $this->fetchUniqueRegions()
        ));
        
        $this->addColumn('continent', array(
            'header' => 'Continent',
            'width' => '200px',
            'type' => 'options',
            'index' => 'Continent',
            'options' => $this->fetchUniqueContinents()
        ));
    }
    
    protected function _prepareActions()
    {
        $this->addAction('delete', array(
            'label' => 'Delete',
            'title' => 'Delete This Entry',
            'field' => 'ID',
            'url' => array(
                'module' => 'default',
                'controller' => 'index',
                'action' => 'delete'
            )
        ));
        
        $this->addAction('edit', array(
            'label' => 'Edit',
            'title' => 'Edit This Row',
            'field' => 'ID',
            'url' => array(
                'module' => 'default',
                'controller' => 'index',
                'action' => 'edit'
            )
        ));
    }
    
    protected function _prepareMassActions()
    {
        $this->setMassactionField('ID');
        
        $this->addMassAction('delete_selected', array(
            'label' => 'Delete Selected',
            'url' => array(
                'module' => 'default',
                'controller' => 'index',
                'action' => 'delete'
            )
        ));
        
        $this->addMassAction('edit_selected', array(
            'label' => 'Edit Selected',
            'url' => array(
                'module' => 'default',
                'controller' => 'index',
                'action' => 'edit'
            )
        ));
    }
    
    protected function _prepareRowClickUrl()
    {
        $this->setRowClickUrl(array(
            'field' => 'ID', 
            'url' => array(
                'module' => 'default', 
                'controller' => 'index', 
                'action' => 'edit')
            )
        );
    }
    
    protected function _prepareCycleColors()
    {
        $this->setCycleColors(array("#EFEFEF", "#F9F9F9"));
    }
    
    protected function _prepareOnMouseOverColor()
    {
        $this->setOnMouseOverColor('#DBDFE2');
    }
    
    /**
     * Encapsulated functionality.
     */
    
    private function fetchUniqueRegions()
    {
        $model = new Model_Country();
        $rows  = $model->fetchUniqueRegions();
        
        $data = array('-1'=>'');
        foreach ($rows as $row) {
            $data[$row->Region] = $row->Region;
        }
        return $data;
    }
    
    private function fetchUniqueContinents()
    {
        $model = new Model_Country();
        $rows  = $model->fetchUniqueContinents();
        
        $data = array('-1'=>'');
        foreach ($rows as $row) {
            $data[$row->Continent] = $row->Continent;
        }
        return $data;
    }
    
    private function getCountryCodeOptions()
    {
        $model = new Model_City();
        $rows  = $model->getCountryCodeOptions();
        
        $data = array('-1'=>'');
        foreach ($rows as $row) {
            $data[$row->CountryCode] = $row->CountryCode;
        }
        return $data;
    }
}
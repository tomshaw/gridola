<?php
/*!
* Gidola Zend Framework 1.x Grid
* Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
* MIT Licensed
*/
class Grid_City extends App_Grid_Abstract 
{
	protected $_exportTypes = array('csv','xml');
	
	protected $_rowClickUrl = array(
		'field' => 'ID', 
		'url' => array(
			'module' => 'default',
			'controller' => 'index', 
			'action' => 'edit'
    ));
	
	protected $_cycleColors = array("#EFEFEF","#F9F9F9");
	
	protected $_onMouseOverColor = '#DBDFE2';

	public function __construct()
	{
		$this->setFormId('city_grid');
		$this->setOrder('Name');
		$this->setSort('DESC');
		parent::__construct();
	}
	
	protected function _prepareData()
	{
		$model = new Model_City();
		$this->setSelect($model->findCityData());
		return parent::_prepareData();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
			'header'=> 'ID',
			'width' => '100px',
			'type'  => 'number',
			'index' => 'ID',
		));
		
		$this->addColumn('name', array(
			'header'=> 'City Name',
			'width' => '200px',
			'type'  => 'text',
			'index' => 'Name',
		));
		
		$this->addColumn('code', array(
			'header'=> 'Country Code',
			'align' => 'center',
			'width' => '200px',
			'type'  => 'options',
			'index' => 'CountryCode',
			'options' => $this->getCountryCodeOptions(),
		));
		
		$this->addColumn('district', array(
			'header'=> 'District',
			'width' => '200px',
			'type'  => 'text',
			'index' => 'District',
		));
		
		$this->addColumn('population', array(
			'header'=> 'Population',
			'width' => '200px',
			'type'  => 'text',
			'index' => 'Population',
		));
		
		$this->addColumn('region', array(
			'header'=> 'Region',
			'width' => '200px',
			'type'  => 'text',
			'index' => 'Region',
		));
		
		$this->addColumn('continent', array(
			'header'=> 'Continent',
			'width' => '200px',
			'type'  => 'text',
			'index' => 'Continent',
		));
		
		$this->addColumn('created_at', array(
			'header'=> 'Created',
			'width' => '200px',
			'type'  => 'datetime',
			'index' => 'created_at',
		));
	}
	
	protected function _prepareActions()
	{		
		$this->addAction('delete', array(
			'label' => 'Delete',
			'field' => 'ID',
			'url' => array(
				'module' => 'default',
				'controller' => 'index',
				'action' => 'delete'
				)
			)
		);
		
		$this->addAction('edit', array(
			'label' => 'Edit',
			'field' => 'ID',
			'url' => array(
				'module' => 'default',
				'controller' => 'index',
				'action' => 'edit'
				)
			)
		);
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
			)
		);
		
		$this->addMassAction('edit_selected', array(
			'label' => 'Edit Selected',
			'url' => array(
				'module' => 'default',
				'controller' => 'index',
				'action' => 'edit'
				)
			)
		);
	}
	
	protected function _prepareRowClickUrl()
	{
		return $this->_rowClickUrl;
	}
	
	protected function _prepareCycleColors()
	{
		return $this->_cycleColors;
	}
	
	protected function _prepareOnMouseOverColor()
	{
		return $this->_onMouseOverColor;
	}
	
	/**
	 * Encapsulated functionality.
	 */
	
	private function getCountryCodeOptions()
	{
		$model = new Model_City();
		$rows = $model->getCountryCodeOptions();
		
		$data = array();
		foreach($rows as $row) {
			$data[$row->CountryCode] = $row->CountryCode; 
		}
		return $data;
	}

}
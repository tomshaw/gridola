<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
abstract class App_Grid_Abstract extends App_Grid_Gridola
{
    protected $_columns = array();
    
    protected $_dataSource = null;
    
    protected $_formId = 'gridola';
    
    protected $_template = 'grid';
    
    protected $_templateExtension = '.phtml';
    
    protected $_exportTypes = array();
    
    protected $_Sort = null;
    
    protected $_Order = null;
    
    protected $_itemsPerPage = 10;
    
    protected $_massActionField = null;
    
    protected $_actions = array();
    
    protected $_massActions = array();
    
    protected $_rowClickUrl = array();
    
    protected $_cycleColors = array();
    
    protected $_onMouseOverColor = null;
    
    protected $_tableClass = null;
    
    protected $_scrollingTypes = array('All','Elastic','Jumping','Sliding');
    
    protected $_scrollType = null;
    
    protected $_paginatorPartial = 'gridpagination';
    
    protected $_dataGridMethods = null;
    
    protected $_configMethods = array(
    	'_prepareDataSource',
    	'_prepareColumns',
    	'_prepareActions',
    	'_prepareMassActions',
    	'_prepareRowClickUrl',
    	'_prepareCycleColors',
    	'_prepareOnMouseOverColor',
        '_prepareExport'
    );
    
    protected $_export = null;
    
    public function __construct()
    {
    	parent::__construct();
        $this->_processData();
    }
    
    protected function getExportType()
    {
    	$export = $this->getRequest()->getParam('export');
    	if($export != '-1') {
           return $export;
    	}
    	return null;
    }
    
    protected function getExport()
    {
    	return $this->_export;
    }
    
    protected function getDataGrid()
    {
        return $this->_columns;
    }
    
    protected function getDataGridName()
    {
        return get_class($this);
    }
    
    protected function setDataSource($dataSource)
    {
        $this->_dataSource = $dataSource;
    }
    
    protected function getDataSource()
    {
        return $this->_dataSource;
    }
    
    protected function getConfigMethods()
    {
    	return $this->_configMethods;
    }
    
    protected function setFormId($formId)
    {
        $this->_formId = $formId;
    }
    
    protected function getFormId()
    {
        return $this->_formId;
    }
    
    protected function setTemplate($template)
    {
        $this->_template = $template;
    }
    
    protected function getTemplate()
    {
        return $this->_template . $this->getTemplateExtension();
    }
    
    protected function getTableClass()
    {
    	return $this->_tableClass;
    }
    
    protected function getTemplateExtension()
    {
    	return $this->_templateExtension;
    }
    
    protected function setExportTypes($exportTypes = array())
    {
        $this->_exportTypes = $exportTypes;
    }
    
    protected function getExportTypes()
    {
        return $this->_exportTypes;
    }
    
    protected function setSort($sort)
    {
        $this->_sort = $sort;
    }
    
    protected function getSort()
    {
        return $this->_sort;
    }
    
    protected function setOrder($order)
    {
        $this->_order = $order;
    }
    
    protected function getOrder()
    {
        return $this->_order;
    }
    
    protected function setItemsPerPage($itemsPerPage)
    {
    	$this->_itemsPerPage = $itemsPerPage;
    }
    
    protected function getItemsPerPage()
    {
    	return $this->_itemsPerPage;
    }
    
    protected function setMassactionField($field)
    {
        $this->_massActionField = $field;
    }
    
    protected function getMassactionField()
    {
        return $this->_massActionField;
    }
    
    protected function getActions()
    {
        return $this->_actions;
    }
    
    protected function getMassActions()
    {
        return $this->_massactions;
    }
    
    protected function setRowClickUrl($url)
    {
    	$this->_rowClickUrl = $url;
    }
    
    protected function getRowClickUrl()
    {
    	return $this->_rowClickUrl;
    }
    
    protected function setCycleColors($cycleColors)
    {
    	$this->_cycleColors = $cycleColors;
    }
    
    protected function getCycleColors()
    {
    	return $this->_cycleColors;
    }
    
    protected function setOnMouseOverColor($onMouseOverColor)
    {
    	$this->_onMouseOverColor = $onMouseOverColor;
    }
    
    protected function getOnMouseOverColor()
    {
    	return $this->_onMouseOverColor;
    }
    
    protected function getScrollingTypes($index = null)
    {
        if($index) {
            if(isset($this->_scrollingTypes[$index])) {
                return $this->_scrollingTypes[$index]; 
            }
        }
        return $this->_scrollingTypes;
    }
    
    protected function setScrollType($scrollType)
    {
    	$this->_scrollType = $scrollType;
    }
    
    protected function getScrollType()
    {
    	return $this->_scrollType;
    }
    
    protected function setPaginatorPartial($partial)
    {
    	$this->_paginatorPartial = $partial;
    }
    
    protected function getPaginatorPartial()
    {
    	return $this->_paginatorPartial  . $this->getTemplateExtension();
    }
    
    public function getDataGridMethods()
    {
        if (null === $this->_dataGridMethods) {
            $prefix = '_';
            $methodNames = get_class_methods($this);
            $this->_dataGridMethods = array();
            foreach ($methodNames as $method) {
                if ('_prepare' === substr($method, 0, 8)) {
                    $this->_dataGridMethods[$prefix.lcfirst(substr($method, 8))] = $method;
                }
            }
        }
        return $this->_dataGridMethods;
    }
    
    protected function _processData()
    {
        $dataGridMethods = $this->getDataGridMethods();
    	
        $configMethods = array_flip($this->getConfigMethods());
    	
        foreach($dataGridMethods as $property => $method) {
            if (!array_key_exists($method, $configMethods)) {
                continue;
            }
            if(!sizeof($this->$property) || is_null($this->$property)) {
                $return = $this->$method();
            }
        }
        return parent::_processData();
    }
    
    protected function addColumn($key, $data = array())
    {
        $this->_columns[$key] = $data;
    }
    
    protected function addAction($key, $data = array())
    {
        $this->_actions[$key] = $data;
    }
    
    protected function addMassAction($key, $data = array())
    {
        $this->_massactions[$key] = $data;
    }
    
    protected function addExport($key, $data = array())
    {
    	$this->_export[$key] = $data;
    }
    
    abstract protected function _prepareDataSource();
    
    abstract protected function _prepareColumns();
    
    abstract protected function _prepareActions();
    
    abstract protected function _prepareMassActions();
    
    abstract protected function _prepareRowClickUrl();
    
    abstract protected function _prepareCycleColors();
    
    abstract protected function _prepareOnMouseOverColor();

    abstract protected function _prepareExport();
}
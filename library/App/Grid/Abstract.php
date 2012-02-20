<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
abstract class App_Grid_Abstract extends App_Grid_Gridola
{
    protected $_grid = array();
    
    protected $_select = null;
    
    protected $_formId = 'gridola';
    
    protected $_template = 'grid';
    
    protected $_templateExtension = '.phtml';
    
    protected $_exportTypes = array();
    
    protected $_Sort = null;
    
    protected $_Order = null;
    
    protected $_massActionField = null;
    
    protected $_actions = array();
    
    protected $_massactions = array();
    
    protected $_rowClickUrl = array();
    
    protected $_cycleColors = array();
    
    protected $_onMouseOverColor = null;
    
    public function __construct()
    {
        $this->_prepareData();
    }
    
    protected function getGrid()
    {
        return $this->_grid;
    }
    
    protected function getRowClickUrl()
    {
        return $this->_rowClickUrl;
    }
    
    protected function getCycleColors()
    {
        return $this->_cycleColors;
    }
    
    protected function getOnMouseOverColor()
    {
        return $this->_onMouseOverColor;
    }
    
    protected function setSelect(Zend_Db_Table_Select $select)
    {
        $this->_select = $select;
    }
    
    protected function getSelect()
    {
        return $this->_select;
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
    
    protected function _prepareData()
    {
        if (is_null($this->getSelect())) {
            $this->_prepareData();
        }
        if (!sizeof($this->getGrid())) {
            $this->_prepareColumns();
        }
        if (sizeof($this->getGrid())) {
            $this->_prepareActions();
            $this->_prepareMassActions();
        }
        return parent::_prepareData();
    }
    
    protected function addColumn($_index, $data = array())
    {
        $this->_grid[$_index] = $data;
    }
    
    protected function addAction($actionId, $data = array())
    {
        $this->_actions[$actionId] = $data;
    }
    
    protected function addMassAction($actionId, $data = array())
    {
        $this->_massactions[$actionId] = $data;
    }
    
    abstract protected function _prepareColumns();
    
    abstract protected function _prepareActions();
    
    abstract protected function _prepareMassActions();
    
    abstract protected function _prepareRowClickUrl();
    
    abstract protected function _prepareCycleColors();
    
    abstract protected function _prepareOnMouseOverColor();   
}
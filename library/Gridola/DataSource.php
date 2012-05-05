<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
abstract class Gridola_DataSource extends Gridola_Grid
{
    protected $_dataSource = null;
    
    protected $_dataGrid = null;
    
    protected $_columns = array();
    
    protected $_order = null;
    
    protected $_sort = null;
    
    protected $_itemsPerPage = 10;
    
    abstract public function processDataSource();
    
    public function setDataSource($dataSource)
    {
        $this->_dataSource = $dataSource;
        return $this;
    }
    
    public function getDataSource()
    {
        return $this->_dataSource;
    }
    
    public function setDataGrid($dataGrid)
    {
        $this->_dataGrid = $dataGrid;
        return $this;
    }
    
    public function getDataGrid()
    {
        return $this->_dataGrid;
    }
    
    protected function setSort($sort)
    {
        $this->_sort = $sort;
        return $this;
    }
    
    protected function getSort()
    {
        return $this->_sort;
    }
    
    protected function setOrder($order)
    {
        $this->_order = $order;
        return $this;
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
    
    public function initialize($dataGrid, $sort, $order, $itemsPerPage)
    {
        $this->setOrder($order)->setSort($sort);
        
        $this->setItemsPerPage($itemsPerPage);
        
        $this->setDataGrid($dataGrid);
        
        if($this->hasExport()) {
            return $this->processDataSource();
        }
        
        if ($this->getRequest()->isPost()) {
            $this->clearSession();
        } elseif ((null === $this->getRequest()->getParam('page')) && (null === ($this->getRequest()->getParam('sort')))) {
            $this->clearSession();
        }
        
        $this->processDataSource();
    }
    
    public function getData()
    {
        $paginator = Zend_Paginator::factory($this->getDataSource());
        
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page', '1'));
        
        $paginator->setItemCountPerPage($this->getItemsPerPage());
        
        return $paginator;
    }
    
    protected function arraySortByColumn($dataSets, $column, $dir = SORT_ASC)
    {
        $sortColumn = array();
        foreach ($dataSets as $key => $row) {
            $sortColumn[$key] = $row[$column];
        }
        array_multisort($sortColumn, $dir, $dataSets);
        return $dataSets;
    }
    
    protected function hasExport()
    {
        $export = $this->getRequest()->getParam('export');
        // @todo define this array somewhere else.
        if(in_array($export, array('csv','xml','xls'))) {
            return true;
        }
        return false;
    }
    
    protected function getPostFilter()
    {
        $callback = function($data) use (&$callback) {
            if (is_array($data)) {
                return array_filter($data, $callback);
            }
            if ($data != '' && $data != '-1') {
                return true;
            }
        };
        return array_filter($this->getRequest()->getPost(), $callback);
    }
    
    protected function clearSession()
    {
        unset($this->getSession()->data);
        return $this;
    }
}
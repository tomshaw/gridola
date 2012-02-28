<?php
/*!
 * Gidola Zend Framework 1.x Grid
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
abstract class App_Grid_Gridola
{
    protected $_request = null;
    
    protected $_session = null;
    
    protected $_view = null;
    
    protected $_element = null;
    
    protected $_rows = null;
    
    protected $_urlHelper = null;
    
    protected $_url = null;
    
    protected $_resourceLoader = null;
    
    protected $_adapterClass = null;
    
    protected $_dataSet = array();
    
    protected $_prefixPaths = array(
        'App_Grid_Element' => 'App/Grid/Element',
    	'App_Grid_Adapter' => 'App/Grid/Adapter',
    	'App_Grid_Export' => 'App/Grid/Export'
    );
    
    public function __construct()
    {
    	$loader = $this->getResourceLoader();
        foreach($this->_prefixPaths as $prefix => $path) {
        	$loader->addPrefixPath($prefix, $path);
        }
    }
    
    protected function getRequest()
    {
        if ($this->_request === null) {
            $this->_request = Zend_Controller_Front::getInstance()->getRequest();
        }
        return $this->_request;
    }
    
    protected function getSession()
    {
        if ($this->_session === null) {
            $this->_session = new Zend_Session_Namespace('store');
        }
        return $this->_session;
    }
    
    protected function getView()
    {
        if ($this->_view === null) {
            $this->_view = new App_Grid_View();
        }
        return $this->_view;
    }
    
    protected function getResourceLoader()
    {
        if (null === $this->_resourceLoader) {
            $loader = new Zend_Loader_PluginLoader();
            $this->_resourceLoader = $loader;
        }
        return $this->_resourceLoader;
    }
    
    protected function getElement()
    {
        if ($this->_element === null) {
            $this->_element = new App_Grid_Element();
        }
        return $this->_element;
    }
    
    protected function getUrlHelper()
    {
        if ($this->_urlHelper === null) {
            $this->_urlHelper = new Zend_Controller_Action_Helper_Url();
        }
        return $this->_urlHelper;
    }
    
    protected function getUrl($controller = null, $module = null, array $params = array())
    {
        if ($this->_url === null) {
            $action = $this->getRequest()->getActionName();
            $this->_url = $this->getUrlHelper()->simple($action, $controller, $module, $params);
        }
        return $this->_url;
    }
    
    protected function setAdapterClass($adapterClass)
    {
        $this->_adapterClass = $adapterClass;
    }
    
    protected function getAdapterClass()
    {
        return $this->_adapterClass;
    }
    
    protected function dynamicSort()
    {
        $this->setSort($this->getRequest()->getParam('sort') == 'desc' ? 'asc' : 'desc');
        return $this;
    }
    
    protected function showFilter()
    {
        return ($this->getAdapterClass() === 'DbSelect' || $this->getAdapterClass() == 'DbTableSelect') ? true : false;
    }
    
    protected function setDataSet($dataSet)
    {
        $this->_dataSet = $dataSet;
    }
    
    protected function getDataSet()
    {
        if(is_null($this->_dataSet)) {
        	$this->_initDataSource();
        }
        return $this->_dataSet;
    }
    
    protected function _initDataSource()
    {
        $dataSource = $this->getDataSource();
        
        $loader = $this->getResourceLoader();
        
        if (is_array($dataSource)) {
            $adapterClassName = 'Array';
        } else if ($dataSource instanceof Zend_Db_Table_Select) {
            $adapterClassName = 'DbTableSelect';
        } else if ($dataSource instanceof Zend_Db_Select) {
            $adapterClassName = 'DbSelect';
        } else if ($dataSource instanceof Zend_Db_Table_Rowset) {
            $adapterClassName = 'Rowset';
        } else if ($dataSource instanceof Iterator) {
            $adapterClassName = 'Iterator';
        } else {
            throw new App_Grid_Exception('The data source provider: ' . get_class($dataSource) . ' is not supported.');
        }
        
        $this->setAdapterClass($adapterClassName);
        
        $adapterObject = $loader->load($adapterClassName);
        
        $dataSourceAdapter = new $adapterObject($dataSource);
        
        $dataSourceAdapter->initialize($this->getDataGrid(), $this->getSort(), $this->getOrder(), $this->getItemsPerPage());
        
        $this->setDataSet($dataSourceAdapter->getData());
    }
    
    protected function processExport()
    {
    	if(null === ($deploymentType = $this->getExportType())) {
    		return $this;
    	}
    	
    	$loader = $this->getResourceLoader();
    	
    	switch($deploymentType) {
    		case 'csv':
    			$deploymentClass = 'Csv';
    			break;
    		case 'xml':
    			$deploymentClass = 'Xml';
    			break;
    		default:
    			throw new App_Grid_Exception('Export support for: ' . $deploymentType . ' is not supported at this time.');
    	}
    	
    	$deploymentObject = $loader->load($deploymentClass);
    	
    	$deploymentAdapter = new $deploymentObject($this->getDataSource(), $this->getDataGrid(), $this->getDataGridName());
    	
    	$deploymentAdapter->export();
    }
    
    protected function _processData()
    {
    	$this->_initDataSource();
    	
    	if($this->getExportType()) {
    		return $this->processExport();
    	}
        
        $searchParams = $this->getRequest()->getPost();
        foreach ($this->getDataGrid() as $_index => $column) {
            if (isset($searchParams[$column['index']])) {
                $column['value'] = $searchParams[$column['index']];
            } elseif (isset($this->getSession()->data[$column['index']])) {
                $column['value'] = $this->getSession()->data[$column['index']];
            } else {
                $column['value'] = '';
            }
            if (isset($this->_columns[$_index])) {
                $this->_columns[$_index]['element'] = $this->getElement()->addElement($column);
                $this->_columns[$_index]['style']   = $this->getElement()->addStyle($column);
            }
        }
        
        $this->initView();
    }
    
    protected function prepareRowClickUrl()
    {
        $rowClickUrl = $this->getRowClickUrl();
        if (sizeof($rowClickUrl)) {
            if (!array_key_exists('field', $rowClickUrl)) {
                throw new App_Grid_Exception('A database field name must be specified when creating a clickable row.');
            }
            if (isset($rowClickUrl['url']) && is_array($rowClickUrl['url'])) {
                $data = array();
                foreach ($rowClickUrl['url'] as $_index => $value) {
                    if (in_array($_index, array(
                        'module',
                        'controller',
                        'action'
                    ))) {
                        $data[$_index] = $value;
                    }
                }
                $this->_rowClickUrl['url'] = $this->getUrlHelper()->url($data) . '/' . $this->_rowClickUrl['field'] . '/';
            }
        }
        return $this;
    }
    
    protected function prepareActionUrls()
    {
        if (sizeof($this->getActions())) {
            foreach ($this->getActions() as $_index => $value) {
                if (!array_key_exists('url', $value)) {
                    throw new App_Grid_Exception('A url must be specified when creating inline row actions.');
                }
                if (sizeof($value['url'])) {
                    if (isset($value['url']['action'])) {
                        $this->_actions[$_index]['url'] = $this->getUrlHelper()->simple($value['url']['action']);
                    }
                }
            }
        }
        return $this;
    }
    
    protected function encodeMassactions()
    {
        if (sizeof($this->getMassActions())) {
            foreach ($this->getMassActions() as $_index => $value) {
                if (sizeof($value['url'])) {
                    $this->_massactions[$_index]['url'] = $this->getUrlHelper()->url($value['url']);
                }
            }
            $this->_massactions = str_replace('\\/', '/', Zend_Json::encode($this->_massactions));
        }
        return $this;
    }
    
    protected function prepareScrollType()
    {
        if (null == $this->getScrollType()) {
            $this->setScrollType($this->getScrollingTypes($jumping = 2));
        } else {
            $scrollTypes = array_flip($this->getScrollingTypes());
            if (!isset($scrollTypes[$this->getScrollType()])) {
                throw New App_Grid_Exception('Available scroll types include, ' . implode(', ', array_flip($scrollTypes)));
            }
        }
        return $this;
    }
    
    protected function preparePaginatorPartial()
    {
        // Contemplating extra functionality here.
        return $this;
    }
    
    protected function prepareExportTypes()
    {
    	$exportTypes = $this->getExportTypes();
    	return $this;
    }
    
    protected function initView()
    {
        $this->getView()
            ->setUrl($this->getUrl())
            ->setRows($this->getDataSet())
            ->setShowFilter($this->showFilter())
            ->setDataGrid($this->getDataGrid())
            ->setSort($this->dynamicSort()->getSort())
            ->setPage($this->getRequest()->getParam('page', 1))
            ->setActions($this->prepareActionUrls()->getActions())
            ->setMassActions($this->getMassActions())
            ->setMassActionField($this->getMassactionField())
            ->setExportTypes($this->prepareExportTypes()->getExportTypes())
            ->setFormId($this->getFormId())
            ->setTableClass($this->getTableClass())
            ->setJsonActions($this->encodeMassactions()->getMassActions())
            ->setJavascriptFormVariable($this->getFormId())
            ->setJavascriptInclude()
            ->setRowClickUrl($this->prepareRowClickUrl()->getRowClickUrl())
            ->setCycleColors($this->getCycleColors())
            ->setOnMouseOverColor($this->getOnMouseOverColor())
            ->setScrollType($this->prepareScrollType()->getScrollType())
            ->setPaginatorPartial($this->preparePaginatorPartial()->getPaginatorPartial());
    }
    
    public function __toString()
    {
        try {
            $return = $this->getView()->render($this->getTemplate());
            return $return;
        }
        catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
        }
        return '';
    }   
}
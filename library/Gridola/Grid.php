<?php
/*!
 * Gidola Zend Framework 1.x Grid
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
abstract class Gridola_Grid
{
    protected $_request = null;
    
    protected $_session = null;
    
    protected $_token = null;
    
    protected $_view = null;
    
    protected $_element = null;
    
    protected $_rows = null;
    
    protected $_urlHelper = null;
    
    protected $_url = null;
    
    protected $_resourceLoader = null;
    
    protected $_adapterClass = null;
    
    protected $_dataSet = array();
    
    protected $_prefixPaths = array(
        'Gridola_Element' => 'Gridola/Element', 
        'Gridola_Adapter' => 'Gridola/Adapter', 
        'Gridola_Export' => 'Gridola/Export');
    
    public function __construct()
    {
        $loader = $this->getResourceLoader();
        foreach ($this->_prefixPaths as $prefix => $path) {
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
    
    protected function getToken()
    {
        if ($this->_token === null) {
            $this->_token = new Gridola_Token();
        }
        return $this->_token;
    }
    
    protected function getView()
    {
        if ($this->_view === null) {
            $this->_view = new Gridola_View();
        }
        return $this->_view;
    }
    
    protected function getResourceLoader()
    {
        if (null === $this->_resourceLoader) {
            $this->_resourceLoader = new Zend_Loader_PluginLoader();
        }
        return $this->_resourceLoader;
    }
    
    protected function getElement()
    {
        if ($this->_element === null) {
            $this->_element = new Gridola_Element();
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
            $action     = $this->getRequest()->getActionName();
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
        if (is_null($this->_dataSet)) {
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
        } else if ($dataSource instanceof Zend_Db_Select) {
            $adapterClassName = 'DbSelect';
        } else if ($dataSource instanceof Zend_Db_Table_Select) {
            $adapterClassName = 'DbTableSelect';
        } else if ($dataSource instanceof Zend_Db_Table_Rowset) {
            $adapterClassName = 'Rowset';
        } else if ($dataSource instanceof Iterator) {
            $adapterClassName = 'Iterator';
        } else {
            throw new Gridola_Exception('The data source provider: ' . get_class($dataSource) . ' is not supported.');
        }
        
        $this->setAdapterClass($adapterClassName);
        
        $adapterObject = $loader->load($adapterClassName);
        
        $dataSourceAdapter = new $adapterObject($dataSource);
        
        $dataSourceAdapter->initialize($this->getDataGrid(), $this->getSort(), $this->getOrder(), $this->getItemsPerPage());
        
        $this->setDataSet($dataSourceAdapter->getData());
    }
    
    protected function _mapDataGridOptions()
    {
        $options = array();
        foreach ($this->getDataGrid() as $_index => $column) {
            if (!array_key_exists('options', $column)) {
                continue;
            }
            if (is_array($column['options'])) {
                foreach ($column['options'] as $_key => $value) {
                    if (is_numeric($_key) && $_key != '-1') {
                        $options[$_index][$_key] = $value;
                    }
                }
            }
        }
        
        if (sizeof($options)) {
            foreach ($this->getDataSet() as $_index => $value) {
                foreach ($value as $key => $var) {
                    if (isset($options[$key])) {
                        $value->{$key} = $options[$key][$var];
                    }
                }
            }
        }
        
        return $this;
    }
    
    protected function processExport()
    {
        if (null === ($exportType = $this->getExportType())) {
            return $this;
        }
        
        $export = $this->getExport();
        
        if (isset($export[$exportType])) {
        	
            $settings = (array) $export[$exportType];
            
            try {
                $handler = $this->getResourceLoader()->load($exportType);
            }
            catch (Zend_Loader_Exception $e) {
                throw new Gridola_Exception('Export support for: ' . $exportType . ' is not supported at this time.');
            }
            
            $adapter = new $handler($this->getDataSource(), $this->getDataGrid(), $this->getDataGridName(), $settings);
            
            $adapter->export();
        }
    }
    
    protected function _processData()
    {
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $token = $this->getToken();
            if (false === ($token->validate())) {
                throw new Gridola_Exception('There was a problem submitting your search.');
            }
        }
        
        $this->_initDataSource();
        
        $this->_mapDataGridOptions();
        
        if ($this->getExportType()) {
            return $this->processExport();
        }
        
        $post = $request->getPost();
        foreach ($this->getDataGrid() as $_index => $column) {
        	
            if (isset($post[$column['index']])) {
                $column['value'] = $post[$column['index']];
            } elseif (isset($this->getSession()->data[$column['index']])) {
                $column['value'] = $this->getSession()->data[$column['index']];
            } else {
                $column['value'] = '';
            }
            
            if (isset($this->_columns[$_index])) {
            	
                $elementType = array_key_exists('type', $column) ? $column['type'] : 'text';
                
                $elementLoader = $this->getResourceLoader();
                
                switch ($elementType) {
                    case 'text':
                        $elementClass = $elementLoader->load('Text');
                        break;
                    case 'number':
                        $elementClass = $elementLoader->load('Number');
                        break;
                    case 'options':
                        $elementClass = $elementLoader->load('Options');
                        break;
                    case 'datetime':
                        $elementClass = $elementLoader->load('DatePicker');
                        break;
                    default:
                        throw new Gridola_Exception('Element type: ' . $elementType . ' is currently not supported.');
                }
                
                $elementObject = new $elementClass($column);
                
                $this->_columns[$_index]['element'] = $elementObject->_toHtml();
                
                $this->_columns[$_index]['style'] = $elementObject->_toStyle();
                
            }
        }
        
        $this->initView();
    }
    
    protected function prepareRowClickUrl()
    {
        $data = $this->getRowClickUrl();
        if (sizeof($data)) {
            if (!array_key_exists('field', $data)) {
                throw new Gridola_Exception('A database column-field name must be specified when creating clickable rows.');
            }
            if (isset($data['url']) && is_array($data['url'])) {
                $route = array();
                foreach ($data['url'] as $_index => $value) {
                    if (in_array($_index, array(
                        'module',
                        'controller',
                        'action'
                    ))) {
                        $route[$_index] = $value;
                    }
                }
                $this->_rowClickUrl['url'] = $this->getUrlHelper()->url($route) . '/' . $this->_rowClickUrl['field'] . '/';
            }
        }
        return $this;
    }
    
    protected function prepareActionUrls()
    {
        if (sizeof($this->getActions())) {
            foreach ($this->getActions() as $_index => $value) {
                if (!array_key_exists('url', $value)) {
                    throw new Gridola_Exception('A url must be specified when creating inline row actions.');
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
                throw New Gridola_Exception('Available scroll types include, ' . implode(', ', array_flip($scrollTypes)));
            }
        }
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
            ->setExport($this->getExport())
            ->setToken($this->getToken()->setSalt($this->getDataGridName())
            ->setTimeout(120))
            ->setFormId($this->getFormId())
            ->setTableClass($this->getTableClass())
            ->setJsonActions($this->encodeMassactions()->getMassActions())
            ->setJavascriptFormVariable($this->getFormId())
            ->setJavascriptInclude()
            ->setRowClickUrl($this->prepareRowClickUrl()->getRowClickUrl())
            ->setCycleColors($this->getCycleColors())
            ->setOnMouseOverColor($this->getOnMouseOverColor())
            ->setScrollType($this->prepareScrollType()->getScrollType())
            ->setPaginatorPartial($this->getPaginatorPartial());
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
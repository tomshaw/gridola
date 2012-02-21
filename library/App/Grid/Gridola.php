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
    
    protected $_db = null;
    
    protected $_element = null;
    
    protected $_searchParams = array();
    
    protected $_rows = null;
    
    protected $_urlHelper = null;
    
    protected $_url = null;
    
    protected $_route = null;
    
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
    
    protected function getDb()
    {
        if ($this->_db === null) {
            $this->_db = new App_Grid_Db();
        }
        return $this->_db;
    }
    
    protected function getView()
    {
        if ($this->_view === null) {
            $this->_view = new App_Grid_View();
        }
        return $this->_view;
    }
    
    protected function getElement()
    {
        if ($this->_element === null) {
            $this->_element = new App_Grid_Element();
        }
        return $this->_element;
    }
    
    protected function getSearchParams()
    {
        if (!sizeof($this->_searchParams)) {
            if ($this->getRequest()->isPost()) {
                $this->_searchParams = $this->getRequest()->getPost();
            }
        }
        return $this->_searchParams;
    }
    
    protected function dynamicSort()
    {
        $this->setSort($this->getRequest()->getParam('sort') == 'desc' ? 'asc' : 'desc');
        return $this;
    }
    
    protected function getUrlHelper()
    {
        if ($this->_urlHelper === null) {
            $this->_urlHelper = new Zend_Controller_Action_Helper_Url();
        }
        return $this->_urlHelper;
    }
    
    protected function getUrl($params = null, $controller = null, $module = null)
    {
        if ($this->_route === null) {
            $action = $this->getRequest()->getActionName();
            $this->_route = $this->getUrlHelper()->simple($action, $controller, $module, $params);
        }
        return $this->_route;
    }
    
    protected function getSimpleUrl()
    {
    	if ($this->_url === null) {
    		$module = $this->getRequest()->getModuleName();
    		$controller = $this->getRequest()->getControllerName();
    		$action = $this->getRequest()->getActionName();
    		$page = $this->getRequest()->getParam('page', 1);
    		if($module == 'default') {
    			$this->_url = '/'.$controller.'/'.$action.'/page/'.$page;
    		} else {
    			$this->_url = '/'.$module.'/'.$controller.'/'.$action.'/page/'.$page;
    		}
    	}
    	return $this->_url;
    }
    
    protected function _prepareData()
    {
        $this->initSelect();
        
        $searchParams = $this->getSearchParams();
        foreach ($this->getGrid() as $_index => $column) {
            if (isset($searchParams[$column['index']])) {
                $column['value'] = $searchParams[$column['index']];
            } elseif (isset($this->getSession()->data[$column['index']])) {
                $column['value'] = $this->getSession()->data[$column['index']];
            } else {
                $column['value'] = '';
            }
            
            if (isset($this->_grid[$_index])) {
                $this->_grid[$_index]['element'] = $this->getElement()->addElement($column);
                $this->_grid[$_index]['style']   = $this->getElement()->addStyle($column);
            }
        }
        
        $this->initView();
    }
    
    protected function getRows()
    {
        if ($this->_rows === null) {
            $this->_rows = $this->initSelect();
        }
        return $this->_rows;
    }
    
    protected function initSelect()
    {
        $this->getDb()->setSelect($this->getSelect())->checkData($this->getGrid())->init();
        $this->getDb()->setSortOrder($this->getSort(), $this->getOrder());
        $this->_rows = $this->getDb()->paginateResults();
    }
    
    protected function prepareRowClickUrl()
    {
        $rowClickUrl = $this->getRowClickUrl();
        if (sizeof($rowClickUrl)) {
            if (!array_key_exists('field', $rowClickUrl)) {
                throw new App_Grid_Exception('A database field name must be specified when creating a clickable row.');
            }
            if (isset($rowClickUrl['url']) && is_array($rowClickUrl['url'])) {
                foreach ($rowClickUrl['url'] as $_index => $value) {
                    if (in_array($_index, array('module','controller','action'))) {
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
        if (sizeof($this->_actions)) {
            foreach ($this->_actions as $_index => $value) {
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
    
    protected function initView()
    {
        $this->getView()
            ->setRows($this->getRows())
            ->setDataGrid($this->getGrid())
            ->setSort($this->dynamicSort()->getSort())
            ->setActions($this->prepareActionUrls()->getActions())
            ->setMassActions($this->getMassActions())
            ->setMassActionField($this->getMassactionField())
            ->setFormId($this->getFormId())
            ->setTableClass($this->getTableClass())
            ->setRoute($this->getUrl())
            ->setSortRoute($this->getSimpleUrl())
            ->setJsonActions($this->encodeMassactions()->getMassActions())
            ->setJavascriptFormVariable($this->getFormId())
            ->setJavascriptInclude()
            ->setRowClickUrl($this->prepareRowClickUrl()->getRowClickUrl())
            ->setCycleColors($this->getCycleColors())
            ->setOnMouseOverColor($this->getOnMouseOverColor());
    }
    
    public function __toString()
    {
        try {
            return $this->getView()->render($this->getTemplate());
        }
        catch (Exception $e) {
            throw new App_Grid_Exception($e);
        }
    }
    
}
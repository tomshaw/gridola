<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	protected function _initAppAutoload ()
	{
		$loader = new Zend_Application_Module_Autoloader(array(
				'namespace' => '',
				'basePath' => APPLICATION_PATH
		));
		$loader->addResourceType('grid','grids','Grid');
		return $loader;
	}
	
	/**
	 * Relies on appnamespace = "Application" be defined in
	 * system application.ini.
	 *
	 * @note Grids and models are namespaced Application_*.
	 */
	// 	protected function _initGridLoaderResource()
	// 	{
	// 		$loader = $this->getResourceLoader();
	// 		$loader->addResourceType('grid','grids','Grid');
	// 		return $loader;
	// 	}

	protected function _initViewSettings()
	{
		$this->bootstrap('view');
	
		$view = $this->getResource('view');
	
		//$view = new Zend_View();
	
		$view->docType('HTML5');
	
		$view->headTitle('Gridola - Zend Framework Grid');
	
		$view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
		 
		//$view->addHelperPath(APPLICATION_PATH . "/modules/default/views/helpers", "App_View_Helper_");
	
		$view->jQuery()->enable()
			->setVersion('1.7.1')
			->setUiVersion('1.8.17')
			->addStylesheet('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/ui-lightness/jquery-ui.css')
			->uiEnable();
	
		$view->headLink()->appendStylesheet('/css/stylesheet.css');
		//$view->headLink()->appendStylesheet('/css/bootstrap-responsive.css');
	
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
			'ViewRenderer'
		);
		 
		//$view->headScript()->prependFile('/js/main.js');
	
		$viewRenderer->setView($view);
	
		return $view;
	}
	
}


# Gridola - Zend Framework 1.11.11 Grid
      
  Gridola is a simple to use generic grid developed to be used with Zend Framework 1.11.11. Goals for the project were to develop a grid that supported in line actions, mass actions and clickable rows and to support generic simple data types including integers and date time fields that could be used to narrow down query results from a start and finish or greater than less than perspective. 
 
## Features

  * Built in dynamic and default column ordering and sorting.
  * Dynamic user defined clickable row routes.
  * Robust data type support including varchar, integer, and date times search fields.
  * Inline actions with dynamic routes and custom user defined value fields.
  * Mass action support with dynamic routes and custom user defined value fields.
  * User defined alternating on mouse over colors and static alternating row colors.
  * User defined grid template paths. Specifiy the template you would like to use.
  * Pure HTML grid templates, allows designers to edit markup directly.
  
### Example grid:
     
		protected $_tableClass = 'table table-bordered';
    
		protected $_itemsPerPage = 20;
    
		public function __construct()
    	{
        	$this->setFormId('city_grid');
        	$this->setOrder('ID');
        	$this->setSort('ASC');
        	$this->setScrollType('Jumping'); // All, Elastic, Jumping, Sliding
        	$this->setItemsPerPage(50);
        	$this->setTemplate('index/citiesgrid'); // Defaults to grid.phtml
        	$this->setPaginatorPartial('index/gridpagination'); // Defaults to gridpagination.phtml
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
	    
	    protected function _prepareExport()
	    {
	        $this->addExport('csv' , array(
	            'label' => 'Comma Separated', // Drop down label.
	            'header' => false, // Display header in output.
	            'write' => false // Write file to disk rootdir/export/filename.xml 
	        ));
	    	
	    	/* SpreadsheetML */
	        $this->addExport('xml' , array(
	            'label' => 'XML Data',
	            'header' => true,
	            'write' => false
	        ));
	    }

## Installation

  Please pay close attention to the bootstrap file and how the grids namespace is initialized. 
  
  * Download the [MySQL World Database](http://dev.mysql.com/doc/world-setup/en/world-setup.html) and load it.
  * Install [Zend Extras](http://framework.zend.com/) into the library folder. Google it if you have any difficulties.
  * Rename application/configs/application.sample.ini to application.ini and fill in your MySQL connection information.
  
  Other documentation concerning installation and how to initialise a grid in your application will be coming soon.

## Todo

 The Gridola project was hastily developed over a single star bucks red bull guzzling weekend. Any input, help, pull requests or ideas from the general public would be greatly appreciated. 

 List of todo items:

  * Consider SQL pattern matching.
  * Currency support.
  * Consider caching paginated results.
  * API documentation.
  * Review any security concerns.
  * Unit test.

## More Information

  Gridoa was developed with these technologies:

  * [MySQL World Database](http://dev.mysql.com/doc/world-setup/en/world-setup.html) Copyright (c) 2005, 2011, Oracle and/or its affiliates. All rights reserved.
  * [Twitter Bootstrap](http://twitter.github.com/bootstrap/) Simple and flexible HTML, CSS, and Javascript for popular user interface components and interactions.
  * [Zend Framework-1.11.11](http://framework.zend.com/) I don't always use frameworks but when I do I choose Zend Framework.
  * [PHP 5.3.10 - 5.4](http://www.php.net/) Works great with the latest versions of PHP.

## License 

(The MIT License)

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
'Software'), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

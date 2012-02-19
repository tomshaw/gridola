# Gridola - Zend Framework 1.x Grid
      
  Gridola is a simple to use generic grid developed to be used with Zend Framework 1.x series. It was somewhat hastily developed out of curiosity in that I wondered how much code would actually be required to develop a fully functional grid. Having developed Magento applications for several years and being accustomed to using their grids, I decided to use the same array initialization strategy that Magento uses. My goals for the project were to develop a grid that supported in line actions, mass actions and clickable rows. To also support generic simple data types including integers and date time fields that could be used to narrow down query results from a start and finish or greater than less than perspective. 
  
  The included sample application was built with [Twitter Bootstrap](http://twitter.github.com/bootstrap/), a simple and flexible HTML, CSS, and Javascript rapid application development framework and is copyright by Twitter. Gridola has been tested using Zend Framework 1.11.11 and PHP 5.3.10. I'm sure it will also work with the PHP 5.4 release candidates. 
  
  Example grid:
     
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
        
        $this->addColumn('created_at', array(
            'header'=> 'Created',
            'width' => '200px',
            'type'  => 'datetime',
            'index' => 'created_at',
        ));
    }

## Installation

  Please pay close attention to the bootstrap file and how the grids namespace is initialized. 
  
  * Download the [MySQL World Database](http://dev.mysql.com/doc/world-setup/en/world-setup.html) and load it.
  * Install [Zend Extras](http://framework.zend.com/) into the library folder. Google it if you have any difficulties.
  
  Other documentation concerning installation and how to initialise a grid in your application will be coming soon.

## Todo

 The Gridola project was hastily developed over a single star bucks red bull guzzling weekend. In other words it was slammed together without much thought other than getting it to work. That being said the code base could use some very close scrutiny. Any input, help, pull requests or ideas from the general public would be greatly appreciated. 

 List of todo items:

  * Cleanup alot of the code.
  * Add check all and checked remember cookie support.
  * Add export functionality.
  * Currency support.
  * Refactor view object.

## Features

  * Built in dynamic and default column ordering and sorting.
  * Dynamic user defined clickable row routes.
  * Robust data type support including varchar, integer, and date times search fields.
  * Inline actions with dynamic routes and custom user defined value fields.
  * Mass action support with dynamic routes and custom user defined value fields.
  * User defined alternating on mouse over colors and static alternating row colors.
  * Pure HTML grid templates that allows designers to edit markup directly.

## More Information

  Gridoa was developed with these technologies:

  * [MySQL World Database](http://dev.mysql.com/doc/world-setup/en/world-setup.html) Copyright � 2005, 2011, Oracle and/or its affiliates. All rights reserved.
  * [Twitter Bootstrap](http://twitter.github.com/bootstrap/) Simple and flexible HTML, CSS, and Javascript for popular user interface components and interactions.
  * [Zend Framework-1.11.11](http://framework.zend.com/) My framework of choice!
  * [PHP 5.3.10](http://www.php.net/) Will test with 5.4 Release Candidates.
  
# Requirements

  * Nothing special.

# API 

Documentation on how to use the API will be coming soon but for now the API is somewhat self explanatory via the example cities grid in the grids namespace.

# Questions or Comments?

Email: tom@tomshaw.info

## License 

(The MIT License)

Copyright (c) 2011 Tom Shaw &lt;tom@tomshaw.info&gt;

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
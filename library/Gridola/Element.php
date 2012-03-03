<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class Gridola_Element
{
    protected $_elementTypes = array('text', 'number', 'options', 'datetime');
    
    public function __construct($data)
    {
        foreach ($data as $_index => $value) {
            $this->$_index = $value;
        }
        $this->start = (isset($this->value['start'])) ? $this->value['start'] : null;
        $this->end = (isset($this->value['end'])) ? $this->value['end'] : null;
    }
    
    public function __set($key, $value)
    {
        $this->{$key} = $value;
    }
    
    public function hasOption($key)
    {
        $option = strtolower($key);
        return isset($this->{$option});
    }
    
    public function getOption($key)
    {
        $option = strtolower($key);
        if ($this->hasOption($option)) {
            return $this->{$option};
        }
        return null;
    }
    
    public function __get($prop)
    {
        return $this->getOption($prop);
    }
    
    public function __isset($key)
    {
        return isset($this->{$key});
    }
    
    public function __unset($key)
    {
        unset($this->{$key});
    }
    
    public function _toHtml()
    {
        return $this->addElement();
    }
    
    public function _toStyle()
    {
        $style = 'style="';
        if (isset($this->align) && isset($this->width)) {
            $style .= 'text-align:' . $this->align . ';width:' . $this->width . ';';
        } else {
            if (isset($this->align)) {
                $style .= 'text-align:' . $this->align . ';';
            }
            if (isset($this->width)) {
                $style .= 'width:' . $this->width . ';';
            } else {
                $style .= 'width:20%;';
            }
        }
        return $style .= '"';
    }   
}
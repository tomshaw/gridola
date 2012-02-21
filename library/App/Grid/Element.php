<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class App_Grid_Element
{
    protected $_type = null;
    
    protected $_style = null;
    
    protected $_index = null;
    
    protected $_value = null;
    
    protected $_options = null;
    
    protected $_start = null;
    
    protected $_end = null;
    
    /**
     * @todo Add Zend View Helpers for all inputs.
     * 
     * @param unknown_type $options
     */
    public function addElement($options)
    {
        $this->_type    = (isset($options['type'])) ? $options['type'] : null;
        $this->_style   = (isset($options['width'])) ? $options['width'] : null;
        $this->_index   = (isset($options['index'])) ? $options['index'] : null;
        $this->_value   = (isset($options['value'])) ? $options['value'] : null;
        $this->_options = array_key_exists('options', $options) ? $options['options'] : false;
        $this->_start   = (isset($this->_value['start'])) ? $this->_value['start'] : null;
        $this->_end     = (isset($this->_value['end'])) ? $this->_value['end'] : null;
        
        switch ($this->_type) {
            case 'text':
                return '<input type="' . $this->_type . '" name="' . $this->_index . '" id="' . $this->_index . '" value="' . $this->_value . '">';
                break;
            case 'checkbox':
                return '<input type="' . $this->_type . '" name="' . $this->_index . '" id="' . $this->_index . '" value="' . $this->_value . '">';
                break;
            case 'number':
                $number = '<input type="text" name="start[' . $this->_index . ']" id="' . $this->_index . '" value="' . $this->_start . '" title="Starting value.">';
                $number .= ' <input type="text" name="end[' . $this->_index . ']" id="' . $this->_index . '" value="' . $this->_end . '" title="Ending value.">';
                return $number;
                break;
            case 'options':
                $select = '<select name="' . $this->_index . '">';
                $select .= '<option value="-1"></option>';
                foreach ($this->_options as $key => $value) {
                    $selected = ($key == $this->_value) ? 'selected="selected"' : '';
                    $select .= '<option value="' . $value . '" ' . $selected . '>' . $value . '</option>';
                }
                $select .= '</select>';
                return $select;
                break;
            case 'datetime':
                $datePickerStart = new ZendX_JQuery_Form_Element_DatePicker("'.$this->_index.'", array('value' => $this->_start), array());
                $datePickerStart->removeDecorator('label')->removeDecorator('HtmlTag')->setBelongsTo('start');
                $datePickerEnd = new ZendX_JQuery_Form_Element_DatePicker("'.$this->_index.'", array('value' => $this->_end), array());
                $datePickerEnd->removeDecorator('label')->removeDecorator('HtmlTag')->setBelongsTo('end');
                return $datePickerStart . $datePickerEnd;
                break;
        }
    }
    
    public function addStyle($data)
    {
        $style = 'style="';
        if (isset($data['align']) && isset($data['width'])) {
            $style .= 'text-align:' . $data['align'] . ';width:' . $data['width'] . ';';
        } else {
            if (isset($data['align'])) {
                $style .= 'text-align:' . $data['align'] . ';';
            } else {
                $style .= 'text-align:left;';
            }
            if (isset($data['width'])) {
                $style .= 'width:' . $data['width'] . ';';
            } else {
                $style .= 'width:20%;';
            }
        }
        return $style .= '"';
    }
    
}
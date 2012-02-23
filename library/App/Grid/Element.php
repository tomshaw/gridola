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
    
    protected $_elementTypes = array('text','number','options','datetime');
    
    /**
     * @param array $options
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
                $input = new Zend_Form_Element_Text($this->_index);
                $input->setValue($this->_value);
                $input->setAttribs(array('style'=>'width:95%;'));
                return $input;
                break;
            case 'number':
                $numberStart = new Zend_Form_Element_Text($this->_index);
                $numberStart->setValue($this->_start);
                $numberStart->setAttribs(array('title'=>'Starting value.'));
                $numberStart->removeDecorator('label')->removeDecorator('HtmlTag');
                $numberStart->setBelongsTo('start');
                $numberEnd = new Zend_Form_Element_Text($this->_index);
                $numberEnd->setValue($this->_end);
                $numberEnd->setAttribs(array('title'=>'Ending value.','style'=>'margin-top:0px;'));
                $numberEnd->removeDecorator('label')->removeDecorator('HtmlTag');
                $numberEnd->setBelongsTo('end');
                return $numberStart . $numberEnd;
                break;
            case 'options':
                $select = new Zend_Form_Element_Select($this->_index);
                $select->setValue(array($this->_value));
                $select->addMultiOptions($this->_options);
                return $select;
                break;
            case 'datetime':
                $datePickerStart = new ZendX_JQuery_Form_Element_DatePicker("'.$this->_index.'", array('value' => $this->_start), array());
                $datePickerStart->removeDecorator('label')->removeDecorator('HtmlTag')->setBelongsTo('start');
                $datePickerEnd = new ZendX_JQuery_Form_Element_DatePicker("'.$this->_index.'", array('value' => $this->_end), array());
                $datePickerEnd->removeDecorator('label')->removeDecorator('HtmlTag')->setBelongsTo('end');
                return $datePickerStart . $datePickerEnd;
                break;
            default:
                throw New App_Grid_Exception('Only element types of : ' . implode(', ', $this->_elementTypes) . ' are supported.');
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
<?php
/*!
* Gidola Zend Framework 1.x Grid
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
	
	public function addElement($options)
	{
		$this->_type 	= (isset($options['type'])) ? $options['type'] : null;
		$this->_style 	= (isset($options['width'])) ? $options['width'] : null;
		$this->_index 	= (isset($options['index'])) ? $options['index'] : null;
		$this->_value 	= (isset($options['value'])) ? $options['value'] : null;
		$this->_options = array_key_exists('options', $options) ? $options['options'] : false;
		$this->_start = (isset($this->_value['start'])) ? $this->_value['start'] : null;
		$this->_end = (isset($this->_value['end'])) ? $this->_value['end'] : null;

		switch($this->_type) {
			case 'text':
				return '<input type="'.$this->_type.'" name="'.$this->_index.'" id="'.$this->_index.'" value="'.$this->_value.'">';
				break;
			case 'checkbox':
				return '<input type="'.$this->_type.'" name="'.$this->_index.'" id="'.$this->_index.'" value="'.$this->_value.'">';
				break;
			case 'number':
				$number = 'Start <input type="text" name="start['.$this->_index.']" id="'.$this->_index.'" value="'.$this->_start.'" style="width:50%;"><br />';
				$number.= 'End&nbsp;&nbsp;<input type="text" name="end['.$this->_index.']" id="'.$this->_index.'" value="'.$this->_end.'" style="width:50%;">';
				return $number;
				break;
			case 'options':
				$select = '<select name="'.$this->_index.'" style="width:100%;">';
				$select.= '<option value="-1"></option>';
				foreach($this->_options as $key => $value) {
					$selected = ($key == $this->_value) ? 'selected="selected"' : ''; 
					$select.= '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
				}
				$select.= '</select>';
				return $select;
				break;
			case 'datetime':
				$datePickerStart = new ZendX_JQuery_Form_Element_DatePicker("'.$this->_index.'", array('value'=>$this->_start), array());
				$datePickerStart->removeDecorator('label')->removeDecorator('HtmlTag');
				$datePickerStart->setBelongsTo('start');
				$datePickerEnd = new ZendX_JQuery_Form_Element_DatePicker("'.$this->_index.'", array('value'=>$this->_end), array());
				$datePickerEnd->removeDecorator('label')->removeDecorator('HtmlTag');
				$datePickerEnd->setBelongsTo('end');
				return $datePickerStart . $datePickerEnd;
				break;
		}	
	}
	
	public function addStyle($data)
	{
		$style = '';
		if(isset($data['width']) || isset($data['align'])) {
			if(isset($data['width']) && isset($data['align'])) {
				$style = 'style="width:'. $data['width'] . ';text-align:'. $data['align'] . ';"';
			} elseif(isset($data['width'])) {
				$style = 'style="width:'. $data['width'] . ';"';
			} elseif(isset($data['align'])) {
				$style= 'style="text-align:'. $data['align'] . ';"';
			}
		}
		return $style;
	}
	
}
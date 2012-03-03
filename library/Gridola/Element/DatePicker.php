<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
*/
class Gridola_Element_DatePicker extends Gridola_Element
{
    public function addElement()
    {	
        $datePickerStart = new ZendX_JQuery_Form_Element_DatePicker("'.$this->index.'", array('value' => $this->start), array());
        $datePickerStart->removeDecorator('label')->removeDecorator('HtmlTag')->setBelongsTo('start');
        $datePickerEnd = new ZendX_JQuery_Form_Element_DatePicker("'.$this->index.'", array('value' => $this->end), array());
        $datePickerEnd->removeDecorator('label')->removeDecorator('HtmlTag')->setBelongsTo('end');
        return $datePickerStart . $datePickerEnd;
    }
}
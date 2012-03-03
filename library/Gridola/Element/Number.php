<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
*/
class Gridola_Element_Number extends Gridola_Element
{
    public function addElement()
    {	
        $numberStart = new Zend_Form_Element_Text($this->index);
        $numberStart->setValue($this->start);
        $numberStart->setAttribs(array('title'=>'Starting value.'));
        $numberStart->removeDecorator('label')->removeDecorator('HtmlTag');
        $numberStart->setBelongsTo('start');
        $numberEnd = new Zend_Form_Element_Text($this->index);
        $numberEnd->setValue($this->end);
        $numberEnd->setAttribs(array('title'=>'Ending value.','style'=>'margin-top:0px;'));
        $numberEnd->removeDecorator('label')->removeDecorator('HtmlTag');
        $numberEnd->setBelongsTo('end');
        return $numberStart . $numberEnd;
    }
}
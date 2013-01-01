<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
*/
class Gridola_Element_Text extends Gridola_Element
{
    public function addElement()
    {
        $input = new Zend_Form_Element_Text($this->index);
        $input->setValue($this->value);
        //$input->setAttribs(array());
        $input->removeDecorator('label')->removeDecorator('HtmlTag');
        return $input;
    }
}
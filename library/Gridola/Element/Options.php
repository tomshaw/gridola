<?php
/*!
 * Gridola - Super Simple Grid for Zend Framework 1.x
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
*/
class Gridola_Element_Options extends Gridola_Element
{
    public function addElement()
    {
        $select = new Zend_Form_Element_Select($this->index);
        $select->setValue(array($this->value));
        $select->addMultiOptions($this->options);
        $select->removeDecorator('label')->removeDecorator('HtmlTag');
        return $select;
    }
}
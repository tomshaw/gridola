<?php
/*!
 * Gidola Zend Framework 1.x Grid
 * Copyright(c) 2011 Tom Shaw <tom@tomshaw.info>
 * MIT Licensed
 */
class Gridola_Token extends Gridola_Grid
{
    protected $_hash;
    
    protected $_salt = 'salt';
    
    protected $_session;
    
    protected $_timeout = 300;
    
    protected $_token;
    
    public function __construct()
    {
        if (null === $this->_token) {
            $this->_token = Zend_Controller_Front::getInstance()->getRequest()->getParam('token');
        }
    }
    
    public function setSalt($salt)
    {
        $this->_salt = $salt;
        return $this;
    }
    
    public function getSalt()
    {
        return $this->_salt;
    }
    
    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
        return $this;
    }
    
    public function getTimeout()
    {
        return $this->_timeout;
    }
    
    protected function getSession()
    {
        if ($this->_session === null) {
            $this->_session = new Zend_Session_Namespace('token');
        }
        return $this->_session;
    }
    
    public function getToken()
    {
    	return $this->_token;
    }
    
    public function getHash()
    {
        if (null === $this->_hash) {
            $this->_hash = md5(mt_rand(1, 1000000) . $this->getSalt() . mt_rand(1, 1000000) . $this->getTimeout());
        }
        return $this->_hash;
    }
    
    protected function sessionToken()
    {
        $session = $this->getSession();
        $session->setExpirationHops(1, null, true);
        $session->setExpirationSeconds($this->getTimeout());
        $session->hash = $this->getHash();
        return $this;
    }
    
    public function validate()
    {
        $session = $this->getSession();
        if (isset($session->hash)) {
            if ($session->hash == $this->getToken()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public function __toString()
    {
        return '<input type="hidden" name="token" value="' . $this->sessionToken()->getHash() . '">';
    }
    
}
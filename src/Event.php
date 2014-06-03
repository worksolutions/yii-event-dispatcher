<?php

namespace WS\EventDispatcher;

abstract class Event extends \CModel {
    private
        $_owner,
        $_params;

    private $_handleErrors;

    static public function className() {
        return get_called_class();
    }

    public function __construct($params = null, $owner = null) {
        $this->setParams($params);
        $this->setOwner($owner);
    }

    /**
     * @return null
     */
    public function getOwner() {
        return $this->_owner;
    }

    /**
     * @param null $owner
     * @return $this
     */
    protected function setOwner($owner) {
        $this->_owner = $owner;
        return $this;
    }

    /**
     * @return null|array
     */
    public function getParams() {
        return $this->_params;
    }

    public function getParam($name) {
        if($this->hasProperty($name)) {
            return $this->$name;
        }
        return $this->_params[$name];
    }

    /**
     * @param array $params
     */
    public function setParams($params) {
        foreach($params as $name => $value) {
            if($this->hasProperty($name)) {
                $this->$name = $value;
            }else {
                $this->_params[$name] = $value;
            }
        }
        $this->_params = $params;
    }

    /**
     * @param $message
     * @return $this
     */
    public function addHandleError($message) {
        $this->_handleErrors[] = $message;
        return $this;
    }

    public function getHandleErrors() {
        return $this->_handleErrors;
    }
}
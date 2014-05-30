<?php

namespace WS\EventDispatcher;


abstract class Handler {

    /**
     * @var Event
     */
    private $_event;

    private  $_params = array();

    static public function className() {
        return get_called_class();
    }

    final public function __construct(Event $event, $params = null) {
        $this->_event = $event;
        $this->_params = $params;
    }

    public function run() {
        if (!$this->identity()) {
            return false;
        }
        return $this->process();
    }

    protected function identity() {
        return true;
    }

    abstract protected function process();

    protected function getEvent() {
        return $this->_event;
    }
}
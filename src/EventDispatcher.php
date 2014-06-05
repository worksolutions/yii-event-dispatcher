<?php

namespace WS\EventDispatcher;


class EventDispatcher extends \CApplicationComponent {

    /**
     * EventDispatcher
     * events => array(
     *      SomeEvent::className() => array(
     *          array(
     *              'class' => SomeHandler::className(),
     *              'params' => array()
     *          ),
     *          //...
     *     ),
     * )
     * @var array events
     */
    public $events = array();

    static public function className() {
        return get_called_class();
    }

    public function createEvent($className, $params = null, $owner = null) {
        if (!is_subclass_of($className, Event::className())) {
            throw new \CException(sprintf('Class event `%s` must instanceof `%s`', $className, Event::className()));
        }
        /** @var $className Event */
        return new $className($params, $owner);
    }

    public function fire(Event $event) {
        $eventClass = get_class($event);
        $handlersData = $this->_getHandlersData($eventClass);
        if(!$event->validate()) {
            throw new \CException("Event `$eventClass` is not valid");
        }

        foreach ($handlersData as $handlerData) {
            $handlerClass = $handlerData['class'];
            if (!is_subclass_of($handlerClass, Handler::className())) {
                throw new \CException("Handler `$handlerClass` not subclass of ".Handler::className());
            }
            try {
                /** @var $handler Handler */
                $handler = new $handlerClass($event, isset($handlerData['params']) ? $handlerData['params'] : array());
                $handler->run();
            }catch (\CException $e) {
                $event->addHandleError($e->getMessage());
            }
        }
        return $this;
    }

    public function attachHandler($eventClass, $handlerClass, $handlerParams) {
        $handleData = array(
            'class' => $handlerClass,
            'params' => $handlerParams
        );
        $this->events[$eventClass][] = $handleData;
        return $this;
    }

    public function detachHandler($handlerClass, $eventClass = null) {
        if(!$eventClass) {
            foreach($this->events as $eventClass => $handleData) {
                $this->_detach($handlerClass, $eventClass);
            }
            return $this;
        }
        return $this->_detach($handlerClass, $eventClass);
    }

    private function _getHandlersData($eventClass) {
        if (!$handleData = $this->events[$eventClass]) {
            throw new \CException("Event `$eventClass` not registered");
        }
        return $handleData;
    }

    /**
     * @param $handlerClass
     * @param $eventClass
     * @return $this
     * @throws \CException
     */
    private function _detach($handlerClass, $eventClass) {
        $handlersData = $this->_getHandlersData($eventClass);
        foreach ($handlersData as $key => $handlerData) {
            if ($handlerClass != $handlerData['class']) {
                continue;
            }
            unset($this->events[$eventClass][$key]);
            return $this;
        }
        return $this;
    }
}
<?php

namespace WS\EventDispatcher;


class EventDispatcher {

    /**
     * EventDispatcher
     * events => array(
     *      SomeEvent::className() => array(
     *              'class' => SomeHandler::className(),
     *              'params' => array()
     *          ),
     *          ...
     * )
     * @var array events
     */
    public $events = array();

    public function fire(Event $event) {
        $eventClass = get_class($event);
        if (! $handleData = $this->events[$eventClass]) {
            throw new \CException("Event `$eventClass` not registered");
        }
        if(!$event->validate()) {
            throw new \CException("Event `$eventClass` is not valid");
        }

        foreach ($handleData as $handlerData) {
            $handlerClass = $handlerData['class'];
            if (!is_subclass_of($handlerClass, Handler::className())) {
                throw new \CException("Handler `$handlerClass` not subclass of ".Handler::className());
            }
            try {
                /** @var $handler Handler */
                $handler = new $handlerClass($event, isset($handleData['params']) ? $handleData['params'] : array());
                $handler->run();
            }catch (\CException $e) {
                $event->addHandleError($e->getMessage());
            }
        }
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

    /**
     * @param $handlerClass
     * @param $eventClass
     * @return $this
     * @throws \CException
     */
    private function _detach($handlerClass, $eventClass) {
        if (!$handleData = $this->events[$eventClass]) {
            throw new \CException("Event `$eventClass` not registered");
        }
        foreach ($handleData as $key => $handlerData) {
            if ($handlerClass != $handlerData['class']) {
                continue;
            }
            unset($this->events[$eventClass][$key]);
            return $this;
        }
    }
}
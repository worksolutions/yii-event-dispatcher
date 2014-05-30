<?php

use WS\EventDispatcher\Event;
use WS\EventDispatcher\EventDispatcher;
use WS\EventDispatcher\Handler;

class EventDispatcherTest extends PHPUnit_Framework_TestCase {

    public function testAttachDetachHandler() {
        $dispatcher = new EventDispatcher();
        $this->assertArrayNotHasKey(TestEvent::className(), $dispatcher->events);
        $dispatcher->attachHandler(TestEvent::className(), TestHandler::className(), array(
            'testParam' => 1
        ));
        $this->assertArrayHasKey(TestEvent::className(), $dispatcher->events);
        $this->assertEquals(1, count($dispatcher->events[TestEvent::className()]));

        $dispatcher->detachHandler(TestHandler::className(), TestEvent::className());
        $this->assertEquals(0, count($dispatcher->events[TestEvent::className()]));
    }

    public function testCallEvent() {
        $that = $this;
        $useHandler = false;
        $eventTestParams = array('eventTestParam' => 1);
        $handlerTestParam = array('handlerTestParam' => 1);
        TestHandler::setProcessCallback(function (Event $event, $params) use ($that, $eventTestParams, $handlerTestParam, & $useHandler) {
            $that->assertInstanceOf(TestEvent::className(), $event);
            $that->assertEquals($eventTestParams, $event->getParams());
            $that->assertEquals($handlerTestParam, $params);
            $useHandler = true;
        });
        $dispatcher = new EventDispatcher();
        $dispatcher->attachHandler(TestEvent::className(), TestHandler::className(), $handlerTestParam);
        $this->assertFalse($useHandler);
        /** @var TestEvent $event */
        $event = $dispatcher->createEvent(TestEvent::className(), $eventTestParams);
        $dispatcher->fire($event);
        $this->assertTrue($useHandler);
    }
}


class TestEvent extends Event {

    public function attributeNames() {

    }
}

class TestHandler extends Handler {
    static $processCallback;

    protected function process() {
        if (!is_callable(static::$processCallback)) {
            throw new \CException('Don`t callable process');
        }
        call_user_func(static::$processCallback, $this->getEvent(), $this->getParams());
    }

    static public function setProcessCallback($f) {
        static::$processCallback = $f;
    }
}
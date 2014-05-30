<?php

use WS\EventDispatcher\Event;
use WS\EventDispatcher\EventDispatcher;
use WS\EventDispatcher\Handler;

class EventDispatcherTest extends PHPUnit_Framework_TestCase {

    public function testAttachDetachHandler() {
        $dispatcher = new EventDispatcher();
        $this->assertEquals(0, count($dispatcher->events));
        $dispatcher->attachHandler(TestEvent::className(), TestHandler::className(), array(
            'testParam' => 1
        ));
        $this->assertEquals(1, count($dispatcher->events));

        $dispatcher->detachHandler(TestHandler::className(), TestEvent::className());
        $this->assertEquals(0, count($dispatcher->events));
    }

    public function testCallEvent() {
        $that = $this;
        $useHandler = false;
        TestHandler::setProcessCallback(function (Event $event, array $params) use ($that, & $useHandler) {
            $that->assertInstanceOf(TestEvent::className(), $event);
            $that->assertEquals(array('testParam' => 1), $params);
            $useHandler = true;
        });

        $dispatcher = new EventDispatcher();
        $dispatcher->attachHandler(TestEvent::className(), TestHandler::className(), array(
            'testParam' => 1
        ));

        $this->assertFalse($useHandler);

        /** @var TestEvent $event */
        $event = $dispatcher->createEvent(TestEvent::className());
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
<?php

use WS\EventDispatcher\Event;
use WS\EventDispatcher\EventDispatcher;
use WS\EventDispatcher\Handler;

class EventDispatcherTest extends PHPUnit_Framework_TestCase {

    public function testAttachDetachHandler() {
        $dispatcher = new EventDispatcher();
        $this->assertEmpty($dispatcher->events);
        $dispatcher->attachHandler(TestEvent::className(), TestHandler::className(), array(
            'testParam' => 1
        ));
        $this->assertEquals(1, count($dispatcher->events));

        $dispatcher->detachHandler(TestHandler::className(), TestEvent::className());
        $this->assertEmpty(count($dispatcher->events));
    }
}


class TestEvent extends Event {

    public function attributeNames() {

    }
}

class TestHandler extends Handler {

    protected function process() {

    }
}
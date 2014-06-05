yii-ws-event-dispatcher
=======================
[![Travis CI](https://travis-ci.org/worksolutions/yii-ws-event-dispatcher.png "Travis CI")](https://travis-ci.org/worksolutions/yii-ws-event-dispatcher)
[![Coverage Status](https://coveralls.io/repos/worksolutions/yii-ws-event-dispatcher/badge.png?branch=master)](https://coveralls.io/r/worksolutions/yii-ws-event-dispatcher?branch=master)

EventDispatcher component a simple and effective make your projects truly extensible.

Installation
------------
Add a dependency to your project's composer.json:
```json
{
    "require": {
        "worksolutions/yii-ws-event-dispatcher": "dev-master"
    }
}
```

Usage examples
--------------
#### Event call
```php
$dispatcher = Yii::app()->eventDispatcher;

/** @var SomeEvent $event */
$event = $dispatcher->createEvent(SomeEvent::className(), $eventTestParams);
$dispatcher->fire($event);
```

#### Config EventDispatcher component
```php
'components' => array(
    'eventDispatcher' => array(
        'class' => \WS\EventDispatcher\EventDispatcher::className(),
        'events' => array(
            SomeEvent::className() => array(
                array(
                     'class' => SomeHandler::className(),
                     'params' => array(),
                ),
                //...
            ),
            //...
        ),
    ),
    //...
)
```

#### Create handler class
```php
use WS\EventDispatcher\Handler;

class SomeHandler extends Handler {
    protected function identity() {
        // check the availability of execution
        return true;
    }

    protected function process() {
        // you handler code
    }
}
```

#### Create event class
```php
use WS\EventDispatcher\Event;

class SomeEvent extends Event {
    public function attributeNames() {
        return array(
            'fieldName',
            //...
        );
    }

    public function rules() {
        return array(
            //validation rules
        );
    }
}
```

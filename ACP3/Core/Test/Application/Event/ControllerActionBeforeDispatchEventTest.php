<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Test\Application\Event;

use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;

class ControllerActionBeforeDispatchEventTest extends \PHPUnit_Framework_TestCase
{
    public function testExpectedRouteParts()
    {
        $serviceId = 'users.controller.frontend.account.edit';

        $event = new ControllerActionBeforeDispatchEvent($serviceId);

        $this->assertEquals('users', $event->getControllerModule());
        $this->assertEquals('frontend', $event->getControllerArea());
        $this->assertEquals('edit', $event->getControllerAction());
    }
}
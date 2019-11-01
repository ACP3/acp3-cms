<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Application\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ControllerActionBeforeDispatchEvent extends Event
{
    public const NAME = 'core.application.controller_action_dispatcher.before_dispatch';

    /**
     * @var string
     */
    private $controllerServiceId;
    /**
     * @var string[]
     */
    private $serviceIdParts = [];

    /**
     * FrontControllerDispatchEvent constructor.
     *
     * @param string $controllerServiceId
     */
    public function __construct(string $controllerServiceId)
    {
        $this->controllerServiceId = $controllerServiceId;

        $this->splitServiceIdIntoParts();
    }

    protected function splitServiceIdIntoParts()
    {
        $this->serviceIdParts = \explode('.', $this->controllerServiceId);
    }

    /**
     * @return string
     */
    public function getControllerServiceId()
    {
        return $this->controllerServiceId;
    }

    /**
     * @return string
     *
     * @deprecated since 4.28.0, to be removed with version 5.0.0. Use ::getArea instead
     */
    public function getControllerArea()
    {
        return isset($this->serviceIdParts[2]) ? $this->serviceIdParts[2] : '';
    }

    /**
     * @return string
     */
    public function getArea()
    {
        return isset($this->serviceIdParts[2]) ? $this->serviceIdParts[2] : '';
    }

    /**
     * @return string
     *
     * @deprecated since 4.28.0, to be removed with version 5.0.0. Use ::getModule instead
     */
    public function getControllerModule()
    {
        return isset($this->serviceIdParts[0]) ? $this->serviceIdParts[0] : '';
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return isset($this->serviceIdParts[0]) ? $this->serviceIdParts[0] : '';
    }

    /**
     * @return string
     */
    public function getController()
    {
        return isset($this->serviceIdParts[3]) ? $this->serviceIdParts[3] : '';
    }

    /**
     * @return string
     */
    public function getControllerAction()
    {
        return isset($this->serviceIdParts[4]) ? $this->serviceIdParts[4] : '';
    }
}
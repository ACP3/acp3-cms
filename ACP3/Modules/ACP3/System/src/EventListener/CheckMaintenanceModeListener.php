<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\EventListener;

use ACP3\Core\Application\Event\ControllerActionRequestEvent;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceModeListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\View
     */
    private $view;

    public function __construct(
        ApplicationPath $appPath,
        SettingsInterface $settings,
        View $view)
    {
        $this->appPath = $appPath;
        $this->settings = $settings;
        $this->view = $view;
    }

    public function __invoke(ControllerActionRequestEvent $event)
    {
        $request = $event->getRequest();

        if ($this->canShowMaintenanceMessage($request)) {
            $this->renderMaintenanceMessage($event);
        }
    }

    /**
     * Checks, whether the maintenance mode is active.
     */
    private function canShowMaintenanceMessage(RequestInterface $request): bool
    {
        return (bool) $this->settings->getSettings('system')['maintenance_mode'] === true &&
            \in_array($request->getArea(), [AreaEnum::AREA_ADMIN, AreaEnum::AREA_WIDGET], true) === false &&
            strpos($request->getQuery(), 'users/index/login/') !== 0;
    }

    private function renderMaintenanceMessage(ControllerActionRequestEvent $event): void
    {
        $this->view->assign([
            'ROOT_DIR' => $this->appPath->getWebRoot(),
            'CONTENT' => $this->settings->getSettings('system')['maintenance_message'],
        ]);

        $event->setResponse(new Response($this->view->fetchTemplate('System/layout.maintenance.tpl'), Response::HTTP_SERVICE_UNAVAILABLE));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerActionRequestEvent::NAME => '__invoke',
        ];
    }
}
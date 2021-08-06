<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newscomments\EventListener;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Comments\Installer\Schema as CommentsSchema;
use ACP3\Modules\ACP3\Newscomments\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnNewsLayoutSettingsEventListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;

    public function __construct(Modules $modules, View $view, SettingsInterface $settings, Forms $formsHelper)
    {
        $this->view = $view;
        $this->formsHelper = $formsHelper;
        $this->settings = $settings;
        $this->modules = $modules;
    }

    public function __invoke(TemplateEvent $event): void
    {
        if (!$this->modules->isInstalled(CommentsSchema::MODULE_NAME) || !$this->modules->isInstalled(Schema::MODULE_NAME)) {
            return;
        }

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $this->view->assign(
            'comments',
            $this->formsHelper->yesNoCheckboxGenerator('comments', $settings['comments'])
        );

        $event->addContent($this->view->fetchTemplate('Newscomments/Partials/news_layout_settings.tpl'));
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'news.layout.settings' => '__invoke',
        ];
    }
}

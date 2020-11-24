<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Cookieconsent\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Modules\ACP3\Cookieconsent\ViewProviders\AdminSettingsViewProvider;

class Settings extends AbstractFrontendAction implements InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Cookieconsent\ViewProviders\AdminSettingsViewProvider
     */
    private $adminSettingsViewProvider;

    public function __construct(AdminSettingsViewProvider $adminSettingsViewProvider, FrontendContext $context)
    {
        parent::__construct($context);

        $this->adminSettingsViewProvider = $adminSettingsViewProvider;
    }

    public function __invoke(): array
    {
        return ($this->adminSettingsViewProvider)();
    }
}
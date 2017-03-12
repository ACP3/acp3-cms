<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Event\Listener;

use ACP3\Core\ACL;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\System\Installer\Schema;

class OnLayoutContentBeforeListener
{
    /**
     * @var ACL
     */
    private $acl;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var View
     */
    private $view;

    /**
     * OnLayoutContentBeforeListener constructor.
     * @param ACL $acl
     * @param SettingsInterface $settings
     * @param View $view
     */
    public function __construct(ACL $acl, SettingsInterface $settings, View $view)
    {
        $this->acl = $acl;
        $this->settings = $settings;
        $this->view = $view;
    }

    public function renderInvalidPageCacheAlert()
    {
        $systemSettings = $this->settings->getSettings(Schema::MODULE_NAME);
        if ($this->acl->hasPermission('admin/system/maintenance/cache') && $systemSettings['page_cache_is_valid'] == 0) {
            $this->view->displayTemplate('System/Partials/alert_invalid_page_cache.tpl');
        }
    }
}

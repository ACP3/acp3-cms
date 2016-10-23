<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Maintenance;

use ACP3\Core;
use ACP3\Modules\ACP3\System;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\System\Controller\Admin\Maintenance
 */
class Cache extends Core\Controller\AbstractAdminAction
{
    /**
     * @param string $action
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute($action = '')
    {
        if (!empty($action)) {
            return $this->executePurge($action);
        }

        return [
            'cache_types' => [
                'general',
                'images',
                'minify',
                'page',
                'templates'
            ]
        ];
    }

    /**
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePurge($action)
    {
        $cacheTypes = [
            'general' => [
                $this->appPath->getCacheDir() . 'http',
                $this->appPath->getCacheDir() . 'sql',
            ],
            'images' => $this->appPath->getCacheDir() . 'images',
            'minify' => $this->appPath->getUploadsDir() . 'assets',
            'page' => $this->appPath->getCacheDir() . 'http',
            'templates' => [
                $this->appPath->getCacheDir() . 'tpl_compiled',
                $this->appPath->getCacheDir() . 'tpl_cached'
            ]
        ];

        $result = false;
        switch ($action) {
            case 'general':
            case 'images':
            case 'minify':
            case 'page':
            case 'templates':
                $result = Core\Cache\Purge::doPurge($cacheTypes[$action]);
                $text = $this->translator->t(
                    'system',
                    $result === true
                        ? 'cache_type_' . $action . '_delete_success'
                        : 'cache_type_' . $action . '_delete_error'
                );

                if ($action === 'page') {
                    $this->config->saveSettings(
                        ['page_cache_is_valid' => true],
                        System\Installer\Schema::MODULE_NAME
                    );
                }
                break;
            default:
                $text = $this->translator->t('system', 'cache_type_not_found');
        }

        return $this->redirectMessages()->setMessage($result, $text, 'acp/system/maintenance/cache');
    }
}

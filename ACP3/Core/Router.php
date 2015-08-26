<?php

namespace ACP3\Core;

use ACP3\Core\Enum\Environment;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router\Aliases;
use ACP3\Modules\ACP3\System;

/**
 * Class Router
 * @package ACP3\Core
 */
class Router
{
    const ADMIN_PANEL_PATTERN = '=^acp/=';

    /**
     * @var \ACP3\Core\Router\Aliases
     */
    protected $aliases;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var string
     */
    protected $environment;

    /**
     * @param \ACP3\Core\Router\Aliases        $aliases
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Config                $config
     * @param string                           $environment
     */
    public function __construct(
        Aliases $aliases,
        RequestInterface $request,
        Config $config,
        $environment
    )
    {
        $this->aliases = $aliases;
        $this->request = $request;
        $this->config = $config;
        $this->environment = $environment;
    }

    /**
     * Generates the internal ACP3 hyperlinks
     *
     * @param string $path
     * @param bool   $isAbsolute
     * @param bool   $forceSecure
     *
     * @return string
     */
    public function route($path, $isAbsolute = false, $forceSecure = false)
    {
        if ($path !== '') {
            $path = $this->preparePath($path);

            if ($this->isAdminUri($path) === false) {
                $alias = $this->aliases->getUriAlias($path);
                $path = $alias . (!preg_match('/\/$/', $alias) ? '/' : '');
            }
        }

        return $this->addUriPrefix($path, $isAbsolute, $forceSecure) . $path;
    }

    /**
     * @param string $path
     * @param bool   $isAbsolute
     * @param bool   $forceSecure
     *
     * @return string
     */
    protected function addUriPrefix($path, $isAbsolute, $forceSecure)
    {
        $prefix = '';
        // Append the current hostname to the URL
        if ($isAbsolute === true) {
            $prefix .= ($forceSecure === true) ? 'https://' : $this->request->getProtocol();
            $prefix .= $this->request->getHostname();
        }

        $prefix .= $this->useModRewrite($path) ? ROOT_DIR : PHP_SELF . '/';

        return $prefix;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function addControllerAndAction($path)
    {
        $pathArray = preg_split('=/=', $path, -1, PREG_SPLIT_NO_EMPTY);
        $indexes = ($this->isAdminUri($path) === true) ? [2, 3] : [1, 2];

        foreach ($indexes as $index) {
            if (isset($pathArray[$index]) === false) {
                $path .= 'index/';
            }
        }

        return $path;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function isAdminUri($path)
    {
        return preg_match(self::ADMIN_PANEL_PATTERN, $path) === true;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function preparePath($path)
    {
        $path = $path . (!preg_match('/\/$/', $path) ? '/' : '');
        if ($path === 'acp/') {
            $path = 'acp/acp/index/index/';
        }

        return $this->addControllerAndAction($path);
    }

    /**
     * Check, whether to use urls with mod_rewrite or not
     *
     * @param string $path
     *
     * @return bool
     */
    protected function useModRewrite($path)
    {
        return $this->environment === Environment::PRODUCTION &&
        (bool)$this->config->getSettings('seo')['mod_rewrite'] === true &&
        $this->isAdminUri($path) === false;
    }
}

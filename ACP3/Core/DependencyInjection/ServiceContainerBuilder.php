<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\DependencyInjection;

use ACP3\Core\Authentication\DependencyInjection\RegisterAuthenticationsCompilerPass;
use ACP3\Core\Controller\DependencyInjection\RegisterControllerActionsPass;
use ACP3\Core\DataGrid\DependencyInjection\RegisterColumnRendererPass;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Installer\DependencyInjection\RegisterInstallersCompilerPass;
use ACP3\Core\Model\DataProcessor\DependencyInjection\RegisterColumnTypesCompilerPass;
use ACP3\Core\Modules;
use ACP3\Core\Validation\DependencyInjection\RegisterValidationRulesPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterLegacySmartyPluginsPass;
use ACP3\Core\View\Renderer\Smarty\DependencyInjection\RegisterSmartyPluginsPass;
use ACP3\Core\WYSIWYG\DependencyInjection\RegisterWysiwygEditorsCompilerPass;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class ServiceContainerBuilder extends ContainerBuilder
{
    /**
     * @var ApplicationPath
     */
    private $applicationPath;
    /**
     * @var SymfonyRequest
     */
    private $symfonyRequest;

    /**
     * ServiceContainerBuilder constructor.
     *
     * @param ApplicationPath $applicationPath
     * @param SymfonyRequest  $symfonyRequest
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function __construct(
        ApplicationPath $applicationPath,
        SymfonyRequest $symfonyRequest
    ) {
        parent::__construct();

        $this->applicationPath = $applicationPath;
        $this->symfonyRequest = $symfonyRequest;

        $this->setUpContainer();
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     * @throws \Exception
     */
    private function setUpContainer(): void
    {
        $this->setProxyInstantiator(new RuntimeInstantiator());

        $this->set('core.http.symfony_request', $this->symfonyRequest);
        $this->set('core.environment.application_path', $this->applicationPath);

        $this
            ->addCompilerPass(
                new RegisterListenersPass(
                    'core.event_dispatcher',
                    'core.eventListener',
                    'core.eventSubscriber'
                )
            )
            ->addCompilerPass(new RegisterAuthenticationsCompilerPass())
            ->addCompilerPass(new RegisterSmartyPluginsPass())
            ->addCompilerPass(new RegisterLegacySmartyPluginsPass())
            ->addCompilerPass(new RegisterColumnRendererPass())
            ->addCompilerPass(new \ACP3\Core\Helpers\DataGrid\DependencyInjection\RegisterColumnRendererPass())
            ->addCompilerPass(new RegisterValidationRulesPass())
            ->addCompilerPass(new RegisterWysiwygEditorsCompilerPass())
            ->addCompilerPass(new RegisterControllerActionsPass())
            ->addCompilerPass(new RegisterInstallersCompilerPass())
            ->addCompilerPass(new RegisterColumnTypesCompilerPass());

        $loader = new YamlFileLoader($this, new FileLocator(__DIR__));
        $loader->load($this->applicationPath->getAppDir() . 'config.yml');
        $loader->load($this->applicationPath->getModulesDir() . 'ACP3/System/Resources/config/services.yml');
        $loader->load(\dirname(__DIR__) . '/config/services.yml');

        // Try to get all available services
        /** @var Modules $modules */
        $modules = $this->get('core.modules');

        foreach ($modules->getAllModulesTopSorted() as $module) {
            $modulePath = $this->applicationPath->getModulesDir() . $module['vendor'] . '/' . $module['dir'];
            $path = $modulePath . '/Resources/config/services.yml';

            if (\is_file($path)) {
                $loader->load($path);
            }

            $this->registerCompilerPass($module['vendor'], $module['dir']);
        }

        $this->compile();
    }

    /**
     * @param \ACP3\Core\Environment\ApplicationPath $applicationPath
     * @param SymfonyRequest                         $symfonyRequest
     *
     * @return \ACP3\Core\DependencyInjection\ServiceContainerBuilder
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public static function create(
        ApplicationPath $applicationPath, SymfonyRequest $symfonyRequest
    ): ServiceContainerBuilder {
        return new static($applicationPath, $symfonyRequest);
    }

    /**
     * @param string $vendor
     * @param string $moduleName
     */
    private function registerCompilerPass(string $vendor, string $moduleName): void
    {
        $fqn = '\\ACP3\\Modules\\' . $vendor . '\\' . $moduleName . '\\ModuleRegistration';

        if (\class_exists($fqn)) {
            $instance = new $fqn();

            if ($instance instanceof Modules\ModuleRegistration) {
                $instance->build($this);
            }
        }
    }
}

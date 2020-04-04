<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CaptchaCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('captcha.utility.captcha_registrar');
        $plugins = $container->findTaggedServiceIds('captcha.extension.captcha');

        foreach ($plugins as $serviceId => $tags) {
            $definition->addMethodCall(
                'registerCaptcha',
                [$serviceId, new Reference($serviceId)]
            );
        }
    }
}
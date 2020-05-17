<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

use ACP3\Core\Component\ComponentRegistry;
use ACP3\Core\Component\ComponentTypeEnum;
use ACP3\Core\Component\Dto\ComponentDataDto;

ComponentRegistry::add(
    new ComponentDataDto(
        ComponentTypeEnum::MODULE,
        'newscomments',
        __DIR__,
        ['comments', 'core', 'news', 'system']
    )
);
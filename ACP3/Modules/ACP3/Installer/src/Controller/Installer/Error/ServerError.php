<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Controller\Installer\Error;

use ACP3\Modules\ACP3\Installer\Core\Controller\AbstractInstallerAction;
use Symfony\Component\HttpFoundation\Response;

class ServerError extends AbstractInstallerAction
{
    public function execute(): void
    {
        $this->response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
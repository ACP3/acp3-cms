<?php

namespace ACP3\Installer\Modules\Errors\Controller;

use ACP3\Installer\Core\Modules\AbstractInstallerController;

/**
 * Class Index
 * @package ACP3\Installer\Modules\Errors\Controller
 */
class Index extends AbstractInstallerController
{
    public function action404()
    {
        header('HTTP/1.0 404 not found');
    }

    public function action500()
    {
        header('HTTP/1.0 500 Internal Server Error');
    }
}

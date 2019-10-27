<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Modules\Install\Controller\Index;

use ACP3\Installer\Core;
use ACP3\Installer\Modules\Install\Controller\AbstractAction;
use ACP3\Installer\Modules\Install\Helpers\Navigation;
use ACP3\Installer\Modules\Install\Helpers\Requirements as RequirementsHelper;

class Requirements extends AbstractAction
{
    /**
     * @var \ACP3\Installer\Modules\Install\Helpers\Requirements
     */
    protected $requirementsHelpers;

    public function __construct(
        Core\Controller\Context\InstallerContext $context,
        Navigation $navigation,
        RequirementsHelper $requirementsHelpers
    ) {
        parent::__construct($context, $navigation);

        $this->requirementsHelpers = $requirementsHelpers;
    }

    public function execute(): array
    {
        [$requirements, $stopInstall] = $this->requirementsHelpers->checkMandatoryRequirements();
        [$requiredFilesAndDirs, $checkAgain] = $this->requirementsHelpers->checkFolderAndFilePermissions();

        return [
            'requirements' => $requirements,
            'files_dirs' => $requiredFilesAndDirs,
            'php_settings' => $this->requirementsHelpers->checkOptionalRequirements(),
            'stop_install' => $stopInstall,
            'check_again' => $checkAgain,
        ];
    }
}

<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Account;

use ACP3\Core\Authentication\Exception\UnauthorizedAccessException;
use ACP3\Core\Controller\AbstractFormAction;

abstract class AbstractAction extends AbstractFormAction
{
    /**
     * @return \ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index\AbstractFormAction|void
     *
     * @throws \ACP3\Core\Authentication\Exception\UnauthorizedAccessException
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if ($this->user->isAuthenticated() === false) {
            throw new UnauthorizedAccessException(['redirect' => \base64_encode($this->request->getPathInfo())]);
        }
    }
}
